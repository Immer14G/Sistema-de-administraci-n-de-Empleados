<?php
include './config/db.php';

// Verificar que se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $fecha_contratacion = $_POST['fecha_contratacion'];
    $profesion = $_POST['profesion'];
    $departamento_id = $_POST['departamento_id'];
    $rol_id = $_POST['rol_id'];
    $salario = $_POST['salario'];
    $proyectos = isset($_POST['proyectos']) ? $_POST['proyectos'] : [];
    $beneficios = isset($_POST['beneficios']) ? $_POST['beneficios'] : [];

    // Limpiar los datos para evitar inyecciones SQL
    $nombre = $conn->real_escape_string($nombre);
    $correo = $conn->real_escape_string($correo);
    $fecha_contratacion = $conn->real_escape_string($fecha_contratacion);
    $profesion = $conn->real_escape_string($profesion);
    $salario = (float)$salario; // Asegurarse que el salario es un número flotante

    // Verificar si el correo ya existe
    $sql_check_email = "SELECT id FROM empleados WHERE correo = '$correo'";
    $result = $conn->query($sql_check_email);

    if ($result->num_rows > 0) {
        // El correo ya existe, mostrar un mensaje de error
        echo "El correo electrónico '$correo' ya está registrado. Por favor, use otro.";
    } else {
        // Insertar el empleado en la base de datos
        $sql = "INSERT INTO empleados (nombre, correo, fecha_contratacion, profesion, departamento_id, rol_id, salario) 
                VALUES ('$nombre', '$correo', '$fecha_contratacion', '$profesion', $departamento_id, $rol_id, $salario)";

        if ($conn->query($sql)) {
            $empleado_id = $conn->insert_id; // Obtener el ID del empleado recién insertado

            // Insertar los proyectos asociados
            if (!empty($proyectos)) {
                foreach ($proyectos as $proyecto_id) {
                    $proyecto_id = (int)$proyecto_id; // Asegurarse que el ID es un número entero
                    $sql_proyecto = "INSERT INTO proyectos_empleado (empleado_id, proyecto_id) 
                                     VALUES ($empleado_id, $proyecto_id)";
                    if (!$conn->query($sql_proyecto)) {
                        echo "Error al agregar proyecto: " . $conn->error;
                    }
                }
            }

            // Insertar los beneficios asociados
            if (!empty($beneficios)) {
                foreach ($beneficios as $beneficio_id) {
                    $beneficio_id = (int)$beneficio_id; // Asegurarse que el ID es un número entero
                    $sql_beneficio = "INSERT INTO beneficios_empleado (empleado_id, beneficio_id) 
                                      VALUES ($empleado_id, $beneficio_id)";
                    if (!$conn->query($sql_beneficio)) {
                        echo "Error al agregar beneficio: " . $conn->error;
                    }
                }
            }

            // Redirigir al usuario con un mensaje de éxito
            header("Location: index.php?mensaje=Empleado agregado correctamente");
            exit;
        } else {
            // Mostrar mensaje de error en caso de fallo
            echo "Error al agregar el empleado: " . $conn->error;
        }
    }
}
?>
    