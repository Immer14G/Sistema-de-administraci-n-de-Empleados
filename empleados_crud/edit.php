<?php
include './config/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener la información del empleado
    $sql = "SELECT e.id, e.nombre, e.correo, e.departamento_id, e.rol_id, s.salario 
            FROM empleados e
            LEFT JOIN salarios s ON e.id = s.empleado_id
            WHERE e.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
    } else {
        echo "Empleado no encontrado";
        exit();
    }


    // Obtener los beneficios asignados
    $sql_beneficios = "SELECT b.id, b.nombre 
                       FROM beneficios b 
                       JOIN beneficios_empleado be ON b.id = be.beneficio_id 
                       WHERE be.empleado_id = ?";
    $stmt_beneficios = $conn->prepare($sql_beneficios);
    $stmt_beneficios->bind_param("i", $id);
    $stmt_beneficios->execute();
    $result_beneficios = $stmt_beneficios->get_result();

    // Obtener los roles disponibles
    $sql_roles = "SELECT id, nombre FROM roles";
    $result_roles = $conn->query($sql_roles);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Editar Empleado</h1>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($empleado['id']); ?>">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo:</label>
            <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($empleado['correo']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="salario" class="form-label">Salario:</label>
            <input type="number" class="form-control" name="salario" value="<?php echo htmlspecialchars($empleado['salario']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="departamento_id" class="form-label">Departamento:</label>
            <select name="departamento_id" class="form-select" required>
                <?php
                // Consulta para obtener los departamentos
                $sql_departamentos = "SELECT id, nombre FROM departamentos";
                $result_departamentos = $conn->query($sql_departamentos);

                // Asegúrate de mostrar la opción correspondiente
                while ($row = $result_departamentos->fetch_assoc()) {
                    $selected = ($empleado['departamento_id'] == $row['id']) ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo de selección de rol -->
        <div class="mb-3">
            <label for="rol_id" class="form-label">Rol:</label>
            <select name="rol_id" class="form-select" required>
                <?php
                // Mostrar los roles disponibles y seleccionar el rol actual del empleado
                while ($row = $result_roles->fetch_assoc()) {
                    $selected = ($empleado['rol_id'] == $row['id']) ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['nombre']}</option>";
                }
                ?>
    
        </div>
        <div class="mb-3">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
