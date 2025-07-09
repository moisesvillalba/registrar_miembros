/**
 * Sistema de Registro - Scripts principales MEJORADOS
 * Maneja la navegación multipaso, validaciones avanzadas, AJAX y UI interactiva
 */
document.addEventListener('DOMContentLoaded', function () {
    // Referencias de elementos del DOM
    const form = document.getElementById('registration-form');
    const formSteps = document.querySelectorAll('.form-step');
    const progressBar = document.getElementById('progress-bar');
    const progressSteps = document.querySelectorAll('.progress-step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const submitButton = document.getElementById('submit-button');
    const fileInputs = document.querySelectorAll('input[type="file"]');

    // Variables de estado
    let isSubmitting = false;
    let validationErrors = {};

    // Inicializar
    init();

    function init() {
        if (progressBar) {
            updateProgressBar();
        }
        setupEventListeners();
        setupValidation();
        setupFileHandling();
        setupPhoneFormatting();
        setupCIFormatting();
        setupFormSubmission();
    }

    function setupEventListeners() {
        // Navegación entre pasos - MEJORADA
        if (nextButtons.length) {
            nextButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const currentStep = parseInt(this.getAttribute('data-next')) - 1;
                    const nextStep = parseInt(this.getAttribute('data-next'));

                    // Validar campos del paso actual antes de avanzar
                    if (validateStep(currentStep + 1)) {
                        navigateToStep(nextStep);
                        updateProgressBar();
                        markStepAsCompleted(currentStep);
                    } else {
                        showNotification('Por favor, complete todos los campos requeridos correctamente.', 'error');
                        scrollToFirstError();
                    }
                });
            });
        }

        if (prevButtons.length) {
            prevButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const prevStep = parseInt(this.getAttribute('data-prev'));
                    navigateToStep(prevStep);
                    updateProgressBar();
                });
            });
        }

        // Navegación directa a través de los iconos de paso - MEJORADA
        if (progressSteps.length) {
            progressSteps.forEach(step => {
                step.addEventListener('click', function () {
                    const clickedStep = parseInt(this.getAttribute('data-step'));
                    const currentStep = getCurrentStep();

                    // Permitir navegación a pasos anteriores o completados
                    if (clickedStep <= currentStep || this.classList.contains('completed')) {
                        navigateToStep(clickedStep);
                        updateProgressBar();
                    }
                });
            });
        }

        // Toggle para menú móvil
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.getElementById('main-menu');

        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function () {
                navMenu.classList.toggle('active');
                this.setAttribute('aria-expanded',
                    this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
            });
        }

        // Cerrar menú móvil al hacer clic fuera
        document.addEventListener('click', function (e) {
            if (navMenu && navToggle && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function setupValidation() {
        // Validación en tiempo real para todos los campos
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Validar al perder el foco
            input.addEventListener('blur', function () {
                validateField(this);
            });

            // Limpiar errores mientras se escribe
            input.addEventListener('input', function () {
                if (this.classList.contains('form-control-error')) {
                    clearFieldError(this);
                    validateField(this);
                }
            });

            // Validación específica para campos especiales
            if (input.name === 'ci') {
                input.addEventListener('input', function () {
                    // Debounce para verificación de duplicados
                    clearTimeout(this.ciCheckTimeout);
                    this.ciCheckTimeout = setTimeout(() => {
                        if (this.value.length >= 6) {
                            checkCIDuplicate(this.value);
                        }
                    }, 1000);
                });
            }
        });
    }

    function setupFileHandling() {
        if (fileInputs.length) {
            fileInputs.forEach(input => {
                input.addEventListener('change', handleFileSelect);

                // Drag and drop
                const container = input.closest('.file-upload-container');
                if (container) {
                    setupDragAndDrop(container, input);
                }
            });
        }
    }

    function setupDragAndDrop(container, input) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            container.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            container.classList.add('dragover');
        }

        function unhighlight(e) {
            container.classList.remove('dragover');
        }

        container.addEventListener('drop', function (e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            handleFileSelect({ target: input });
        }, false);
    }

    function setupPhoneFormatting() {
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                formatPhoneNumber(e.target);
            });
        });
    }

    function setupCIFormatting() {
        const ciInput = document.getElementById('ci');
        if (ciInput) {
            ciInput.addEventListener('input', function (e) {
                // Solo permitir números y limitar a 8 dígitos
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) {
                    value = value.slice(0, 8);
                }
                e.target.value = value;
            });
        }
    }

    function setupFormSubmission() {
        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                handleFormSubmit();
            });
        }
    }

    // === FUNCIONES DE NAVEGACIÓN ===

    function getCurrentStep() {
        let currentStep = 1;
        formSteps.forEach((step, index) => {
            if (step.classList.contains('active')) {
                currentStep = index + 1;
            }
        });
        return currentStep;
    }

    function navigateToStep(stepNumber) {
        formSteps.forEach((step, index) => {
            step.classList.remove('active');
            if (index + 1 === stepNumber) {
                step.classList.add('active');
            }
        });

        // Actualizar estados de los indicadores de paso
        progressSteps.forEach((step, index) => {
            step.classList.remove('active');

            if (index + 1 === stepNumber) {
                step.classList.add('active');
            }
        });

        // Hacer scroll hacia arriba
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function updateProgressBar() {
        if (!progressBar) return;

        const currentStep = getCurrentStep();
        const totalSteps = formSteps.length;
        const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;

        progressBar.style.width = `${Math.max(0, progressPercentage)}%`;
    }

    function markStepAsCompleted(stepIndex) {
        if (progressSteps[stepIndex]) {
            progressSteps[stepIndex].classList.add('completed');
        }
    }

    // === FUNCIONES DE VALIDACIÓN MEJORADAS ===

    function validateStep(stepNumber) {
        const stepElement = document.querySelector(`[data-step="${stepNumber}"]`);
        if (!stepElement) return false;

        const requiredFields = stepElement.querySelectorAll('[required]');
        let isValid = true;

        // Limpiar errores previos
        clearStepErrors(stepElement);

        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Validaciones específicas por paso
        switch (stepNumber) {
            case 1:
                isValid = validatePersonalData() && isValid;
                break;
            case 3:
                isValid = validateLogialData() && isValid;
                break;
            case 4:
                isValid = validateMedicalData() && isValid;
                break;
        }

        return isValid;
    }

    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        const fieldType = field.type;

        // Campo requerido vacío
        if (field.required && !value) {
            showFieldError(field, 'Este campo es requerido');
            return false;
        }

        if (!value) {
            clearFieldError(field);
            return true; // Campo opcional vacío es válido
        }

        // Validaciones específicas
        switch (fieldName) {
            case 'ci':
                return validateCI(field, value);
            case 'telefono':
            case 'numero_emergencia':
                return validatePhone(field, value);
            case 'fecha_nacimiento':
                return validateBirthDate(field, value);
            case 'fecha_iniciacion':
                return validateInitiationDate(field, value);
            default:
                clearFieldError(field);
                return true;
        }
    }

    function validateCI(field, value) {
        if (!/^[0-9]{6,8}$/.test(value)) {
            showFieldError(field, 'CI debe tener entre 6 y 8 dígitos');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validatePhone(field, value) {
        const cleanPhone = value.replace(/[\s\-\(\)\+]/g, '');
        if (!/^(595[0-9]{9}|0[0-9]{8,9}|[0-9]{9,10})$/.test(cleanPhone)) {
            showFieldError(field, 'Formato de teléfono inválido');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validateBirthDate(field, value) {
        const birthDate = new Date(value);
        const today = new Date();
        const age = (today - birthDate) / (365.25 * 24 * 60 * 60 * 1000);

        if (age < 18) {
            showFieldError(field, 'Debe ser mayor de 18 años');
            return false;
        }

        if (age > 100) {
            showFieldError(field, 'Fecha de nacimiento no válida');
            return false;
        }

        clearFieldError(field);
        return true;
    }

    function validateInitiationDate(field, value) {
        const initiationDate = new Date(value);
        const today = new Date();

        if (initiationDate > today) {
            showFieldError(field, 'La fecha de iniciación no puede ser futura');
            return false;
        }

        // Validar edad mínima en iniciación
        const birthDateField = document.getElementById('fecha_nacimiento');
        if (birthDateField && birthDateField.value) {
            const birthDate = new Date(birthDateField.value);
            const ageAtInitiation = (initiationDate - birthDate) / (365.25 * 24 * 60 * 60 * 1000);

            if (ageAtInitiation < 18) {
                showFieldError(field, 'La edad en la fecha de iniciación debe ser mayor a 18 años');
                return false;
            }
        }

        clearFieldError(field);
        return true;
    }

    function validatePersonalData() {
        // Validaciones adicionales para datos personales
        return true;
    }

    function validateLogialData() {
        // Validar coherencia de fechas
        const birthDate = document.getElementById('fecha_nacimiento').value;
        const initiationDate = document.getElementById('fecha_iniciacion').value;

        if (birthDate && initiationDate) {
            return validateInitiationDate(document.getElementById('fecha_iniciacion'), initiationDate);
        }

        return true;
    }

    function validateMedicalData() {
        // Validaciones específicas para datos médicos
        return true;
    }

    function validateAllFields() {
        let isValid = true;

        // Validar cada paso
        for (let i = 1; i <= formSteps.length; i++) {
            if (!validateStep(i)) {
                isValid = false;
            }
        }

        return isValid;
    }

    // === FUNCIONES DE MANEJO DE ERRORES ===

    function showFieldError(field, message) {
        field.classList.add('form-control-error');

        // Remover mensaje de error previo
        clearFieldError(field, false);

        // Agregar nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;

        // Insertar después del campo o su contenedor
        const container = field.closest('.form-group') || field.parentElement;
        container.appendChild(errorDiv);

        // Agregar a lista de errores
        validationErrors[field.name] = message;
    }

    function clearFieldError(field, removeClass = true) {
        if (removeClass) {
            field.classList.remove('form-control-error');
        }

        // Eliminar mensaje de error
        const container = field.closest('.form-group') || field.parentElement;
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }

        // Remover de lista de errores
        delete validationErrors[field.name];
    }

    function clearStepErrors(stepElement) {
        const errorFields = stepElement.querySelectorAll('.form-control-error');
        const errorMessages = stepElement.querySelectorAll('.error-message');

        errorFields.forEach(field => field.classList.remove('form-control-error'));
        errorMessages.forEach(msg => msg.remove());

        // Limpiar errores de validación del paso
        const stepFields = stepElement.querySelectorAll('input, select, textarea');
        stepFields.forEach(field => {
            delete validationErrors[field.name];
        });
    }

    function scrollToFirstError() {
        const firstError = document.querySelector('.form-control-error');
        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            firstError.focus();
        }
    }

    // === FUNCIONES DE ARCHIVOS MEJORADAS ===

    function handleFileSelect(event) {
        const input = event.target;
        const files = input.files;

        // Validar archivos
        const validFiles = [];
        const errors = [];

        Array.from(files).forEach(file => {
            const validation = validateFile(file);
            if (validation.valid) {
                validFiles.push(file);
            } else {
                errors.push(`${file.name}: ${validation.message}`);
            }
        });

        if (errors.length > 0) {
            showNotification(`Errores en archivos: ${errors.join(', ')}`, 'error');
        }

        // Mostrar vista previa solo de archivos válidos
        updateFilePreview(input, validFiles);
        updateFileLabel(input, validFiles.length);
    }

    function validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            return { valid: false, message: 'Tipo de archivo no permitido (solo JPG, PNG, PDF)' };
        }

        if (file.size > maxSize) {
            return { valid: false, message: 'Archivo demasiado grande (máximo 5MB)' };
        }

        return { valid: true };
    }

    function updateFilePreview(input, files) {
        const previewContainer = input.closest('.form-group').querySelector('.file-preview');
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        if (files.length > 0) {
            files.forEach((file, index) => {
                const fileElement = createFilePreviewElement(file, index);
                previewContainer.appendChild(fileElement);
            });
        }
    }

    function createFilePreviewElement(file, index) {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-item';

        const icon = getFileIcon(file.type);

        fileDiv.innerHTML = `
            <div class="file-info">
                <i class="fas ${icon}"></i>
                <div class="file-details">
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-file-btn" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Agregar funcionalidad de eliminación
        const removeBtn = fileDiv.querySelector('.remove-file-btn');
        removeBtn.addEventListener('click', () => removeFile(index));

        return fileDiv;
    }

    function getFileIcon(mimeType) {
        switch (mimeType) {
            case 'application/pdf':
                return 'fa-file-pdf';
            case 'image/jpeg':
            case 'image/png':
                return 'fa-file-image';
            default:
                return 'fa-file';
        }
    }

    function removeFile(index) {
        const fileInput = document.getElementById('certificados');
        if (!fileInput) return;

        const dt = new DataTransfer();
        Array.from(fileInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });

        fileInput.files = dt.files;
        handleFileSelect({ target: fileInput });
    }

    function updateFileLabel(input, fileCount) {
        const label = input.closest('.file-upload-container').querySelector('.file-label span');
        if (label) {
            label.textContent = fileCount > 0 ?
                `${fileCount} archivo(s) seleccionado(s)` :
                'Seleccionar archivos';
        }
    }

    // === FUNCIONES DE FORMATO ===

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');

        if (value.startsWith('595')) {
            // Formato internacional: +595 XXX XXX XXX
            value = value.slice(0, 12);
            if (value.length > 3) {
                value = '+595 ' + value.slice(3, 6) + ' ' + value.slice(6, 9) + ' ' + value.slice(9);
            } else {
                value = '+595';
            }
        } else if (value.startsWith('0')) {
            // Formato nacional: 0XXX XXX XXX
            value = value.slice(0, 10);
            if (value.length > 4) {
                value = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
            }
        } else if (value.length > 0) {
            // Agregar prefijo nacional automáticamente
            value = '0' + value;
            value = value.slice(0, 10);
            if (value.length > 4) {
                value = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
            }
        }

        input.value = value;
    }

    // === FUNCIONES AJAX ===

    function checkCIDuplicate(ci) {
        fetch('check_duplicate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ci: ci })
        })
            .then(response => response.json())
            .then(data => {
                const ciField = document.getElementById('ci');
                if (data.exists) {
                    showFieldError(ciField, 'Este CI ya está registrado en el sistema');
                } else {
                    clearFieldError(ciField);
                }
            })
            .catch(error => {
                console.error('Error verificando CI:', error);
            });
    }

    function handleFormSubmit() {
        if (isSubmitting) return;

        // Validar todos los campos
        if (!validateAllFields()) {
            showNotification('Por favor, complete todos los campos requeridos correctamente.', 'error');

            // Ir al primer paso con errores
            const firstErrorStep = findFirstStepWithErrors();
            if (firstErrorStep > 0) {
                navigateToStep(firstErrorStep);
                updateProgressBar();
                setTimeout(scrollToFirstError, 300);
            }
            return;
        }

        isSubmitting = true;
        updateSubmitButton(true);

        // Enviar formulario via AJAX
        const formData = new FormData(form);

        fetch('process.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');

                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                } else {
                    showNotification(data.message || 'Error al procesar el formulario', 'error');

                    if (data.errors && Array.isArray(data.errors)) {
                        data.errors.forEach(error => {
                            console.error('Error de validación:', error);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión. Por favor, intente nuevamente.', 'error');
            })
            .finally(() => {
                isSubmitting = false;
                updateSubmitButton(false);
            });
    }

    function findFirstStepWithErrors() {
        for (let i = 1; i <= formSteps.length; i++) {
            const stepElement = document.querySelector(`[data-step="${i}"]`);
            if (stepElement && stepElement.querySelector('.form-control-error')) {
                return i;
            }
        }
        return 0;
    }

    function updateSubmitButton(loading) {
        if (!submitButton) return;

        if (loading) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save"></i> Enviar Formulario';
        }
    }

    // === FUNCIONES DE NOTIFICACIÓN MEJORADAS ===

    function showNotification(message, type = 'info') {
        let container = document.getElementById('notification-container');
        if (!container) {
            container = createNotificationContainer();
        }

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const icon = getNotificationIcon(type);

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${icon}"></i>
                <div class="notification-message">${message}</div>
                <button class="notification-close" aria-label="Cerrar notificación">&times;</button>
            </div>
        `;

        container.appendChild(notification);

        // Mostrar con animación
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Configurar cierre
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            closeNotification(notification);
        });

        // Cerrar automáticamente
        setTimeout(() => {
            if (notification.parentNode) {
                closeNotification(notification);
            }
        }, type === 'error' ? 8000 : 5000);

        return notification;
    }

    function createNotificationContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.setAttribute('aria-live', 'polite');
        document.body.appendChild(container);
        return container;
    }

    function getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            default: return 'fa-info-circle';
        }
    }

    function closeNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // === EFECTOS VISUALES ===

    // Efecto ripple para botones (mantenido de tu versión original)
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;

            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // === UTILIDADES ===

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Guardar datos del formulario en localStorage (opcional)
    function saveFormData() {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem('registro_temporal', JSON.stringify(data));
    }

    // Cargar datos guardados (opcional)
    function loadFormData() {
        const savedData = localStorage.getItem('registro_temporal');
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field && field.type !== 'file') {
                    field.value = data[key];
                }
            });
        }
    }

    // Limpiar datos guardados
    function clearSavedData() {
        localStorage.removeItem('registro_temporal');
    }

    // Guardar datos del formulario periódicamente
    setInterval(() => {
        if (form && !isSubmitting) {
            saveFormData();
        }
    }, 30000); // Cada 30 segundos

    // Cargar datos al inicio
    // loadFormData();
});

