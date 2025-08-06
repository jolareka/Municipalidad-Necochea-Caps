// Inicializar la página
document.addEventListener('DOMContentLoaded', function() {
    mostrarFormulario();
    ocultarMensajeDespuesDeTiempo();
});

// Mostrar/ocultar formularios según la selección
function mostrarFormulario() {
    const tipoSeleccionado = document.getElementById('tipo-formulario').value;
    
    // Ocultar todos los formularios
    document.getElementById('formulario-centro').classList.remove('activo');
    document.getElementById('formulario-profesional').classList.remove('activo');
    document.getElementById('formulario-campania').classList.remove('activo');
    
    // Mostrar el formulario seleccionado
    switch(tipoSeleccionado) {
        case 'centro':
            document.getElementById('formulario-centro').classList.add('activo');
            break;
        case 'profesional':
            document.getElementById('formulario-profesional').classList.add('activo');
            break;
        case 'campania':
            document.getElementById('formulario-campania').classList.add('activo');
            break;
    }
}

// Mostrar/ocultar selector de campaña
function toggleCampania() {
    const checkbox = document.getElementById('tiene_campania');
    const selector = document.getElementById('campania_seleccionada');
    
    if (checkbox.checked) {
        selector.style.display = 'inline-block';
    } else {
        selector.style.display = 'none';
        selector.value = '';
    }
}

// Mostrar panel para agregar prestación
function mostrarAgregarPrestacion() {
    document.getElementById('panel-prestacion').classList.remove('panel-oculto');
    document.querySelector('.btn-secundario').style.display = 'none';
}

// Ocultar panel para agregar prestación
function ocultarAgregarPrestacion() {
    document.getElementById('panel-prestacion').classList.add('panel-oculto');
    document.querySelector('.btn-secundario').style.display = 'inline-block';
    
    // Limpiar campos
    document.getElementById('select-prestacion').value = '';
    document.getElementById('select-profesional').value = '';
    document.getElementById('horario-profesional').value = '';
}

// Agregar prestación a la lista
function agregarPrestacion() {
    const selectPrestacion = document.getElementById('select-prestacion');
    const selectProfesional = document.getElementById('select-profesional');
    const horarioProfesional = document.getElementById('horario-profesional').value;
    const listaPrestaciones = document.getElementById('lista-prestaciones');
    
    // Validar que se haya seleccionado una prestación
    if (!selectPrestacion.value) {
        alert('Por favor, seleccione una prestación');
        return;
    }
    
    // Obtener textos de las opciones seleccionadas
    const nombrePrestacion = selectPrestacion.options[selectPrestacion.selectedIndex].text;
    const nombreProfesional = selectProfesional.value ? 
        selectProfesional.options[selectProfesional.selectedIndex].text : 
        'Sin profesional asignado';
    
    // Ocultar mensaje de "sin prestaciones" si existe
    const sinPrestaciones = listaPrestaciones.querySelector('.sin-prestaciones');
    if (sinPrestaciones) {
        sinPrestaciones.remove();
    }
    
    // Crear elemento de prestación
    const divPrestacion = document.createElement('div');
    divPrestacion.className = 'prestacion-item';
    
    // Construir información de la prestación
    let infoPrestacion = nombrePrestacion;
    if (selectProfesional.value) {
        infoPrestacion += ` - ${nombreProfesional}`;
        if (horarioProfesional) {
            infoPrestacion += ` (${horarioProfesional})`;
        }
    }
    
    divPrestacion.innerHTML = `
        <input type="hidden" name="prestaciones[]" value="${selectPrestacion.value}">
        <input type="hidden" name="profesional_prestacion[]" value="${selectProfesional.value || ''}">
        <input type="hidden" name="horario_profesional[]" value="${horarioProfesional}">
        <div class="prestacion-info">${infoPrestacion}</div>
        <button type="button" onclick="eliminarPrestacion(this)">Eliminar</button>
    `;
    
    // Agregar a la lista
    listaPrestaciones.appendChild(divPrestacion);
    
    // Ocultar panel y limpiar campos
    ocultarAgregarPrestacion();
}

// Eliminar prestación de la lista
function eliminarPrestacion(boton) {
    const prestacionItem = boton.parentNode;
    const listaPrestaciones = document.getElementById('lista-prestaciones');
    
    prestacionItem.remove();
    
    // Si no quedan prestaciones, mostrar mensaje
    if (listaPrestaciones.children.length === 0) {
        listaPrestaciones.innerHTML = '<p class="sin-prestaciones">No hay prestaciones asignadas</p>';
    }
}

