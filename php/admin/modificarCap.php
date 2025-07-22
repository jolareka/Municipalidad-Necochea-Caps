<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: inicioAdmin.php');
    exit;
}
include "conexion.php";

$id_caps = isset($_GET['id_caps']) ? intval($_GET['id_caps']) : 0;

// Obtener datos del CAPS
$cap = [
    'nombre' => '', 'descripcion' => '', 'coordenadas' => '', 'horario' => '', 'imagen' => '', 'telefono' => '', 'Campaña' => 0
];
if ($id_caps > 0) {
    $result = mysqli_query($conexion, "SELECT * FROM caps WHERE id_caps = $id_caps");
    if ($result) $cap = mysqli_fetch_assoc($result);
}

// Obtener prestaciones y profesionales
$prestaciones = mysqli_query($conexion, "SELECT * FROM prestaciones");
$profesionales = mysqli_query($conexion, "SELECT * FROM profesionales");
$campanias = mysqli_query($conexion, "SELECT * FROM campañas");

// Obtener prestaciones asignadas y sus profesionales
$prest_asignadas = [];
if ($id_caps > 0) {
    $res = mysqli_query($conexion, "SELECT pc.id_prestaciones, pp.id_profesionales FROM prestaciones_caps pc LEFT JOIN profesionales_prestaciones pp ON pc.id_prestaciones = pp.id_profesionales WHERE pc.id_caps = $id_caps");
    while($row = mysqli_fetch_assoc($res)) {
        $prest_asignadas[] = [
            'id_prestaciones' => $row['id_prestaciones'],
            'id_profesionales' => $row['id_profesionales']
        ];
    }
}

// Obtener campaña asignada
$campania_asignada = null;
if ($id_caps > 0) {
    $res = mysqli_query($conexion, "SELECT id_campañas FROM campañas_caps WHERE id_caps = $id_caps AND fecha_fin >= CURDATE() LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) $campania_asignada = $row['id_campañas'];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $coordenadas = $_POST['coordenadas'] ?? '';
    $horario = $_POST['horario'] ?? '';
    // Subida de imagen
    $imagen = $cap['imagen'] ?? '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['imagen']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $img_dest = __DIR__ . '/../../img/caps/' . $img_name;
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $img_dest)) {
                $imagen = $img_name;
            }
        }
    }
    $telefono = $_POST['telefono'] ?? '';
    $campania = isset($_POST['campania']) ? 1 : 0;
    $campania_sel = $_POST['campania_sel'] ?? null;
    // Actualizar o insertar CAPS
    if ($id_caps > 0) {
        $sql = "UPDATE caps SET nombre='$nombre', descripcion='$descripcion', coordenadas='$coordenadas', horario='$horario', imagen='$imagen', telefono='$telefono', Campaña=$campania WHERE id_caps=$id_caps";
        mysqli_query($conexion, $sql);
        mysqli_query($conexion, "DELETE FROM prestaciones_caps WHERE id_caps=$id_caps");
        mysqli_query($conexion, "DELETE FROM campañas_caps WHERE id_caps=$id_caps");
    } else {
        $sql = "INSERT INTO caps (nombre, descripcion, coordenadas, horario, imagen, telefono, Campaña) VALUES ('$nombre', '$descripcion', '$coordenadas', '$horario', '$imagen', '$telefono', $campania)";
        mysqli_query($conexion, $sql);
        $id_caps = mysqli_insert_id($conexion);
    }
    // Asignar prestaciones y profesionales
    if (isset($_POST['prestaciones'])) {
        foreach($_POST['prestaciones'] as $i => $id_prest) {
            $id_prof = $_POST['profesional_prest'][$i] ?? 'NULL';
            mysqli_query($conexion, "INSERT INTO prestaciones_caps (id_caps, id_prestaciones) VALUES ($id_caps, $id_prest)");
            // No hay relación directa en la estructura, pero se podría guardar en una tabla adicional si existiera
        }
    }
    // Asignar campaña
    if ($campania && $campania_sel) {
        mysqli_query($conexion, "INSERT INTO campañas_caps (id_caps, id_campañas, horario, fecha_inicio, fecha_fin, requisitos) VALUES ($id_caps, $campania_sel, '', CURDATE(), CURDATE(), '')");
    }
    header('Location: panelAdmin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar CAPS</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/panelAdmin.css">
    <script>
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
    }
    </script>
