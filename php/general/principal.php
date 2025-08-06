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
<link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/principal.css">
<h1>Centro de Atencion Primaria de la Salud</h1>
<h2>¿Que es un Caps?</h2>
<p>Las siglas CAPS significa Centro de Atencion Primaria de la Salud.</p>
<p>Su objetivo es brindar servicios de sanidad en los barrios que se encuentren en las cercanías de los mismos y las personas que decidan asistir.</p>
<p>Son una puerta de entrada al sistema de salud, sirviendo de promoción y prevención.</p>

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
</div>
<div id="map" style="width:100%;height:400px;margin:32px 0 0 0;border-radius:8px;"></div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsdto0bY3WfiIHL_JVxqiyAEFRphHOZ0g"></script>
<script>
    var caps = <?php echo json_encode($caps_js); ?>; // Pasar los datos de los caps a JavaScript
    function initMap() { // Inicializar el mapa
        var map = new google.maps.Map(document.getElementById('map'), { // Configuracion
            center: {lat: -38.55, lng: -58.74}, // Centro de Necochea 
            zoom: 13, // Nivel de zoom
            mapTypeId: 'roadmap', // Tipo de mapa
            disableDefaultUI: true 
        });
        caps.forEach(function(cap) { // Recorre cada cap
            if (cap.coordenadas) { // Verifica si tiene coordenadas
                var parts = cap.coordenadas.split(','); // Separa latitud y longitud
                var lat = parseFloat(parts[0]); // Convierte a float
                var lng = parseFloat(parts[1]); // Convierte a float
                if (!isNaN(lat) && !isNaN(lng)) { // Verifica que sean numeros válidos
                    var marker = new google.maps.Marker({ // Crea un marcador
                        position: {lat: lat, lng: lng}, // Posicion del marcador 
                        map: map, // Mapa donde se muestra
                        title: cap.nombre, // Titulo del marcador
                        icon: {
                            url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png" // Icono del marcador
                        }
                    });
                    var infowindow = new google.maps.InfoWindow({ // Ventana de información
                        content: '<strong>' + cap.nombre + '</strong><br>' + cap.descripcion + '<br><a href="/Municipalidad-Necochea-Caps/php/general/mostrarCap.php?id_caps=' + cap.id_caps + '">Ver detalles</a>' // Contenido de la ventana
                    });
                    marker.addListener('click', function() { // Evento al hacer click en el marcador
                        infowindow.open(map, marker); // Abre la ventana de información
                    });
                }
            }
        });
    }
    window.onload = initMap; // Llama a la funcion al cargar la pagina
</script>
<?php mysqli_close($conexion); ?>