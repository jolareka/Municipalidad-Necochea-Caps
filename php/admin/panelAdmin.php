<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: inicioAdmin.php');
    exit;
}
include "conexion.php";

// Eliminar CAPS si se envia el id por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    mysqli_query($conexion, "DELETE FROM caps WHERE id_caps = $id");
}

// Obtener todos los CAPS
$result = mysqli_query($conexion, "SELECT id_caps, nombre FROM caps");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - CAPS</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/inicioAdmin.css">
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/panelAdmin.css">
</head>
<body>
    <div class="modificar-caps">
    <h1 style="text-align:center;">Panel de Administración de CAPS</h1>
    <a class="btnagregar"href="agregar_datos.php" class="boton-agregar">Agregar</a>
    <div class="lista-caps-admin">
        <?php while($cap = mysqli_fetch_assoc($result)): ?>
            <div class="renglon-cap">
                <span class="nombre-cap"><?php echo htmlspecialchars($cap['nombre']); ?></span>
                <div class="acciones-cap">
                    <a href="modificarCap.php?id_caps=<?php echo $cap['id_caps']; ?>">Modificar</a>
                    <form method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este CAPS?');">
                        <input type="hidden" name="eliminar_id" value="<?php echo $cap['id_caps']; ?>">
                        <button type="submit" class="eliminar">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conexion); ?>