validateManualDate(value) {
    const dateRegex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
    const match = value.match(dateRegex);
    const manualInput = this.container.querySelector('.manual-date-input');

    if (!manualInput) return false;

    if (match) {
        const day = parseInt(match[1]);
        const month = parseInt(match[2]);
        const year = parseInt(match[3]);

        // Validar rangos básicos
        if (month >= 1 && month <= 12 &&
            day >= 1 && day <= 31 &&
            year >= this.options.minYear &&
            year <= this.options.maxYear) {

            const date = new Date(year, month - 1, day);
            const maxDate = new Date(this.options.maxDate);

            if (date.getDate() === day &&
                date.getMonth() === month - 1 &&
                date.getFullYear() === year &&
                date <= maxDate) {

                const isoDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                this.updateHiddenInput(isoDate);
                this.updatePreview(isoDate);

                manualInput.classList.remove('date-input-invalid');
                manualInput.classList.add('date-input-valid');
                this.container.classList.remove('invalid');
                this.container.classList.add('valid');
                return true;
            }
        }
    }

    // Fecha inválida
    if (value.length >= 8) {
        manualInput.classList.remove('date-input-valid');
        manualInput.classList.add('date-input-invalid');
        this.container.classList.remove('valid');
        this.container.classList.add('invalid');
        this.showToast('Fecha inválida', 'error');
    }
    this.hidePreview();
    return false;
}

