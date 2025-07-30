<?php
session_start();

// Verificar si es administrador
if (!isset($_SESSION['admin'])) {
    header('Location: inicioAdmin.php');
    exit;
}

include "conexion.php";
$mensaje = '';

// PROCESAR FORMULARIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // AGREGAR CENTRO DE SALUD
    if (isset($_POST['guardar_centro'])) {
        $nombre_centro = $_POST['nombre_centro'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $coordenadas = $_POST['coordenadas'] ?? '';
        $horario = $_POST['horario'] ?? '';
        $telefono = preg_replace('/\D/', '', $_POST['telefono'] ?? '');
        
        // Procesar imagen
        $imagen = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = basename($_FILES['imagen']['name']);
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $destino = __DIR__ . '/../imagenes/caps/' . $nombre_archivo;
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($extension, $extensiones_permitidas)) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $imagen = $nombre_archivo;
                }
            }
        }
        
        $tiene_campania = isset($_POST['tiene_campania']) ? 1 : 0;
        
        // Insertar centro de salud
        $consulta = "INSERT INTO caps (nombre, descripcion, coordenadas, horario, imagen, telefono, Campaña) 
                     VALUES ('$nombre_centro', '$descripcion', '$coordenadas', '$horario', '$imagen', '$telefono', $tiene_campania)";
        
        if (mysqli_query($conexion, $consulta)) {
            $id_centro = mysqli_insert_id($conexion);
            
            // Procesar prestaciones si existen
            if (isset($_POST['prestaciones'])) {
                foreach ($_POST['prestaciones'] as $indice => $id_prestacion) {
                    $id_profesional = $_POST['profesional_prestacion'][$indice] ?? null;
                    $horario_profesional = $_POST['horario_profesional'][$indice] ?? '';
                    
                    // CORREGIDO: Usar prestaciones_caps en lugar de dos tablas separadas
                    $id_prof_value = ($id_profesional && $id_profesional !== '') ? $id_profesional : 'NULL';
                    $horario_escapado = mysqli_real_escape_string($conexion, $horario_profesional);
                    
                    // Insertar en prestaciones_caps con todos los datos
                    $sql_prestacion = "INSERT INTO prestaciones_caps (id_caps, id_prestaciones, id_profesional, horario_profesional) 
                                     VALUES ($id_centro, $id_prestacion, $id_prof_value, '$horario_escapado')";
                    
                    if (!mysqli_query($conexion, $sql_prestacion)) {
                        $mensaje = 'Error al asignar prestación: ' . mysqli_error($conexion);
                        break;
                    }
                }
            }
            
            if (empty($mensaje)) {
                $mensaje = 'Centro de Salud guardado exitosamente.';
            }
        } else {
            $mensaje = 'Error al guardar el Centro de Salud: ' . mysqli_error($conexion);
        }
    }
    
    // AGREGAR PROFESIONAL
    if (isset($_POST['guardar_profesional'])) {
        $nombre_profesional = mysqli_real_escape_string($conexion, $_POST['nombre_profesional'] ?? '');
        $apellido_profesional = mysqli_real_escape_string($conexion, $_POST['apellido_profesional'] ?? '');
        
        if ($nombre_profesional && $apellido_profesional) {
            $consulta = "INSERT INTO profesionales (nombre, apellido) VALUES ('$nombre_profesional', '$apellido_profesional')";
            
            if (mysqli_query($conexion, $consulta)) {
                $mensaje = 'Profesional guardado exitosamente.';
            } else {
                $mensaje = 'Error al guardar el profesional: ' . mysqli_error($conexion);
            }
        } else {
            $mensaje = 'Complete todos los campos del profesional.';
        }
    }
    
    // AGREGAR CAMPAÑA
    if (isset($_POST['guardar_campania'])) {
        $imagen_campania = '';
        
        // Procesar imagen subida
        if (isset($_FILES['imagen_campania']) && $_FILES['imagen_campania']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = basename($_FILES['imagen_campania']['name']);
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $destino = __DIR__ . '/../imagenes/campanias/' . $nombre_archivo;
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($extension, $extensiones_permitidas)) {
                if (move_uploaded_file($_FILES['imagen_campania']['tmp_name'], $destino)) {
                    $imagen_campania = $nombre_archivo;
                }
            }
        }
        
        // O usar nombre de archivo existente
        if (!$imagen_campania && !empty($_POST['nombre_archivo_existente'])) {
            $imagen_campania = mysqli_real_escape_string($conexion, $_POST['nombre_archivo_existente']);
        }
        
        if ($imagen_campania) {
            $consulta = "INSERT INTO campañas (imagen) VALUES ('$imagen_campania')";
            
            if (mysqli_query($conexion, $consulta)) {
                $mensaje = 'Campaña guardada exitosamente.';
            } else {
                $mensaje = 'Error al guardar la campaña: ' . mysqli_error($conexion);
            }
        } else {
            $mensaje = 'Debe seleccionar una imagen o especificar un nombre de archivo.';
        }
    }
}

