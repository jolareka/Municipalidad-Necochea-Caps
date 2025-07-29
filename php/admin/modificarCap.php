<?php
session_start();
if (!isset($_SESSION['admin'])) { 
    header('Location: inicioAdmin.php'); 
    exit; 
}
include "conexion.php";

// Obtener ID del CAPS a editar
$id_caps = isset($_GET['id_caps']) ? intval($_GET['id_caps']) : 0;
$editando = $id_caps > 0;

// Datos por defecto
$caps = [
    'nombre' => '',
    'descripcion' => '',
    'coordenadas' => '',
    'horario' => '',
    'telefono' => '',
    'imagen' => ''
];

// Si estamos editando, cargar datos existentes
if ($editando) {
    $result = mysqli_query($conexion, "SELECT * FROM caps WHERE id_caps = $id_caps");
    if ($result && mysqli_num_rows($result) > 0) {
        $caps = mysqli_fetch_assoc($result);
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $coordenadas = mysqli_real_escape_string($conexion, $_POST['coordenadas']);
    $horario = mysqli_real_escape_string($conexion, $_POST['horario']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $imagen = $caps['imagen']; // Mantener imagen actual por defecto
    
    // Manejar subida de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $img_name = time() . '_' . basename($_FILES['imagen']['name']);
        $img_dest = __DIR__ . '/../../img/caps/' . $img_name;
        
        // Crear directorio si no existe
        $dir = dirname($img_dest);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $img_dest)) {
            $imagen = $img_name;
        }
    }
    
    if ($editando) {
        // Actualizar CAPS existente
        $sql = "UPDATE caps SET 
                nombre = '$nombre', 
                descripcion = '$descripcion', 
                coordenadas = '$coordenadas', 
                horario = '$horario', 
                telefono = '$telefono', 
                imagen = '$imagen' 
                WHERE id_caps = $id_caps";
        
        if (mysqli_query($conexion, $sql)) {
            // Eliminar prestaciones anteriores
            mysqli_query($conexion, "DELETE FROM prestaciones_caps WHERE id_caps = $id_caps");
            $mensaje = "CAPS actualizado correctamente";
        } else {
            $error = "Error al actualizar: " . mysqli_error($conexion);
        }
    } else {
        // Crear nuevo CAPS
        $sql = "INSERT INTO caps (nombre, descripcion, coordenadas, horario, telefono, imagen, Campaña) 
                VALUES ('$nombre', '$descripcion', '$coordenadas', '$horario', '$telefono', '$imagen', 0)";
        
        if (mysqli_query($conexion, $sql)) {
            $mensaje = "CAPS creado correctamente";
            $id_caps = mysqli_insert_id($conexion);
        } else {
            $error = "Error al crear: " . mysqli_error($conexion);
        }
    }
    
    // Guardar prestaciones, profesionales y horarios (si no hubo errores)
    if (isset($mensaje) && isset($_POST['prestaciones']) && is_array($_POST['prestaciones'])) {
        foreach ($_POST['prestaciones'] as $id_prestacion) {
            $id_prestacion = intval($id_prestacion);
            if ($id_prestacion > 0) {
                // Obtener el profesional y horario asignados a esta prestación
                $id_profesional = isset($_POST['profesional_' . $id_prestacion]) ? intval($_POST['profesional_' . $id_prestacion]) : NULL;
                $id_profesional = $id_profesional > 0 ? $id_profesional : NULL;
                
                $horario = isset($_POST['horario_' . $id_prestacion]) ? mysqli_real_escape_string($conexion, $_POST['horario_' . $id_prestacion]) : '';
                
                // Verificar si ya existe esta combinación para evitar duplicados
                $check = mysqli_query($conexion, "SELECT 1 FROM prestaciones_caps WHERE id_caps = $id_caps AND id_prestaciones = $id_prestacion");
                if (mysqli_num_rows($check) == 0) {
                    $sql_insert = "INSERT INTO prestaciones_caps (id_caps, id_prestaciones, id_profesional, horario_profesional) 
                                   VALUES ($id_caps, $id_prestacion, " . ($id_profesional ? $id_profesional : 'NULL') . ", '$horario')";
                    $result = mysqli_query($conexion, $sql_insert);
                    if (!$result) {
                        $error = "Error al asignar prestación: " . mysqli_error($conexion);
                        break;
                    }
                }
            }
        }
    }
    
    // Redirigir después de 2 segundos si todo salió bien
    if (isset($mensaje)) {
        echo "<script>
                alert('$mensaje');
                setTimeout(function(){ window.location.href = 'panelAdmin.php'; }, 2000);
              </script>";
    }
}

