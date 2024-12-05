<?php
include './config/db.php'; // Incluye la conexión a la base de datos
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            // Validación del nombre de usuario (puedes agregar más validaciones si es necesario)
            if (strlen($username) < 4) {
                $error = "El nombre de usuario debe tener al menos 4 caracteres.";
            } else {
                // Verificar si el usuario ya existe
                $sql = "SELECT id FROM usuarios WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = "El usuario ya existe.";
                } else {
                    // Insertar nuevo usuario
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insertSql = "INSERT INTO usuarios (username, password) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("ss", $username, $hashedPassword);

                    if ($insertStmt->execute()) {
                        $success = "Usuario registrado con éxito.";
                    } else {
                        $error = "Error al registrar el usuario.";
                    }
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
   <style>
        /* Fondo oscuro */
        body {
            background-color: #f8f9fa;
            color: #495057;
        }

        /* Estilo del contenedor */
        .container {
            max-width: 400px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 5px;
            font-weight: bold;
        }

        .btn {
            font-weight: bold;
            padding: 10px;
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

        .form-control {
            border-radius: 5px;
        }

        /* Estilo del botón "Volver al Login" */
        .mt-2 {
            margin-top: 10px;
        }
    </style>
    
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Registrar Usuario</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrar</button>
        <a href="login.php" class="btn btn-secondary w-100 mt-2">Volver al Login</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