clearManualDate() {
    const manualInput = this.container.querySelector('.manual-date-input');
    if (manualInput) {
        manualInput.value = '';
        manualInput.classList.remove('date-input-valid', 'date-input-invalid');
        this.container.classList.remove('valid', 'invalid');
        this.updateHiddenInput('');
        this.hidePreview();
    }
}

updateFromNative(value) {
    this.updateHiddenInput(value);
    this.updatePreview(value);
}

updateFromSelectors() {
    const daySelect = this.container.querySelector('[data-type="day"]');
    const monthSelect = this.container.querySelector('[data-type="month"]');
    const yearSelect = this.container.querySelector('[data-type="year"]');

    if (!daySelect || !monthSelect || !yearSelect) {
        return;
    }

    const day = daySelect.value;
    const month = monthSelect.value;
    const year = yearSelect.value;

    if (day && month && year) {
        const date = new Date(year, month - 1, day);
        const maxDate = new Date(this.options.maxDate);

        if (date.getDate() == day &&
            date.getMonth() == month - 1 &&
            date.getFullYear() == year &&
            date <= maxDate) {

            const isoDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            this.updateHiddenInput(isoDate);
            this.updatePreview(isoDate);

            // Validación visual
            this.container.querySelectorAll('.date-selector').forEach(select => {
                select.classList.remove('date-input-invalid');
                select.classList.add('date-input-valid');
            });
            this.container.classList.remove('invalid');
            this.container.classList.add('valid');
        } else {
            this.container.querySelectorAll('.date-selector').forEach(select => {
                select.classList.add('date-input-invalid');
            });
            this.container.classList.add('invalid');
            this.showToast('Fecha inválida', 'error');
            this.hidePreview();
        }
    } else {
        this.container.querySelectorAll('.date-selector').forEach(select => {
            select.classList.remove('date-input-invalid', 'date-input-valid');
        });
        this.container.classList.remove('valid', 'invalid');
        this.hidePreview();
    }
}

