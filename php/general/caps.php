<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Municipalidad-Necochea-Caps/php/componentes/navegador.php';
include "conexion.php";
$sql = "SELECT id_caps, nombre, descripcion, coordenadas, horario, imagen, telefono FROM caps"; //Consulta para obtener los CAPS
$result = mysqli_query($conexion, $sql); // Ejecuta la consulta

$caps_js = []; // Guardar los Caps en un array para JS
if ($result && mysqli_num_rows($result) > 0) { // Si hay resultados
    while($columna = mysqli_fetch_assoc($result)) { // Recorre los resultados
        $caps_js[] = $columna; // Agrega cada caps al array
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="\Municipalidad-Necochea-Caps\css\caps.css">
    <title>Document</title>
</head>
<body>
    <h1>Centros De Atencion Primaria de Salud</h1>
    <p class="pinicio">Lista de Centros de Salud en la localidad de Necochea</p>
    <div class="caps-list">
    <?php if (!empty($caps_js)): ?>
        <?php foreach($caps_js as $columna): ?>
            <div class="cap-item">
                <img src="<?php echo htmlspecialchars($columna['imagen']); ?>">
                <h2><?php echo htmlspecialchars($columna['nombre']); ?></h2> <!-- Mostrar nombre --> 
                <p>Descripción: <?php echo htmlspecialchars($columna['descripcion']); ?></p> <!-- Mostrar descripcion -->
                <!-- <p>Coordenadas: <?php echo htmlspecialchars($columna['coordenadas']); ?></p> Mostrar coordenadas -->
                <p>Horario: <?php echo htmlspecialchars($columna['horario']); ?></p> <!-- Mostrar horario -->
                <p>Teléfono: <?php echo htmlspecialchars($columna['telefono']); ?></p> <!-- Mostrar telefono -->
                <a class="verdetalle"href="/Municipalidad-Necochea-Caps/php/general/mostrarCap.php?id_caps=<?php echo $columna['id_caps']; ?>">Ver detalles</a> <!-- Enlace a la pagina de detalles del caps -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se encontraron caps.</p>
    <?php endif; ?>
</body>
</html>