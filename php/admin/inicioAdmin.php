<?php
session_start();
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    // Usuario y clave hardcodeados
    if ($usuario === 'admin' && $clave === 'admin') {
        $_SESSION['admin'] = true;
        header('Location: panelAdmin.php');
        exit;
    } else {
        $mensaje = 'Usuario o clave incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión Admin</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/inicioAdmin.css">
</head>
<body>
    <div class="caja-inicio">
        <h2>Acceso Administrador</h2>
        <?php if ($mensaje): ?>
            <div class="error"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Clave" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