updateHiddenInput(value) {
    if (this.hiddenInput) {
        this.hiddenInput.value = value;

        // Disparar evento change para validaciones del formulario
        const event = new Event('change', {
            bubbles: true
        });
        this.hiddenInput.dispatchEvent(event);
    }
}

updatePreview(isoDate) {
    if (!isoDate) return;

    const date = new Date(isoDate);
    const preview = this.container.querySelector('.date-preview');

    if (!preview) return;

    const formattedDateEl = preview.querySelector('.formatted-date');
    const ageInfoEl = preview.querySelector('.age-info');

    if (!formattedDateEl || !ageInfoEl) return;

    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    const formattedDate = date.toLocaleDateString('es-ES', options);
    formattedDateEl.textContent = formattedDate;

    if (this.fieldName === 'fecha_nacimiento') {
        const age = this.calculateAge(date);
        ageInfoEl.textContent = `Edad: ${age} años`;
    } else if (this.fieldName === 'fecha_iniciacion') {
        const yearsAgo = this.calculateYearsAgo(date);
        ageInfoEl.textContent = `Hace ${yearsAgo} años`;
    }

    preview.classList.add('show');
}

hidePreview() {
    const preview = this.container.querySelector('.date-preview');
    if (preview) {
        preview.classList.remove('show');
    }
}

calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age;
}

calculateYearsAgo(date) {
    const today = new Date();
    let years = today.getFullYear() - date.getFullYear();
    const monthDiff = today.getMonth() - date.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
        years--;
    }

    return Math.max(0, years);
}

hapticFeedback(element) {
    // Feedback visual
    element.classList.add('haptic-feedback');
    setTimeout(() => {
        element.classList.remove('haptic-feedback');
    }, 100);

    // Vibración real en dispositivos compatibles
    if (navigator.vibrate) {
        navigator.vibrate(50);
    }
}

showToast(message, type = 'info') {
    // Crear toast si no existe
    let toast = document.getElementById('date-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'date-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        `;
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.background = type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#1f2937';
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
    }, 2000);
}
}

// ===== SISTEMA DE NAVEGACIÓN DE PASOS =====
class FormStepsNavigation {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.init();
    }

    init() {
        this.setupStepNavigation();
        this.setupProgressBar();
        this.updateProgress();
    }

    setupStepNavigation() {
        // Botones de pasos en el header
        document.querySelectorAll('.progress-step').forEach(step => {
            step.addEventListener('click', (e) => {
                const stepNumber = parseInt(e.currentTarget.dataset.step);
                this.goToStep(stepNumber);
            });
        });

        // Botones next/prev
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const nextStep = parseInt(e.currentTarget.dataset.next);
                this.goToStep(nextStep);
            });
        });

        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const prevStep = parseInt(e.currentTarget.dataset.prev);
                this.goToStep(prevStep);
            });
        });
    }

    setupProgressBar() {
        this.progressBar = document.getElementById('progress-bar');
    }

    goToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > this.totalSteps) return;

        // Validar paso actual antes de avanzar
        if (stepNumber > this.currentStep && !this.validateCurrentStep()) {
            return;
        }

        // Ocultar paso actual
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"]`);
        if (currentStepEl) {
            currentStepEl.classList.remove('active');
        }

        // Mostrar nuevo paso
        const newStepEl = document.querySelector(`[data-step="${stepNumber}"]`);
        if (newStepEl) {
            newStepEl.classList.add('active');
        }

        // Actualizar botones de navegación
        document.querySelectorAll('.progress-step').forEach(step => {
            step.classList.remove('active');
        });

        const newProgressStep = document.querySelector(`.progress-step[data-step="${stepNumber}"]`);
        if (newProgressStep) {
            newProgressStep.classList.add('active');
        }

        this.currentStep = stepNumber;
        this.updateProgress();

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    validateCurrentStep() {
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"].active`);
        if (!currentStepEl) return true;

        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo es obligatorio');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('form-control-error');

        // Crear o actualizar mensaje de error
        let errorEl = field.parentNode.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'field-error';
            field.parentNode.appendChild(errorEl);
        }

        errorEl.textContent = message;
        errorEl.classList.add('show');
    }

    clearFieldError(field) {
        field.classList.remove('form-control-error');
        const errorEl = field.parentNode.querySelector('.field-error');
        if (errorEl) {
            errorEl.classList.remove('show');
        }
    }

    updateProgress() {
        const percentage = (this.currentStep / this.totalSteps) * 100;
        if (this.progressBar) {
            this.progressBar.style.width = `${percentage}%`;
        }
    }
}

