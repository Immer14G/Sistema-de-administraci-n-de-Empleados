<?php
include "../config/db.php"; // Incluye la conexión a la base de datos

// Consulta SQL para obtener todos los departamentos
try {
    $sql = "SELECT id, nombre FROM departamentos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Error al obtener los departamentos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Departamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        /* Fondo claro */
        body {
            background-color: #f4f7fc;
            color: #495057;
            font-family: 'Roboto', sans-serif;
        }

        /* Barra lateral */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #007bff;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .navbar-brand {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
            padding: 10px;
        }

        .sidebar .nav-link {
            color: #ffffff;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 5px;
            display: block;
            margin: 10px 0;
        }

        .sidebar .nav-link:hover {
            background-color: #0056b3;
            text-decoration: none;
        }

        .sidebar .nav-link.active {
            background-color: #004085;
        }

        /* Contenido principal */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s ease;
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        /* Estilo de la tabla */
        .table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        /* Estilo de la paginación */
        .pagination .page-link {
            color: #007bff;
            background-color: #ffffff;
            border-color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-item:hover .page-link {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>


</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar">
        <div class="navbar-brand">Empresa ING</div>
        <a href="/empleados_crud/index.php" class="nav-link">Inicio</a>
        <a href="ListaEmpleados.php" class="nav-link">Lista de Empleados</a>
        <a href="listarUsuarios.php" class="nav-link">Usuarios</a>
        <a href="/empleados_crud/logout.php" class="btn btn-danger">Cerrar Sesión</a>
       
    </div>
        
    <!-- Contenido principal -->
    <div class="main-content">
        <h2 class="text-center mb-4">Lista de Departamentos</h2>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Departamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No hay departamentos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
