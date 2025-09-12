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
<h1>Centro de Atencion Primaria de la Salud 
<img src="\Municipalidad-Necochea-Caps\php\imagenes\caps\imagen_caps.png" alt="" class="imagen-caps">
</h1>
<div class="contenedor-informacion">
<h2>¿Que es un CAPS?</h2>
<p class="pinicio">Las siglas CAPS significa Centro de Atencion Primaria de la Salud.</p>
<p class="pinicio">Su objetivo es brindar servicios de sanidad en los barrios que se encuentren en las cercanías de los mismos y las personas que decidan asistir.</p>
<p class="pinicio">Son una puerta de entrada al sistema de salud, sirviendo de promoción y prevención.</p> 
</div>
<div class="contenedor-objetivo">
<h2>Objetivos</h2>
<p class="pinicio">✔️Ofrecer atención sanitaria básica y seguimiento del historial de los pacientes de manera segura y gratuita.</p>
<p class="pinicio">✔️Facilitar el acceso a la salud en la comunidad.</p>
<p class="pinicio">✔️Actuar como puerta de entrada al sistema de salud.</p>
<p class="pinicio">✔️Promover la prevención y las prácticas saludables.</p>
</div>
<div class="contenedor-campaña">
<h2>Campañas</h2>
<p class="pinicio">A lo largo del año se realizan diferentes campañas de vacunación y prevención en los CAPS</p>
<p class="pinicio">Son comunicadas por medio de folletos y medios de difusion y comunicacion de cada CAPS</p>
<p class="pinicio">Estas campañas son fundamentales para proteger la salud de la comunidad y prevenir enfermedades.</p>
<p class="pinicio">Algunas de las campañas más comunes incluyen:</p>
</div>
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\camapaña-diabetes.jpg" alt="" class="imgcampania">
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\camapaña-sarampion.jpg" alt="" class="imgcampania">
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\campaña-pap2.jpeg" alt="" class="imgcampania">
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\campaña-sifilis.jpeg" alt="" class="imgcampania">
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\campañas-colon.jpeg" alt="" class="imgcampania">
    <img src="\Municipalidad-Necochea-Caps\php\imagenes\campanias\camapaña-cancer.jpeg" alt="" class="imgcampania">

<h1>Te compartimos un mapa con todos los CAPS para ver cual queda mas cerca de tu domicilio</h1>

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