// ===== SISTEMA DE VALIDACIÓN DE FORMULARIO =====
class FormValidation {
    constructor() {
        this.form = document.getElementById('registration-form');
        this.init();
    }

    init() {
        if (this.form) {
            this.setupSubmitHandler();
            this.setupRealTimeValidation();
        }
    }

    setupSubmitHandler() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            if (this.validateForm()) {
                this.submitForm();
            }
        });
    }

    setupRealTimeValidation() {
        // Validación en tiempo real para campos específicos
        this.form.querySelectorAll('input[type="text"], input[type="tel"], input[type="email"]').forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });

        // Formateo automático para teléfono
        this.form.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', (e) => {
                this.formatPhoneNumber(e.target);
            });
        });

        // Validación de CI
        const ciInput = this.form.querySelector('#ci');
        if (ciInput) {
            ciInput.addEventListener('input', (e) => {
                this.formatCI(e.target);
            });
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        // Validar campos requeridos
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'Este campo es obligatorio';
        }
        // Validar CI
        else if (field.id === 'ci' && value && !/^\d{6,8}$/.test(value)) {
            isValid = false;
            message = 'El CI debe tener entre 6 y 8 dígitos';
        }
        // Validar teléfono
        else if (field.type === 'tel' && value && !/^\+?595\s?\(?\d{2,3}\)?\s?\d{3}-?\d{3}$/.test(value)) {
            isValid = false;
            message = 'Formato de teléfono inválido';
        }
        // Validar email
        else if (field.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            isValid = false;
            message = 'Formato de email inválido';
        }

        if (isValid) {
            this.clearFieldError(field);
        } else {
            this.showFieldError(field, message);
        }

        return isValid;
    }

    formatCI(input) {
        let value = input.value.replace(/\D/g, ''); // Solo números
        if (value.length > 8) {
            value = value.slice(0, 8);
        }
        input.value = value;
    }

    formatPhoneNumber(input) {
        let value = input.value.replace(/[^\d+]/g, ''); // Solo números y +

        // Formato paraguayo: +595 (9XX) XXX-XXX
        if (value.startsWith('595')) {
            value = '+' + value;
        }
        if (value.startsWith('+595') && value.length > 4) {
            const number = value.slice(4);
            if (number.length >= 3) {
                value = `+595 (${number.slice(0, 3)})`;
                if (number.length > 3) {
                    value += ` ${number.slice(3, 6)}`;
                    if (number.length > 6) {
                        value += `-${number.slice(6, 9)}`;
                    }
                }
            }
        }

        input.value = value;
    }

    showFieldError(field, message) {
        field.classList.add('form-control-error');

        let errorEl = field.parentNode.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'field-error';
            field.parentNode.appendChild(errorEl);
        }

        errorEl.textContent = message;
        errorEl.classList.add('show');
    }

    clearFieldError(field) {
        field.classList.remove('form-control-error');
        const errorEl = field.parentNode.querySelector('.field-error');
        if (errorEl) {
            errorEl.classList.remove('show');
        }
    }

    validateForm() {
        let isValid = true;
        const requiredFields = this.form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    submitForm() {
        // Mostrar loading
        this.showLoading();

        // Crear FormData
        const formData = new FormData(this.form);

        // Enviar formulario
        fetch(this.form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                this.hideLoading();
                // Mostrar respuesta en una nueva ventana o redirigir
                const newWindow = window.open('', '_blank');
                newWindow.document.write(data);
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error:', error);
                this.showNotification('Error al enviar el formulario. Por favor, intente nuevamente.', 'error');
            });
    }

    showLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('show');
        }
    }

    hideLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.remove('show');
        }
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

        container.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);

        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
}

