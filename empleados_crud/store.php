<?php
// Incluir la conexión a la base de datos
include './config/db.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se recibió una solicitud POST con los datos del empleado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $fecha_contratacion = $_POST['fecha_contratacion'];
    $profesion = $_POST['profesion'];
    $departamento_id = $_POST['departamento_id'];
    $rol_id = $_POST['rol_id'];
    $salario = $_POST['salario'];

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Insertar el nuevo empleado
        $stmt = $conn->prepare("INSERT INTO empleados (nombre, correo, fecha_contratacion, profesion, departamento_id, rol_id, salario) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiii", $nombre, $correo, $fecha_contratacion, $profesion, $departamento_id, $rol_id, $salario);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al agregar el empleado.");
        }

        // Obtener el ID del nuevo empleado insertado
        $empleado_id = $conn->insert_id;

        // Insertar los beneficios relacionados (si existen)
        if (!empty($_POST['beneficios'])) {
            foreach ($_POST['beneficios'] as $beneficio_id) {
                $stmt = $conn->prepare("INSERT INTO beneficios_empleado (empleado_id, beneficio_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $empleado_id, $beneficio_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error al agregar los beneficios del empleado.");
                }
            }
        }

        // Insertar los proyectos relacionados (si existen)
        if (!empty($_POST['proyectos'])) {
            foreach ($_POST['proyectos'] as $proyecto_id) {
                $stmt = $conn->prepare("INSERT INTO proyectos_empleado (empleado_id, proyecto_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $empleado_id, $proyecto_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error al agregar los proyectos del empleado.");
                }
            }
        }

        // Confirmar la transacción
        $conn->commit();

        // Redirigir con mensaje de éxito
        header("Location: index.php?mensaje=" . urlencode("Empleado agregado correctamente"));
        exit; // Asegurarse de que no continúe la ejecución después de la redirección
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollback();

        // Redirigir con mensaje de error
        header("Location: index.php?error=" . urlencode($e->getMessage()));
        exit; // Asegurarse de que no continúe la ejecución después de la redirección
    }
} else {
    // Si no se recibió la solicitud POST
    header("Location: index.php?error=" . urlencode("Solicitud no válida"));
    exit;
}
?>
