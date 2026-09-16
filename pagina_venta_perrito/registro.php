<?php
include 'conexion.php';
session_start();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        $check_sql = "SELECT id FROM usuarios WHERE email = ?";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->execute([$email]);

        if ($check_stmt->rowCount() > 0) {
            $mensaje = "El correo electrónico ya está registrado.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, email, password, telefono, direccion, rol) VALUES (?, ?, ?, ?, ?, 'cliente')";
            $stmt = $conexion->prepare($sql);
            
            if ($stmt->execute([$nombre, $email, $password_hashed, $telefono, $direccion])) {
                header("Location: login.php?registro=exitoso");
                exit();
            } else {
                $mensaje = "Hubo un error al registrar el usuario.";
            }
        }
    } else {
        $mensaje = "Por favor, completa los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tienda Perrito</title>
    <link rel="stylesheet" href="css/style_auth.css">
    <style>
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #777;
            padding: 0;
        }
        .toggle-password:hover {
            color: #ff7f00;
        }
    </style>
</head>
<body>
    <div class="container-auth">
        <h2>Crear una Cuenta</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="error-msg"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                </div>
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono">
            </div>
            <div class="form-group">
                <label>Dirección de Envío:</label>
                <textarea name="direccion"></textarea>
            </div>
            <button type="submit" class="btn-submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>