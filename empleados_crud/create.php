<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* Fondo claro y estilizado */
        body {
            background-color: #f4f7fa;
            color: #495057;
        }

        .container {
            max-width: 800px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 30px;
        }

        .alert {
            border-radius: 5px;
            font-weight: bold;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control, .form-select {
            border-radius: 5px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .btn {
            font-weight: bold;
            padding: 12px;
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        /* Estilo para los campos de selección múltiple */
        select[multiple] {
            height: auto;
        }
    </style>
    
</head>
<body>
<div class="container mt-5">
    <h1>Agregar Empleado</h1>

    <!-- Mostrar Mensajes de Error o Éxito -->
    <?php
    if (isset($_GET['mensaje'])) {
        echo "<div class='alert alert-success'>{$_GET['mensaje']}</div>";
    } elseif (isset($_GET['error'])) {
        echo "<div class='alert alert-danger'>{$_GET['error']}</div>";
    }
    ?>

    <form action="store.php" method="POST">
        <!-- Campo Nombre -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" id="nombre" required>
        </div>

        <!-- Campo Correo -->
        <div class="mb-3">
            <label for="correo" class="form-label">Correo:</label>
            <input type="email" class="form-control" name="correo" id="correo" required>
        </div>

        <!-- Campo Fecha de Contratación -->
        <div class="mb-3">
            <label for="fecha_contratacion" class="form-label">Fecha de Contratación:</label>
            <input type="date" class="form-control" name="fecha_contratacion" id="fecha_contratacion" required>
        </div>

        <!-- Campo Profesión -->
        <div class="mb-3">
            <label for="profesion" class="form-label">Profesión:</label>
            <input type="text" class="form-control" name="profesion" id="profesion" required>
        </div>

        <!-- Campo Departamento -->
        <div class="mb-3">
            <label for="departamento_id" class="form-label">Departamento:</label>
            <select name="departamento_id" id="departamento_id" class="form-select" required>
                <?php
                include './config/db.php';
                if ($conn) {
                    $sql = "SELECT id, nombre FROM departamentos";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay departamentos disponibles</option>";
                    }
                } else {
                    echo "<option value=''>Error de conexión a la base de datos</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo Rol -->
        <div class="mb-3">
            <label for="rol_id" class="form-label">Rol:</label>
            <select name="rol_id" id="rol_id" class="form-select" required>
                <?php
                if ($conn) {
                    $sql = "SELECT id, nombre FROM roles";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay roles disponibles</option>";
                    }
                } else {
                    echo "<option value=''>Error de conexión a la base de datos</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo Salario -->
        <div class="mb-3">
            <label for="salario" class="form-label">Salario:</label>
            <input type="text" class="form-control" name="salario" id="salario" pattern="^\d+(\.\d{1,2})?$" 
                   title="El salario debe ser un número válido con hasta dos decimales." required>
        </div>

        <!-- Campo Beneficios -->
        <div class="mb-3">
            <label for="beneficios" class="form-label">Beneficios:</label>
            <select name="beneficios[]" id="beneficios" class="form-select" multiple>
                <?php
                if ($conn) {
                    $sql = "SELECT id, nombre FROM beneficios";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay beneficios disponibles</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