// Ocultar mensaje después de un tiempo
function ocultarMensajeDespuesDeTiempo() {
    const mensaje = document.querySelector('.mensaje');
    if (mensaje) {
        setTimeout(function() {
            mensaje.style.opacity = '0';
            mensaje.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                mensaje.style.display = 'none';
            }, 300);
        }, 3500);
    }
}

// Validaciones del formulario
function validarFormularioCentro() {
    const nombre = document.getElementById('nombre_centro').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const coordenadas = document.getElementById('coordenadas').value.trim();
    const horario = document.getElementById('horario').value;
    const telefono = document.getElementById('telefono').value.trim();
    
    if (!nombre || !descripcion || !coordenadas || !horario || !telefono) {
        alert('Por favor, complete todos los campos obligatorios');
        return false;
    }
    
    // Validar formato de coordenadas (básico)
    const coordenadasRegex = /^-?\d+\.?\d*,\s*-?\d+\.?\d*$/;
    if (!coordenadasRegex.test(coordenadas)) {
        alert('Por favor, ingrese las coordenadas en formato: latitud, longitud (ej: -38.5555, -58.7389)');
        return false;
    }
    
    // Validar teléfono (solo números)
    if (!/^\d+$/.test(telefono)) {
        alert('El teléfono debe contener solo números');
        return false;
    }
    
    return true;
}

function validarFormularioProfesional() {
    const nombre = document.getElementById('nombre_profesional').value.trim();
    const apellido = document.getElementById('apellido_profesional').value.trim();
    
    if (!nombre || !apellido) {
        alert('Por favor, complete el nombre y apellido del profesional');
        return false;
    }
    
    return true;
}

function validarFormularioCampania() {
    const archivoSubido = document.getElementById('imagen_campania').files.length > 0;
    const nombreArchivo = document.getElementById('nombre_archivo_existente').value.trim();
    
    if (!archivoSubido && !nombreArchivo) {
        alert('Por favor, suba una imagen o especifique un nombre de archivo existente');
        return false;
    }
    
    return true;
}

// Agregar event listeners para validación
document.addEventListener('DOMContentLoaded', function() {
    // Validación para formulario de centro
    const formCentro = document.querySelector('#formulario-centro form');
    if (formCentro) {
        formCentro.addEventListener('submit', function(e) {
            if (!validarFormularioCentro()) {
                e.preventDefault();
            }
        });
    }
    
    // Validación para formulario de profesional
    const formProfesional = document.querySelector('#formulario-profesional form');
    if (formProfesional) {
        formProfesional.addEventListener('submit', function(e) {
            if (!validarFormularioProfesional()) {
                e.preventDefault();
            }
        });
    }
    
    // Validación para formulario de campaña
    const formCampania = document.querySelector('#formulario-campania form');
    if (formCampania) {
        formCampania.addEventListener('submit', function(e) {
            if (!validarFormularioCampania()) {
                e.preventDefault();
            }
        });
    }
});

// Formatear teléfono mientras se escribe
function formatearTelefono(input) {
    // Remover todo excepto números
    let valor = input.value.replace(/\D/g, '');
    
    // Limitar a 10 dígitos
    if (valor.length > 10) {
        valor = valor.substring(0, 10);
    }
    
    input.value = valor;
}

// Agregar event listener para formatear teléfono
document.addEventListener('DOMContentLoaded', function() {
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function() {
            formatearTelefono(this);
        });
    }
});

// Confirmar antes de eliminar prestación
function eliminarPrestacion(boton) {
    if (confirm('¿Está seguro de que desea eliminar esta prestación?')) {
        const prestacionItem = boton.parentNode;
        const listaPrestaciones = document.getElementById('lista-prestaciones');
        
        prestacionItem.remove();
        
        // Si no quedan prestaciones, mostrar mensaje
        if (listaPrestaciones.children.length === 0) {
            listaPrestaciones.innerHTML = '<p class="sin-prestaciones">No hay prestaciones asignadas</p>';
        }
    }
}

// Función para mostrar preview de imagen
function mostrarPreviewImagen(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (!preview) {
                const img = document.createElement('img');
                img.id = previewId;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.marginTop = '10px';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '5px';
                input.parentNode.appendChild(img);
            }
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else if (preview) {
        preview.remove();
    }
}

// Agregar event listeners para preview de imágenes
document.addEventListener('DOMContentLoaded', function() {
    const imagenInput = document.getElementById('imagen');
    const imagenCampaniaInput = document.getElementById('imagen_campania');
    
    if (imagenInput) {
        imagenInput.addEventListener('change', function() {
            mostrarPreviewImagen(this, 'preview-imagen');
        });
    }
    
    if (imagenCampaniaInput) {
        imagenCampaniaInput.addEventListener('change', function() {
            mostrarPreviewImagen(this, 'preview-campania');
        });
    }
});
        