// ===== SISTEMA DE DETECCIÓN OFFLINE =====
class OfflineDetection {
    constructor() {
        this.init();
    }

    init() {
        window.addEventListener('online', () => {
            this.hideOfflineIndicator();
        });

        window.addEventListener('offline', () => {
            this.showOfflineIndicator();
        });

        // Verificar estado inicial
        if (!navigator.onLine) {
            this.showOfflineIndicator();
        }
    }

    showOfflineIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            indicator.classList.add('show');
        }
    }

    hideOfflineIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            indicator.classList.remove('show');
        }
    }
}

// ===== NAVEGACIÓN MÓVIL =====
class MobileNavigation {
    constructor() {
        this.navToggle = document.querySelector('.nav-toggle');
        this.navMenu = document.querySelector('.nav-menu');
        this.init();
    }

    init() {
        if (this.navToggle && this.navMenu) {
            this.navToggle.addEventListener('click', () => {
                this.toggleMenu();
            });

            // Cerrar menú al hacer clic en un enlace
            this.navMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    this.closeMenu();
                });
            });
        }
    }

    toggleMenu() {
        this.navMenu.classList.toggle('active');
        const isOpen = this.navMenu.classList.contains('active');
        this.navToggle.setAttribute('aria-expanded', isOpen);
    }

    closeMenu() {
        this.navMenu.classList.remove('active');
        this.navToggle.setAttribute('aria-expanded', 'false');
    }
}

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Inicializando sistema de formulario optimizado...');

    try {
        // Inicializar navegación móvil
        new MobileNavigation();
        console.log('✅ Navegación móvil inicializada');

        // Inicializar detección offline
        new OfflineDetection();
        console.log('✅ Detección offline inicializada');

        // Inicializar navegación de pasos
        new FormStepsNavigation();
        console.log('✅ Navegación de pasos inicializada');

        // Inicializar validación de formulario
        new FormValidation();
        console.log('✅ Validación de formulario inicializada');

        // Inicializar campos de fecha con manejo de errores
        const fechaNacimiento = new UniversalDateInput('fecha_nacimiento', {
            minYear: 1940,
            maxYear: new Date().getFullYear(),
            maxDate: new Date().toISOString().split('T')[0]
        });
        console.log('✅ Campo fecha de nacimiento inicializado');

        const fechaIniciacion = new UniversalDateInput('fecha_iniciacion', {
            minYear: 1950,
            maxYear: new Date().getFullYear(),
            maxDate: new Date().toISOString().split('T')[0]
        });
        console.log('✅ Campo fecha de iniciación inicializado');

        console.log('🎉 Sistema de formulario completamente inicializado');

    } catch (error) {
        console.error('❌ Error al inicializar el sistema:', error);
    }
});