// Obtener datos para los selects
$lista_prestaciones = mysqli_query($conexion, "SELECT * FROM prestaciones ORDER BY nombre");
$lista_profesionales = mysqli_query($conexion, "SELECT * FROM profesionales ORDER BY nombre, apellido");
$lista_campanias = mysqli_query($conexion, "SELECT * FROM campañas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Datos - Panel Administrativo</title>
    <link rel="stylesheet" href="/Municipalidad-Necochea-Caps/css/agregar_datos.css">
</head>
<body>
    <div class="contenedor">
        <h1>Agregar Datos al Sistema</h1>
        
        <!-- Mostrar mensajes -->
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo (strpos($mensaje, 'Error') !== false || strpos($mensaje, 'Complete') !== false) ? 'error' : 'exito'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <!-- Selector de tipo de formulario -->
        <div class="selector-tipo">
            <label for="tipo-formulario">¿Qué desea agregar?</label>
            <select id="tipo-formulario" onchange="mostrarFormulario()">
                <option value="centro">Centro de Salud (CAPS)</option>
                <option value="profesional">Profesional</option>
                <option value="campania">Campaña</option>
            </select>
        </div>
        
        <!-- FORMULARIO CENTRO DE SALUD -->
        <div id="formulario-centro" class="formulario activo">
            <h2>Agregar Centro de Salud</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="campo">
                    <label for="nombre_centro">Nombre del centro:</label>
                    <input type="text" name="nombre_centro" id="nombre_centro" required>
                </div>
                
                <div class="campo">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" required></textarea>
                </div>
                
                <div class="campo">
                    <label for="coordenadas">Coordenadas:</label>
                    <input type="text" name="coordenadas" id="coordenadas" placeholder="Ej: -38.5555, -58.7389" required>
                </div>
                
                <div class="campo">
                    <label for="horario">Horario (HH:MM):</label>
                    <input type="time" name="horario" id="horario" required>
                </div>
                
                <div class="campo">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" required>
                </div>
                
                <div class="campo">
                    <label for="imagen">Imagen del centro:</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*">
                </div>
                
                <div class="campo checkbox">
                    <label>
                        <input type="checkbox" name="tiene_campania" id="tiene_campania" onchange="toggleCampania()">
                        ¿Tiene campaña activa?
                    </label>
                    <select name="campania_seleccionada" id="campania_seleccionada" style="display:none;">
                        <option value="">Seleccionar campaña</option>
                        <?php 
                        if ($lista_campanias && mysqli_num_rows($lista_campanias) > 0):
                            while ($campania = mysqli_fetch_assoc($lista_campanias)): 
                        ?>
                            <option value="<?php echo $campania['id_campañas']; ?>">
                                <?php echo htmlspecialchars($campania['imagen']); ?>
                            </option>
                        <?php 
                            endwhile; 
                        endif;
                        ?>
                    </select>
                </div>
                
                <div class="campo">
                    <label>Prestaciones del centro:</label>
                    <div id="lista-prestaciones">
                        <p class="sin-prestaciones">No hay prestaciones asignadas</p>
                    </div>
                    <button type="button" class="btn-secundario" onclick="mostrarAgregarPrestacion()">
                        Agregar Prestación
                    </button>
                </div>
                
                <!-- Panel para agregar prestaciones -->
                <div id="panel-prestacion" class="panel-oculto">
                    <h3>Agregar Prestación</h3>
                    <select id="select-prestacion">
                        <option value="">Seleccionar prestación</option>
                        <?php 
                        if ($lista_prestaciones && mysqli_num_rows($lista_prestaciones) > 0):
                            mysqli_data_seek($lista_prestaciones, 0);
                            while ($prestacion = mysqli_fetch_assoc($lista_prestaciones)): 
                        ?>
                            <option value="<?php echo $prestacion['id_prestaciones']; ?>">
                                <?php echo htmlspecialchars($prestacion['nombre']); ?>
                            </option>
                        <?php 
                            endwhile; 
                        endif;
                        ?>
                    </select>
                    
                    <select id="select-profesional">
                        <option value="">Seleccionar profesional (opcional)</option>
                        <?php 
                        if ($lista_profesionales && mysqli_num_rows($lista_profesionales) > 0):
                            mysqli_data_seek($lista_profesionales, 0);
                            while ($profesional = mysqli_fetch_assoc($lista_profesionales)): 
                        ?>
                            <option value="<?php echo $profesional['id_profesionales']; ?>">
                                <?php echo htmlspecialchars($profesional['nombre'] . ' ' . $profesional['apellido']); ?>
                            </option>
                        <?php 
                            endwhile; 
                        endif;
                        ?>
                    </select>
                    
                    <input type="text" id="horario-profesional" placeholder="Horario del profesional (ej: 08:00-12:00)">
                    
                    <div class="botones-panel">
                        <button type="button" class="btn-primario" onclick="agregarPrestacion()">Agregar</button>
                        <button type="button" class="btn-cancelar" onclick="ocultarAgregarPrestacion()">Cancelar</button>
                    </div>
                </div>
                
                <div class="botones-formulario">
                    <button type="submit" name="guardar_centro" class="btn-primario">Guardar Centro</button>
                    <a href="panelAdmin.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
        
        <!-- FORMULARIO PROFESIONAL -->
        <div id="formulario-profesional" class="formulario">
            <h2>Agregar Profesional</h2>
            <form method="post">
                <div class="campo">
                    <label for="nombre_profesional">Nombre:</label>
                    <input type="text" name="nombre_profesional" id="nombre_profesional" required>
                </div>
                
                <div class="campo">
                    <label for="apellido_profesional">Apellido:</label>
                    <input type="text" name="apellido_profesional" id="apellido_profesional" required>
                </div>
                
                <div class="botones-formulario">
                    <button type="submit" name="guardar_profesional" class="btn-primario">Guardar Profesional</button>
                    <a href="panelAdmin.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
        
        <!-- FORMULARIO CAMPAÑA -->
        <div id="formulario-campania" class="formulario">
            <h2>Agregar Campaña</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="campo">
                    <label for="imagen_campania">Subir imagen:</label>
                    <input type="file" name="imagen_campania" id="imagen_campania" accept="image/*">
                </div>
                
                <div class="separador">O</div>
                
                <div class="campo">
                    <label for="nombre_archivo_existente">Nombre de archivo existente:</label>
                    <input type="text" name="nombre_archivo_existente" id="nombre_archivo_existente" 
                           placeholder="Ej: campania_vacunacion.jpg">
                </div>
                
                <div class="botones-formulario">
                    <button type="submit" name="guardar_campania" class="btn-primario">Guardar Campaña</button>
                    <a href="panelAdmin.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/Municipalidad-Necochea-Caps/js/agregar_datos.js"></script>
</body>
</html>

<?php mysqli_close($conexion); ?>