<?php
// Incluir la conexión a la base de datos
include './config/db.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se recibió una solicitud POST y que el id sea válido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    $id = intval($_POST['id']);

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Verificar si el empleado existe antes de intentar eliminarlo
        $stmt = $conn->prepare("SELECT id FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Consultas para eliminar datos relacionados, sin tocar la tabla de proyectos
            $queries = [
                "DELETE FROM historial_cambios WHERE empleado_id = ?",
                "DELETE FROM beneficios_empleado WHERE empleado_id = ?",
                // Eliminar el empleado
                "DELETE FROM empleados WHERE id = ?"
            ];

            // Eliminar registros relacionados en las tablas de relación
            foreach ($queries as $query) {
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception("Error al eliminar datos relacionados.");
                }
            }

            // Confirmar la transacción
            $conn->commit();

            // Redirigir con mensaje de éxito
            header("Location: index.php?mensaje=" . urlencode("Empleado eliminado correctamente"));
            exit; // Asegurarse de que no continúe la ejecución después de la redirección
        } else {
            // El empleado no existe
            throw new Exception("Empleado no encontrado.");
        }
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollback();

        // Redirigir con mensaje de error
        header("Location: index.php?error=" . urlencode($e->getMessage()));
        exit; // Asegurarse de que no continúe la ejecución después de la redirección
    }
} else {
    // Si el ID no es válido o no se proporcionó
    header("Location: index.php?error=" . urlencode("ID inválido o no proporcionado"));
    exit;
}
?>