</head>
<body>
    <h1 style="text-align:center;">Modificar CAPS</h1>
    <form method="post" style="max-width:500px;margin:40px auto;background:#fff;padding:24px;border-radius:8px;">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($cap['nombre']); ?>" required><br>
        <label>Descripción:</label>
        <input type="text" name="descripcion" value="<?php echo htmlspecialchars($cap['descripcion']); ?>" required><br>
        <label>Coordenadas:</label>
        <input type="text" name="coordenadas" value="<?php echo htmlspecialchars($cap['coordenadas']); ?>" required><br>
        <label>Horario:</label>
        <input type="text" name="horario" value="<?php echo htmlspecialchars($cap['horario']); ?>" required><br>
        <label>Imagen:</label>
        <input type="file" name="imagen" accept="image/*"><br>
        <?php if (!empty($cap['imagen'])): ?>
            <div style="margin:8px 0;">
                <span style="color:#888;">Actual:</span><br>
                <img src="/Municipalidad-Necochea-Caps/php/imagenes/caps/<?php echo htmlspecialchars($cap['imagen']); ?>" alt="Imagen actual" style="max-width:120px;max-height:120px;border:1px solid #ccc;">
            </div>
        <?php endif; ?>
        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($cap['telefono']); ?>"><br>
        <label>¿Tiene campaña?</label>
        <input type="checkbox" name="campania" id="campania-check" <?php if($cap['Campaña']) echo 'checked'; ?> onchange="document.getElementById('campania-select').style.display=this.checked?'inline':'none'">
        <select name="campania_sel" id="campania-select" style="display:<?php echo $cap['Campaña'] ? 'inline' : 'none'; ?>;">
            <option value="">Seleccionar campaña</option>
            <?php 
            $campanias_arr = [];
            while($camp = mysqli_fetch_assoc($campanias)) {
                $campanias_arr[] = $camp;
                echo '<option value="'.$camp['id_campañas'].'" '.($campania_asignada==$camp['id_campañas']?'selected':'').'>'.htmlspecialchars($camp['imagen']).'</option>';
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
        </select><br>
        <label>Prestaciones asignadas:</label><br>
        <div id="prestaciones-lista">
            <?php if (empty($prest_asignadas)): ?>
                <div style="color: #888; margin-bottom: 10px;">No hay prestaciones asignadas.</div>
            <?php else: ?>
                <?php foreach($prest_asignadas as $asig): ?>
                    <div>
                        <input type="hidden" name="prestaciones[]" value="<?php echo $asig['id_prestaciones']; ?>">
                        <input type="hidden" name="profesional_prest[]" value="<?php echo $asig['id_profesionales']; ?>">
                        <?php 
                        $p = mysqli_query($conexion, "SELECT nombre FROM prestaciones WHERE id_prestaciones=".$asig['id_prestaciones']);
                        $n = mysqli_fetch_assoc($p);
                        $pr = mysqli_query($conexion, "SELECT nombre, apellido FROM profesionales WHERE id_profesionales=".$asig['id_profesionales']);
                        $np = mysqli_fetch_assoc($pr);
                        echo ($n['nombre'] ?? '') . ' - ' . (($np['nombre'] ?? '') . ' ' . ($np['apellido'] ?? ''));
                        ?>
                        <button type="button" onclick="this.parentNode.remove()">Eliminar</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
        <br>
        <button type="submit">Guardar</button>
        <a href="panelAdmin.php">Cancelar</a>
    </form>
</body>
</html>
<?php mysqli_close($conexion); ?>