// Obtener prestaciones y profesionales actuales del CAPS
$prestaciones_asignadas = [];
if ($editando) {
    $result = mysqli_query($conexion, "SELECT pc.id_prestaciones, pc.id_profesional, pc.horario_profesional, 
                                              p.nombre as prestacion_nombre, 
                                              CONCAT(pr.nombre, ' ', pr.apellido) as profesional_nombre
                                       FROM prestaciones_caps pc 
                                       LEFT JOIN prestaciones p ON pc.id_prestaciones = p.id_prestaciones
                                       LEFT JOIN profesionales pr ON pc.id_profesional = pr.id_profesionales
                                       WHERE pc.id_caps = $id_caps");
    while ($row = mysqli_fetch_assoc($result)) {
        $prestaciones_asignadas[$row['id_prestaciones']] = [
            'id_profesional' => $row['id_profesional'],
            'horario_profesional' => $row['horario_profesional'],
            'prestacion_nombre' => $row['prestacion_nombre'],
            'profesional_nombre' => $row['profesional_nombre']
        ];
    }
}

// Obtener todas las prestaciones y profesionales
$prestaciones = mysqli_query($conexion, "SELECT id_prestaciones, nombre FROM prestaciones ORDER BY nombre");
$profesionales = mysqli_query($conexion, "SELECT id_profesionales, nombre, apellido FROM profesionales ORDER BY nombre, apellido");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editando ? 'Editar' : 'Crear'; ?> CAPS</title>
    <link rel="stylesheet" href="../../css/modificarCap.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $editando ? 'Editar' : 'Crear'; ?> CAPS</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($mensaje)): ?>
            <div class="success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del CAPS:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($caps['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($caps['descripcion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="coordenadas">Coordenadas (latitud, longitud):</label>
                <input type="text" id="coordenadas" name="coordenadas" value="<?php echo htmlspecialchars($caps['coordenadas']); ?>" 
                       placeholder="Ej: -38.5495, -58.7377" required>
            </div>
            
            <div class="form-group">
                <label for="horario">Horario de atención:</label>
                <input type="text" id="horario" name="horario" value="<?php echo htmlspecialchars($caps['horario']); ?>" 
                       placeholder="Ej: 08:00-16:00" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($caps['telefono']); ?>" 
                       placeholder="Ej: 2262-123456">
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <?php if (!empty($caps['imagen'])): ?>
                    <div style="margin-top: 10px;">
                        <p>Imagen actual:</p>
                        <img src="/Municipalidad-Necochea-Caps/img/caps/<?php echo htmlspecialchars($caps['imagen']); ?>" 
                             class="image-preview" alt="Imagen actual">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="prestaciones-section">
                    <label>Prestaciones, Profesionales y Horarios:</label>
                    <div class="prestaciones-grid">
                        <?php 
                        mysqli_data_seek($prestaciones, 0); // Reiniciar el puntero del resultado
                        while ($prestacion = mysqli_fetch_assoc($prestaciones)): 
                            $checked = isset($prestaciones_asignadas[$prestacion['id_prestaciones']]) ? 'checked' : '';
                            $profesional_asignado = isset($prestaciones_asignadas[$prestacion['id_prestaciones']]) ? 
                                                   $prestaciones_asignadas[$prestacion['id_prestaciones']]['id_profesional'] : '';
                            $horario_asignado = isset($prestaciones_asignadas[$prestacion['id_prestaciones']]) ? 
                                               $prestaciones_asignadas[$prestacion['id_prestaciones']]['horario_profesional'] : '';
                        ?>
                            <div class="prestacion-item">
                                <div class="prestacion-checkbox">
                                    <input type="checkbox" 
                                           id="prest_<?php echo $prestacion['id_prestaciones']; ?>"
                                           name="prestaciones[]" 
                                           value="<?php echo $prestacion['id_prestaciones']; ?>"
                                           onchange="togglePrestacionDetails(<?php echo $prestacion['id_prestaciones']; ?>)"
                                           <?php echo $checked; ?>>
                                    <label for="prest_<?php echo $prestacion['id_prestaciones']; ?>">
                                        <strong><?php echo htmlspecialchars($prestacion['nombre']); ?></strong>
                                    </label>
                                </div>
                                
                                <div class="prestacion-details">
                                    <div class="profesional-select">
                                        <label for="prof_<?php echo $prestacion['id_prestaciones']; ?>">Profesional:</label>
                                        <select name="profesional_<?php echo $prestacion['id_prestaciones']; ?>" 
                                                id="prof_<?php echo $prestacion['id_prestaciones']; ?>"
                                                <?php echo !$checked ? 'disabled' : ''; ?>>
                                            <option value="">Sin profesional</option>
                                            <?php 
                                            mysqli_data_seek($profesionales, 0);
                                            while ($profesional = mysqli_fetch_assoc($profesionales)): 
                                                $selected = ($profesional_asignado == $profesional['id_profesionales']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $profesional['id_profesionales']; ?>" <?php echo $selected; ?>>
                                                    <?php echo htmlspecialchars($profesional['nombre'] . ' ' . $profesional['apellido']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="horario-input">
                                        <label for="horario_<?php echo $prestacion['id_prestaciones']; ?>">Horario:</label>
                                        <input type="text" 
                                               name="horario_<?php echo $prestacion['id_prestaciones']; ?>" 
                                               id="horario_<?php echo $prestacion['id_prestaciones']; ?>"
                                               value="<?php echo htmlspecialchars($horario_asignado); ?>"
                                               placeholder="Ej: 09:00-13:00"
                                               <?php echo !$checked ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php if (mysqli_num_rows($prestaciones) == 0): ?>
                        <p style="color: #666; margin-top: 10px;">No hay prestaciones disponibles. <a href="prestaciones.php">Crear prestaciones</a></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <script>
            function togglePrestacionDetails(prestacionId) {
                const checkbox = document.getElementById('prest_' + prestacionId);
                const select = document.getElementById('prof_' + prestacionId);
                const horario = document.getElementById('horario_' + prestacionId);
                
                if (checkbox.checked) {
                    select.disabled = false;
                    horario.disabled = false;
                } else {
                    select.disabled = true;
                    select.value = ''; // Limpiar selección
                    horario.disabled = true;
                    horario.value = ''; // Limpiar horario
                }
            }
            </script>
            
            <div class="button-group">
                <button type="submit"><?php echo $editando ? 'Actualizar' : 'Crear'; ?> CAPS</button>
                <a href="panelAdmin.php" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php 
mysqli_close($conexion); 
?>