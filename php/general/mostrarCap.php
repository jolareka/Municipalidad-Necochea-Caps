<?php
include "conexion.php";

// Obtener el id_caps desde la URL
$id_caps = isset($_GET['id_caps']) ? intval($_GET['id_caps']) : 0;
if ($id_caps <= 0) {
    echo '<p>No se ha seleccionado un CAPS válido.</p>';
    exit;
}

// Consulta para obtener la información básica del CAPS
$sql_cap = "SELECT * FROM caps WHERE id_caps = $id_caps";
$result_cap = mysqli_query($conexion, $sql_cap);
$cap = mysqli_fetch_assoc($result_cap);
if (!$cap) {
    echo '<p>CAPS no encontrado.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del CAPS</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/mostrarCap.css">
</head>
<body>
    <h1>Detalle del CAPS</h1>
    <div class="cap-item">
        <img src="../img/<?php echo htmlspecialchars($cap['imagen']); ?>" alt="Imagen del CAPS" class="cap-img">
        <h2><?php echo htmlspecialchars($cap['nombre']); ?></h2>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($cap['descripcion']); ?></p>
        <p><strong>Coordenadas:</strong> <?php echo htmlspecialchars($cap['coordenadas']); ?></p>
        <p><strong>Horario:</strong> <?php echo htmlspecialchars($cap['horario']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cap['telefono']); ?></p>

        <!-- Prestaciones -->
        <?php
        $sql_prestaciones = "SELECT p.id_prestaciones, p.nombre FROM prestaciones p INNER JOIN prestaciones_caps pc ON p.id_prestaciones = pc.id_prestaciones WHERE pc.id_caps = $id_caps";
        $result_prestaciones = mysqli_query($conexion, $sql_prestaciones);
        ?>
        <div class="prestaciones">
            <h3>Prestaciones:</h3>
            <?php if ($result_prestaciones && mysqli_num_rows($result_prestaciones) > 0): ?>
                <ul>
                <?php while($prest = mysqli_fetch_assoc($result_prestaciones)): ?>
                    <li>
                        <?php echo htmlspecialchars($prest['nombre']); ?>
                        <!-- Profesionales de la prestación -->
                        <?php
                        // Mostrar profesionales relacionados a la prestación
                        $sql_prof = "SELECT pr.nombre, pr.apellido, pp.horario_profesionales FROM profesionales pr INNER JOIN profesionales_prestaciones pp ON pr.id_profesionales = pp.id_profesionales WHERE pr.id_profesionales IN (SELECT id_profesionales FROM profesionales_prestaciones WHERE id_profesionales = pr.id_profesionales)";
                        $result_prof = mysqli_query($conexion, $sql_prof);
                        ?>
                        <ul>
                        <?php if ($result_prof && mysqli_num_rows($result_prof) > 0): ?>
                            <?php while($prof = mysqli_fetch_assoc($result_prof)): ?>
                                <li><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']); ?> (Horario: <?php echo htmlspecialchars($prof['horario_profesionales']); ?>)</li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No hay profesionales asignados.</li>
                        <?php endif; ?>
                        </ul>
                    </li>
                <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No hay prestaciones registradas.</p>
            <?php endif; ?>
        </div>

        <!-- Campañas activas -->
        <?php
        $sql_campanias = "SELECT c.imagen, cc.horario, cc.fecha_inicio, cc.fecha_fin, cc.requisitos FROM campañas_caps cc INNER JOIN campañas c ON cc.id_campañas = c.id_campañas WHERE cc.id_caps = $id_caps AND cc.fecha_fin >= CURDATE()";
        $result_campanias = mysqli_query($conexion, $sql_campanias);
        ?>
        <div class="campanias">
            <h3>Campañas activas:</h3>
            <?php if ($result_campanias && mysqli_num_rows($result_campanias) > 0): ?>
                <ul>
                <?php while($camp = mysqli_fetch_assoc($result_campanias)): ?>
                    <li>
                        <img src="/Municipalidad-Necochea-Caps/imagenes/caps/<?php echo urlencode($row['imagen']); ?>" alt="Imagen del CAPS" class="cap-img" onerror="this.style.display='none'">
                    <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
                        <span><strong>Horario:</strong> <?php echo htmlspecialchars($camp['horario']); ?></span><br>
                        <span><strong>Inicio:</strong> <?php echo htmlspecialchars($camp['fecha_inicio']); ?></span><br>
                        <span><strong>Fin:</strong> <?php echo htmlspecialchars($camp['fecha_fin']); ?></span><br>
                        <span><strong>Requisitos:</strong> <?php echo htmlspecialchars($camp['requisitos']); ?></span>
                    </li>
                <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No hay campañas activas.</p>
            <?php endif; ?>
        </div>

        <!-- Mapa de ubicación -->
        <div id="map" style="width:100%;height:350px;margin:24px 0;border-radius:8px;"></div>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsdto0bY3WfiIHL_JVxqiyAEFRphHOZ0g"></script>
        <script>
            function initMap() {
                // Obtener las coordenadas del CAPS
                var coords = "<?php echo htmlspecialchars($cap['coordenadas']); ?>";
                // Separar latitud y longitud el formato es "lat,lng"(latitud y longitud)
                var parts = coords.split(',');
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: lat, lng: lng},
                    zoom: 16,
                    mapTypeId: 'roadmap',
                    disableDefaultUI: true
                });
                var marker = new google.maps.Marker({
                    position: {lat: lat, lng: lng},
                    map: map,
                    title: '<?php echo addslashes($cap['nombre']); ?>'
                });
            }
            window.onload = initMap;
        </script>

    </div>
</body>
</html>
<?php mysqli_close($conexion); ?>