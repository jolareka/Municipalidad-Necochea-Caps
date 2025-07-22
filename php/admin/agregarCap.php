<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: inicioAdmin.php');
    exit;
}
include "../general/conexion.php";

// Obtener prestaciones y profesionales
$prestaciones = mysqli_query($conexion, "SELECT * FROM prestaciones");
$profesionales = mysqli_query($conexion, "SELECT * FROM profesionales");
$campanias = mysqli_query($conexion, "SELECT * FROM campañas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = '';
    // CAPS
    if (isset($_POST['nombre']) && empty($_POST['guardar_prof']) && empty($_POST['guardar_camp'])) {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $coordenadas = $_POST['coordenadas'] ?? '';
        $horario = $_POST['horario'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $campania = isset($_POST['campania']) ? 1 : 0;
        $campania_sel = $_POST['campania_sel'] ?? null;
        // Subida de imagen CAPS
        $imagen = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['imagen']['name']);
            $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $img_dest = __DIR__ . '/Municipalidad-Necochea-Caps/php/imagenes/caps/' . $img_name;
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($img_ext, $allowed)) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $img_dest)) {
                    $imagen = $img_name;
                }
            }
        }
        // Insertar caps
        $sql = "INSERT INTO caps (nombre, descripcion, coordenadas, horario, imagen, telefono, Campaña) VALUES ('$nombre', '$descripcion', '$coordenadas', '$horario', '$imagen', '$telefono', $campania)";
        if (mysqli_query($conexion, $sql)) {
            $id_caps = mysqli_insert_id($conexion);
            // Asignar prestaciones y profesionales
            if (isset($_POST['prestaciones'])) {
                foreach($_POST['prestaciones'] as $i => $id_prest) {
                    $id_prof = $_POST['profesional_prest'][$i] ?? 'NULL';
                    mysqli_query($conexion, "INSERT INTO prestaciones_caps (id_caps, id_prestaciones) VALUES ($id_caps, $id_prest)");
                }
            }
            // Asignar campaña
            if ($campania && $campania_sel) {
                mysqli_query($conexion, "INSERT INTO campañas_caps (id_caps, id_campañas, horario, fecha_inicio, fecha_fin, requisitos) VALUES ($id_caps, $campania_sel, '', CURDATE(), CURDATE(), '')");
            }
            $mensaje = 'Centro de Salud guardado exitosamente.';
        } else {
            $mensaje = 'Error al guardar el Centro de Salud.';
        }
    }
    // PROFESIONAL
    if (!empty($_POST['guardar_prof'])) {
        $nombre_prof = $_POST['nombre_prof'] ?? '';
        $apellido_prof = $_POST['apellido_prof'] ?? '';
        if ($nombre_prof && $apellido_prof) {
            if (mysqli_query($conexion, "INSERT INTO profesionales (nombre, apellido) VALUES ('$nombre_prof', '$apellido_prof')")) {
                $mensaje = 'Profesional guardado exitosamente.';
            } else {
                $mensaje = 'Error al guardar el profesional.';
            }
        } else {
            $mensaje = 'Complete todos los campos del profesional.';
        }
    }
    // CAMPAÑA
    if (!empty($_POST['guardar_camp'])) {
        $imagen_camp = '';
        if (isset($_FILES['imagen_camp']) && $_FILES['imagen_camp']['error'] === UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['imagen_camp']['name']);
            $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $img_dest = __DIR__ . '/Municipalidad-Necochea-Caps/php/imagenes/campanias/' . $img_name;
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($img_ext, $allowed)) {
                if (move_uploaded_file($_FILES['imagen_camp']['tmp_name'], $img_dest)) {
                    $imagen_camp = $img_name;
                }
            }
        }
        // Si no se subió archivo, usar el texto
        if (!$imagen_camp && !empty($_POST['imagen_camp_text'])) {
            $imagen_camp = $_POST['imagen_camp_text'];
        }
        if ($imagen_camp) {
            if (mysqli_query($conexion, "INSERT INTO campañas (imagen) VALUES ('$imagen_camp')")) {
                $mensaje = 'Campaña guardada exitosamente.';
            } else {
                $mensaje = 'Error al guardar la campaña.';
            }
        } else {
            $mensaje = 'Debe seleccionar una imagen o escribir el nombre de archivo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar CAPS</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/agregarCap.css">
    <script>
    function mostrarAgregarPrestacion() {
        document.getElementById('agregar-prestacion').style.display = 'block';
        document.getElementById('btn-agregar-prest').style.display = 'none';
    }
    function ocultarAgregarPrestacion() {
        document.getElementById('agregar-prestacion').style.display = 'none';
        document.getElementById('btn-agregar-prest').style.display = 'inline-block';
    }
    function agregarPrestacion() {
        var selectPrest = document.getElementById('prestacion-select');
        var selectProf = document.getElementById('profesional-select');
        var lista = document.getElementById('prestaciones-lista');
        var idPrest = selectPrest.value;
        var nombrePrest = selectPrest.options[selectPrest.selectedIndex].text;
        var idProf = selectProf.value;
        var nombreProf = selectProf.options[selectProf.selectedIndex].text;
        if (!idPrest) return;
        var div = document.createElement('div');
        div.innerHTML = '<input type="hidden" name="prestaciones[]" value="'+idPrest+'">' +
                        '<input type="hidden" name="profesional_prest[]" value="'+idProf+'">' +
                        nombrePrest + ' - ' + nombreProf +
                        ' <button type="button" onclick="this.parentNode.remove()">Eliminar</button>';
        lista.appendChild(div);
        ocultarAgregarPrestacion();
        selectPrest.selectedIndex = 0;
        selectProf.selectedIndex = 0;
    }
    </script>
</head>
<body>
    <h1 class="titulo-form">Agregar datos</h1>
    <div class="form-container">
        <?php if (!empty($mensaje)): ?>
        <div id="mensaje-guardado" class="mensaje-guardado">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        <script>
        setTimeout(function(){
            var msg = document.getElementById('mensaje-guardado');
            if(msg) msg.style.display = 'none';
        }, 3500);
        </script>
        <?php endif; ?>
        <label>¿Qué desea agregar?</label>
        <select id="tipo-agregar" onchange="mostrarFormularioAgregar()" style="width:100%;margin-bottom:20px;">
            <option value="caps">Centro de Salud (CAPS)</option>
            <option value="profesional">Profesional</option>
            <option value="campania">Campaña</option>
        </select>
        <div id="form-caps">
            <form method="post" enctype="multipart/form-data">
                <label>Nombre del centro:</label>
                <input type="text" name="nombre" required><br>
                <label>Descripción:</label>
                <input type="text" name="descripcion" required><br>
                <label>Coordenadas:</label>
                <input type="text" name="coordenadas" required><br>
                <label>Horario:</label>
                <input type="text" name="horario" required><br>
                <label>Imagen:</label>
                <input type="file" name="imagen" accept="image/*"><br>
                <label>Teléfono:</label>
                <input type="text" name="telefono"><br>
                <label>¿Tiene campaña?</label>
                <input type="checkbox" name="campania" id="campania-check" onchange="document.getElementById('campania-select').style.display=this.checked?'inline':'none'">
                <select name="campania_sel" id="campania-select" style="display:none;">
                <option value="">Seleccionar campaña</option>
                    <?php 
                    $campanias_arr = [];
                    $campanias2 = mysqli_query($conexion, "SELECT * FROM campañas");
                    while($camp = mysqli_fetch_assoc($campanias2)) {
                        $campanias_arr[] = $camp;
                        echo '<option value="'.$camp['id_campañas'].'">'.htmlspecialchars($camp['imagen']).'</option>';
                    }
                    ?>
                </select><br>
                <div id="datos-campania" style="display:none; background:#f7f7f7; border:1px solid #ddd; padding:10px; margin-bottom:10px;"></div>
                <script>
                var campanias = <?php echo json_encode($campanias_arr); ?>;
                document.getElementById('campania-select').addEventListener('change', function() {
                    var val = this.value;
                    var info = '';
                    if(val) {
                        var camp = campanias.find(function(c){ return c.id_campañas == val; });
                        if(camp) {
                            info += '<b>Imagen:</b> '+camp.imagen+'<br>';
                            info += '<b>ID:</b> '+camp.id_campañas+'<br>';
                        }
                    }
                    document.getElementById('datos-campania').innerHTML = info;
                    document.getElementById('datos-campania').style.display = val ? 'block' : 'none';
                });
                </script>
                <label>Prestaciones asignadas:</label><br>
                <div id="prestaciones-lista">
                    <div style="color: #888; margin-bottom: 10px;">No hay prestaciones asignadas.</div>
                </div>
                <button type="button" id="btn-agregar-prest" onclick="mostrarAgregarPrestacion()" style="margin-top:10px;">Agregar prestación</button>
                <div id="agregar-prestacion" style="display:none; margin-top:10px;">
                    <select id="prestacion-select">
                <option value="">Seleccionar prestación</option>
                        <?php $prestaciones2 = mysqli_query($conexion, "SELECT * FROM prestaciones"); while($prest = mysqli_fetch_assoc($prestaciones2)): ?>
                            <option value="<?php echo $prest['id_prestaciones']; ?>"><?php echo htmlspecialchars($prest['nombre']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select id="profesional-select">
                <option value="">Seleccionar profesional</option>
                        <?php $profesionales2 = mysqli_query($conexion, "SELECT * FROM profesionales"); while($prof = mysqli_fetch_assoc($profesionales2)): ?>
                            <option value="<?php echo $prof['id_profesionales']; ?>"><?php echo htmlspecialchars($prof['nombre'].' '.$prof['apellido']); ?></option>
                        <?php endwhile; ?>
                    </select>
                <button type="button" onclick="agregarPrestacion()">Agregar</button>
                <button type="button" onclick="ocultarAgregarPrestacion()">Cancelar</button>
                </div>
                <br>
                <button type="submit">Guardar centro</button>
                <a href="panelAdmin.php">Cancelar</a>
            </form>
        </div>
        <div id="form-profesional" style="display:none;">
            <form method="post">
                <label>Nombre del profesional:</label>
                <input type="text" name="nombre_prof" required><br>
                <label>Apellido del profesional:</label>
                <input type="text" name="apellido_prof" required><br>
                <button type="submit" name="guardar_prof">Guardar profesional</button>
            </form>
        </div>
        <div id="form-campania" style="display:none;">
            <form method="post" enctype="multipart/form-data">
                <label>Imagen de la campaña:</label>
                <input type="file" name="imagen_camp" accept="image/*"><br>
                <span style="color:#888;">O nombre de archivo existente:</span>
                <input type="text" name="imagen_camp_text"><br>
                <button type="submit" name="guardar_camp">Guardar campaña</button>
            </form>
        </div>
    </div>
    <script>
    function setFormEnabled(formId, enabled) {
        var form = document.getElementById(formId);
        if (!form) return;
        var elements = form.querySelectorAll('input, select, textarea, button');
        elements.forEach(function(el) {
            if (enabled) {
                el.removeAttribute('disabled');
            } else {
                el.setAttribute('disabled', 'disabled');
            }
        });
    }
    function mostrarFormularioAgregar() {
        var tipo = document.getElementById('tipo-agregar').value;
        var caps = document.getElementById('form-caps');
        var prof = document.getElementById('form-profesional');
        var camp = document.getElementById('form-campania');
        caps.style.display = tipo === 'caps' ? 'block' : 'none';
        prof.style.display = tipo === 'profesional' ? 'block' : 'none';
        camp.style.display = tipo === 'campania' ? 'block' : 'none';
        setFormEnabled('form-caps', tipo === 'caps');
        setFormEnabled('form-profesional', tipo === 'profesional');
        setFormEnabled('form-campania', tipo === 'campania');
    }
    mostrarFormularioAgregar();
    </script>
</body>
</html>
<?php mysqli_close($conexion); ?>
