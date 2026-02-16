/* ========================================
   GASTOS APP - JavaScript Principal
   ======================================== */

// Esperar a que el DOM esté listo
function initializeApp() {
    // Aplicar tema antes de cualquier ajuste visual
    initializeThemeToggle();

    // Inicializar SweetAlert2
    initializeSweetAlert();
    
    // Inicializar DataTables
    initializeDataTables();
    
    // Inicializar Select2 (si jQuery está disponible)
    if (typeof $ !== 'undefined') {
        initializeSelect2();
    }
    
    // Inicializar tooltips de Bootstrap
    initializeBootstrapTooltips();
    
    // Manejar confirmaciones de eliminación
    handleDeleteConfirmations();
    
    // Manejar acciones con modales
    handleModalActions();

    // Manejar mostrar/ocultar password
    initializePasswordToggles();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

/**
 * Inicializar SweetAlert2
 */
function initializeSweetAlert() {
    if (typeof Swal === 'undefined') {
        return;
    }
    
    // Configuraciones por defecto de SweetAlert
    Swal.mixin({
        allowOutsideClick: false,
        didOpen: function(modal) {
            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    Swal.close();
                }
            });
        }
    });
}

/**
 * Inicializar DataTables
 */
function initializeDataTables() {
    if (typeof $ === 'undefined' || typeof $.fn.dataTable === 'undefined') {
        return; // DataTables no está cargado
    }
    
    // Buscar todas las tablas con clase "data-table"
    if ($('.data-table').length > 0) {
        $('.data-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            responsive: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            paging: true,
            pageLength: 10,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
    }
}

/**
 * Inicializar Select2 para multi-selects elegantes
 */
function initializeSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        return; // Select2 no está cargado
    }
    
    // Inicializar todos los selects con clase "select2-multiple"
    $('.select2-multiple').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        language: 'es',
        placeholder: 'Selecciona una o más opciones...',
        containerCssClass: 'select2-lg'
    });
    
    // Inicializar todos los selects con clase "select2-single"
    $('.select2-single').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        language: 'es',
        placeholder: 'Selecciona una opción...',
        containerCssClass: 'select2-lg'
    });
}

/**
 * Inicializar tooltips de Bootstrap 5
 */
function initializeBootstrapTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Manejar confirmaciones de eliminación
 */
function handleDeleteConfirmations() {
    var deleteButtons = document.querySelectorAll('[data-action="delete"]');
    
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            var confirmDelete = confirm('¿Está seguro de que desea proseguir?');
            if (confirmDelete) {
                // Enviar formulario o hacer petición
                var form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    });
}

/**
 * Manejar acciones con modales
 */
function handleModalActions() {
    var modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    
    modalTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            // Lógica adicional si es necesaria
        });
    });
}

/**
 * Inicializar toggles de password
 */
function initializePasswordToggles() {
    var toggles = document.querySelectorAll('[data-password-toggle]');

    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var targetId = this.getAttribute('data-password-toggle');
            var input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            var isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.textContent = isHidden ? 'Ocultar' : 'Mostrar';
            this.setAttribute('aria-label', isHidden ? 'Ocultar password' : 'Mostrar password');
        });
    });
}

/**
 * Tema oscuro/claro con cookie
 */
function initializeThemeToggle() {
    var toggleButton = document.getElementById('theme-toggle');
    var savedTheme = getCookieValue('theme');
    var theme = savedTheme || 'dark';

    // Solo aplicar si el tema no está configurado (el inline script ya lo aplicó)
    var currentTheme = document.documentElement.getAttribute('data-theme');
    if (!currentTheme) {
        applyTheme(theme);
    } else {
        // Solo actualizar el icono del botón
        updateThemeIcon(currentTheme);
    }

    if (!toggleButton) {
        return;
    }

    toggleButton.addEventListener('click', function() {
        var nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
        setCookieValue('theme', nextTheme, 365);
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    updateThemeIcon(theme);
}

function updateThemeIcon(theme) {
    var toggleButton = document.getElementById('theme-toggle');
    if (!toggleButton) {
        return;
    }

    var icon = toggleButton.querySelector('i');
    if (icon) {
        icon.classList.toggle('fa-moon', theme === 'light');
        icon.classList.toggle('fa-sun', theme === 'dark');
    }

    toggleButton.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro');
}

function setCookieValue(name, value, days) {
    var expires = '';
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
}

function getCookieValue(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}

/**
 * Función auxiliar para mostrar notificaciones con SweetAlert
 * @param {string} title - Título de la notificación
 * @param {string} message - Mensaje de la notificación
 * @param {string} type - Tipo: 'success', 'error', 'warning', 'info'
 */
function showNotification(title, message, type = 'info') {
    if (typeof Swal === 'undefined') {
        alert(title + ': ' + message);
        return;
    }
    
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonText: 'OK',
        confirmButtonColor: '#0d6efd'
    });
}

/**
 * Función auxiliar para confirmación con SweetAlert
 * @param {string} title - Título
 * @param {string} message - Mensaje
 * @param {function} onConfirm - Callback si confirma
 */
function showConfirmation(title, message, onConfirm) {
    if (typeof Swal === 'undefined') {
        if (confirm(title + ': ' + message)) {
            onConfirm();
        }
        return;
    }
    
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
}

/**
 * Función para activar/desactivar estados (toggle) con AJAX
 * @param {string} url - URL del endpoint POST
 * @param {string} actionName - Nombre de la acción para mostrar en confirmación
 */
function toggleState(url, actionName = 'cambiar estado') {
    if (typeof Swal === 'undefined') {
        // Fallback si SweetAlert no está disponible
        if (confirm('¿Está seguro de que desea ' + actionName + '?')) {
            fetch(url, { method: 'POST' })
                .then(() => location.reload())
                .catch(err => alert('Error: ' + err));
        }
        return;
    }
    
    Swal.fire({
        title: '¿Cambiar estado?',
        text: '¿Está seguro de que desea ' + actionName + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar petición AJAX POST
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la petición');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.message || 'Estado actualizado correctamente',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        // Recargar la página o actualizar tabla
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo actualizar el estado',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error inesperado',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

/**
 * Función para eliminar con confirmación
 * @param {string} url - URL del endpoint
 * @param {number} id - ID del elemento
 */
function deleteItem(url, id) {
    if (typeof Swal === 'undefined') {
        var confirmDelete = confirm('¿Está seguro de que desea eliminar este elemento?');
        if (confirmDelete) {
            window.location.href = url + '?id=' + id;
        }
        return;
    }
    
    Swal.fire({
        title: 'Eliminar',
        text: '¿Está seguro? Esta acción no puede deshacerse.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url + '?id=' + id;
        }
    });
}
