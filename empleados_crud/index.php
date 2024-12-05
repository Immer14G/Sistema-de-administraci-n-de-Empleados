<?php

include './config/db.php';

// Protección con login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Variables para la paginación
$limite = 10; // Número de empleados por página
$pagina = isset($_GET['pagina']) && filter_var($_GET['pagina'], FILTER_VALIDATE_INT) && $_GET['pagina'] > 0
    ? (int)$_GET['pagina']
    : 1;
$inicio = ($pagina - 1) * $limite;

// Variable para el buscador
$search = trim($_GET['search'] ?? '');

// Si la búsqueda es muy corta, mostrar un mensaje o evitar la búsqueda
if (strlen($search) <= 2 && !empty($search)) {
    $error = "La búsqueda debe tener al menos 3 caracteres.";
    $like_search = '%';  // Hacemos que la búsqueda sea amplia si es muy corta
} else {
    $like_search = strlen($search) > 2 ? "%$search%" : '%';
}

$total_paginas = 1;

try {
    // Consulta principal con SQL ajustada a la nueva estructura de la base de datos
    $sql = "SELECT SQL_CALC_FOUND_ROWS
                e.id, 
                e.nombre, 
                e.correo, 
                e.fecha_contratacion, 
                e.profesion, 
                d.nombre AS departamento, 
                r.nombre AS rol, 
                e.salario
            FROM empleados e
            LEFT JOIN departamentos d ON e.departamento_id = d.id
            LEFT JOIN roles r ON e.rol_id = r.id
            WHERE e.nombre LIKE ? OR e.correo LIKE ? OR d.nombre LIKE ?
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $like_search, $like_search, $like_search, $inicio, $limite);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        throw new Exception("Error al obtener los resultados de la consulta.");
    }

    // Obtener el total de registros
    $total_result = $conn->query("SELECT FOUND_ROWS() AS total");
    if ($total_result) {
        $total_empleados = $total_result->fetch_assoc()['total'];
        $total_paginas = ceil($total_empleados / $limite);
    }
} catch (Exception $e) {
    $error = "Ocurrió un problema al cargar los empleados. Por favor, inténtelo más tarde.";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Empleados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   
    <style>
        /* Fondo oscuro */
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
        <a href="#" class="navbar-brand">Empresa ING</a>
        <a href="views/listaEmpleados.php" class="nav-link">Empleados</a>
        <a href="views/listarDepartamentos.php" class="nav-link">Departamentos</a>
        <a href="views/listarUsuarios.php" class="nav-link">Usuarios</a>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <h1 class="text-center">Administración de Empleados</h1>
        <div class="d-flex justify-content-between mb-3">
            <a href="create.php" class="btn btn-success">Agregar Empleado</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>

        <!-- Tabla de empleados -->
        <table class="table table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Fecha de Contratación</th>
                    <th>Profesión</th>
                    <th>Departamento</th>
                    <th>Rol</th>
                    <th>Salario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['correo']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_contratacion']) ?></td>
                            <td><?= htmlspecialchars($row['profesion'] ?? 'No asignada') ?></td>
                            <td><?= htmlspecialchars($row['departamento'] ?? 'No asignado') ?></td>
                            <td><?= htmlspecialchars($row['rol'] ?? 'No asignado') ?></td>
                            <td><?= $row['salario'] ? '$' . number_format($row['salario'], 2) : 'No asignado' ?></td>
                            <td>
                                <a href="edit.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-primary">Editar</a>
                                <form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este empleado?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay empleados registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Navegación de paginación -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $pagina == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&search=<?= htmlspecialchars($search) ?>">Anterior</a>
                </li>
                <?php
                $range = 2;
                for ($i = max(1, $pagina - $range); $i <= min($total_paginas, $pagina + $range); $i++):
                ?>
                    <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina == $total_paginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&search=<?= htmlspecialchars($search) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
