<?php
include './config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que todos los campos estén presentes en la solicitud POST
    if (!isset($_POST['id']) || !isset($_POST['nombre']) || !isset($_POST['correo'])  || !isset($_POST['salario']) || !isset($_POST['departamento_id'])) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Validar y sanitizar entradas
    $id = (int)$_POST['id']; 
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $salario = trim($_POST['salario']);
    $departamento_id = (int)$_POST['departamento_id']; // Aseguramos que el departamento_id sea un número

    // Validamos que los campos no estén vacíos
    if (empty($nombre) || empty($correo)  || empty($salario) || empty($departamento_id)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Validar el formato del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "El correo electrónico no es válido.";
        exit;
    }

    // Preparamos la consulta para evitar inyección SQL
    $stmt = $conn->prepare("UPDATE empleados 
                            SET nombre=?, correo=?, salario=?, departamento_id=? 
                            WHERE id=?");
    $stmt->bind_param("ssiii", $nombre, $correo, $salario, $departamento_id, $id); // Corregido el bind_param

    // Intentamos ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al index.php en la raíz del proyecto
        header("Location: index.php");
        exit;
    } else {
        // Si ocurre un error, mostrar un mensaje
        echo "Error: " . $stmt->error;
    }

    // Cerramos la consulta
    $stmt->close();
}
?>
