<?php
include "conexion.php";

// Obtener el id_caps desde la URL
$id_caps = isset($_GET['id_caps']) ? intval($_GET['id_caps']) : 0;
if ($id_caps <= 0) {
    echo '<p>No se ha seleccionado un CAPS válido.</p>';
    exit;
}

// Consulta para obtener la información basica del CAPS
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
     <div class="cap-item">
    <h1>Detalle del CAPS</h1>
        <div class="caps-descripcion">
        <h2><?php echo htmlspecialchars($cap['nombre']); ?></h2>
        <img src="/Municipalidad-Necochea-Caps/php/imagenes/caps/<?php echo htmlspecialchars($cap['imagen']); ?>" alt="Imagen del CAPS" class="cap-img">
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($cap['descripcion']); ?></p>
        <p><strong>Horario:</strong> <?php echo htmlspecialchars($cap['horario']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cap['telefono']); ?></p>

        <!-- Prestaciones -->
        <?php
        $sql_prestaciones = "SELECT p.id_prestaciones, p.nombre FROM prestaciones p INNER JOIN prestaciones_caps pc ON p.id_prestaciones = pc.id_prestaciones WHERE pc.id_caps = $id_caps";
        $result_prestaciones = mysqli_query($conexion, $sql_prestaciones);
        
        // DEBUG: Líneas temporales para debugging (eliminar después)
        echo "<!-- DEBUG: Consultando prestaciones para CAPS ID: $id_caps -->";
        if (!$result_prestaciones) {
            echo "<!-- ERROR en consulta prestaciones: " . mysqli_error($conexion) . " -->";
        } else {
            echo "<!-- Número de prestaciones encontradas: " . mysqli_num_rows($result_prestaciones) . " -->";
        }
        ?>
        <div class="tarjetas">
        <div class="prestaciones">
            <h3>Prestaciones:</h3>
            <?php if ($result_prestaciones && mysqli_num_rows($result_prestaciones) > 0): ?>
                <ul>
                <?php while($prest = mysqli_fetch_assoc($result_prestaciones)): ?>
                    <li>
                        <?php echo htmlspecialchars($prest['nombre']); ?>
                        <!-- Profesionales de la prestacion -->
                        <?php
                        // CONSULTA CORREGIDA: Obtener profesional específico para esta prestación en este CAPS
                        $id_prestacion = $prest['id_prestaciones'];
                        $sql_prof = "SELECT pr.nombre, pr.apellido, pc.horario_profesional 
                                   FROM profesionales pr 
                                   INNER JOIN prestaciones_caps pc ON pr.id_profesionales = pc.id_profesional 
                                   WHERE pc.id_caps = $id_caps AND pc.id_prestaciones = $id_prestacion";
                        $result_prof = mysqli_query($conexion, $sql_prof);
                        ?>
                        <ul>
                        <?php if ($result_prof && mysqli_num_rows($result_prof) > 0): ?>
                            <?php while($prof = mysqli_fetch_assoc($result_prof)): ?>
                                <li><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']); ?> 
                                <?php if (!empty($prof['horario_profesional'])): ?>
                                    (Horario: <?php echo htmlspecialchars($prof['horario_profesional']); ?>)
                                <?php endif; ?>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No hay profesionales asignados a esta prestación.</li>
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
        $sql_campanias = "SELECT c.imagen, cc.horario, cc.fecha_inicio, cc.fecha_fin, cc.requisitos 
                         FROM campañas_caps cc 
                         INNER JOIN campañas c ON cc.id_campañas = c.id_campañas 
                         WHERE cc.id_caps = $id_caps AND cc.fecha_fin >= CURDATE()";
        $result_campanias = mysqli_query($conexion, $sql_campanias);
        ?>
        <div class="campanias">
            <h3>Campañas activas:</h3>
            <?php if ($result_campanias && mysqli_num_rows($result_campanias) > 0): ?>
                <ul>
                <?php while($camp = mysqli_fetch_assoc($result_campanias)): ?>
                    <li>
                        <img src="/Municipalidad-Necochea-Caps/php/imagenes/campanias/<?php echo htmlspecialchars($camp['imagen']); ?>" alt="Imagen de campaña" class="cap-img" onerror="this.style.display='none'">
                        <h2>Campaña Activa</h2>
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
    </div>

        <!-- Mapa de ubicación -->   
         <div class="map-section">
         <h2>¿No conocias este Caps? </h2>
         <h3>Aqui te dejamos un mapa con su ubicacion</h3>
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
</div>
    <footer>
      <p>Municipalidad de Necochea</p>
    </footer>
</div>  
</body>
</html>
<?php mysqli_close($conexion); ?>