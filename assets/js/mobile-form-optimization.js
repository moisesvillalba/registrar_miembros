// ===== CONFIGURACIÓN GLOBAL =====

// FUNCIÓN GLOBAL DE NOTIFICACIONES (evita duplicados)
function showGlobalNotification(message, type = 'info') {
    let notification = document.getElementById('mobile-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'mobile-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: ${type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
            max-width: 90%;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-weight: 500;
        `;
        document.body.appendChild(notification);
    }

    notification.textContent = message;
    notification.style.background = type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6';
    notification.style.opacity = '1';

    // Limpiar timeout anterior si existe
    if (notification.timeoutId) {
        clearTimeout(notification.timeoutId);
    }

    notification.timeoutId = setTimeout(() => {
        notification.style.opacity = '0';
    }, type === 'error' ? 5000 : 3000); // Errores duran más tiempo
}

// ===== CONFIGURACIÓN GLOBAL =====
const DEVICE_CONFIG = {
    isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
    isLowMemory: navigator.deviceMemory && navigator.deviceMemory <= 2,
    isSlowConnection: navigator.connection && (navigator.connection.effectiveType === '2g' || navigator.connection.effectiveType === '3g'),

    getDeviceType() {
        if (this.isMobile && (this.isLowMemory || this.isSlowConnection)) {
            return 'mobile-basic';
        } else if (this.isMobile) {
            return 'mobile-modern';
        } else {
            return 'desktop';
        }
    }
};

// ===== SISTEMA UNIVERSAL DE FECHAS =====
class UniversalDateInput {
    constructor(fieldName, options = {}) {
        this.fieldName = fieldName;
        this.options = {
            minYear: options.minYear || 1940,
            maxYear: options.maxYear || new Date().getFullYear(),
            maxDate: options.maxDate || new Date().toISOString().split('T')[0],
            ...options
        };

        this.container = document.getElementById(`${fieldName}_methods`);
        this.hiddenInput = document.getElementById(fieldName);
        this.currentMethod = 'native';

        // Verificar que los elementos existan antes de inicializar
        if (this.container && this.hiddenInput) {
            this.init();
        } else {
            console.error(`Elements not found for field: ${fieldName}`);
        }
    }

    init() {
        this.setupDeviceDetection();
        this.setupEventListeners();
        this.updateSelectorsConstraints();
    }

    setupDeviceDetection() {
        const deviceType = DEVICE_CONFIG.getDeviceType();
        this.container.setAttribute('data-device', deviceType);

        // Auto-seleccionar mejor método según dispositivo
        let defaultMethod = 'native';
        if (deviceType === 'mobile-basic') {
            defaultMethod = 'manual';
        } else if (deviceType === 'desktop') {
            defaultMethod = 'selectors';
        }

        // Marcar método recomendado
        const recommendedTab = this.container.querySelector(`[data-method="${defaultMethod}"]`);
        if (recommendedTab) {
            recommendedTab.classList.add('recommended');
        }
    }

    setupEventListeners() {
        // Tabs de métodos
        this.container.querySelectorAll('.date-method-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                this.switchMethod(e.target.dataset.method);
                this.hapticFeedback(e.target);
            });
        });

        // Botones de años rápidos
        this.container.querySelectorAll('.quick-year-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.setQuickYear(parseInt(e.target.dataset.year));
                this.hapticFeedback(e.target);
            });
        });

        // Décadas expandibles
        this.container.querySelectorAll('.decade-header').forEach(header => {
            header.addEventListener('click', (e) => {
                this.toggleDecade(e.currentTarget.dataset.decade);
                this.hapticFeedback(e.currentTarget);
            });
        });

        // Años en décadas
        this.container.querySelectorAll('.decade-year').forEach(year => {
            year.addEventListener('click', (e) => {
                this.setQuickYear(parseInt(e.target.dataset.year));
                this.hapticFeedback(e.target);
            });
        });

        // Input nativo
        const nativeInput = this.container.querySelector('.native-date-input');
        if (nativeInput) {
            nativeInput.addEventListener('change', (e) => {
                this.updateFromNative(e.target.value);
            });
        }

        // Input manual
        const manualInput = this.container.querySelector('.manual-date-input');
        if (manualInput) {
            manualInput.addEventListener('input', (e) => {
                this.formatManualInput(e.target);
                this.validateManualDate(e.target.value);
            });
        }

        // Botón limpiar manual
        const clearBtn = this.container.querySelector('.manual-clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearManualDate();
            });
        }

        // Selectores
        this.container.querySelectorAll('.date-selector').forEach(selector => {
            selector.addEventListener('change', () => {
                this.updateFromSelectors();
            });
        });
    }

    updateSelectorsConstraints() {
        const daySelect = this.container.querySelector('[data-type="day"]');
        const monthSelect = this.container.querySelector('[data-type="month"]');
        const yearSelect = this.container.querySelector('[data-type="year"]');

        if (!daySelect || !monthSelect || !yearSelect) {
            console.warn('Date selectors not found');
            return;
        }

        const updateDays = () => {
            const month = parseInt(monthSelect.value);
            const year = parseInt(yearSelect.value);

            if (month && year) {
                const daysInMonth = new Date(year, month, 0).getDate();
                const currentDay = parseInt(daySelect.value);

                // Limpiar opciones de días
                daySelect.innerHTML = '<option value="">Día</option>';

                // Agregar días válidos
                for (let d = 1; d <= daysInMonth; d++) {
                    const option = document.createElement('option');
                    option.value = d;
                    option.textContent = d.toString().padStart(2, '0');
                    daySelect.appendChild(option);
                }

                // Restaurar día seleccionado si es válido
                if (currentDay && currentDay <= daysInMonth) {
                    daySelect.value = currentDay;
                }
            }
        };

        monthSelect.addEventListener('change', updateDays);
        yearSelect.addEventListener('change', updateDays);
    }

    switchMethod(method) {
        // Ocultar contenido actual
        this.container.querySelectorAll('.date-method-content').forEach(content => {
            content.classList.remove('active');
        });
        this.container.querySelectorAll('.date-method-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Mostrar nuevo contenido
        const newContent = this.container.querySelector(`#${this.fieldName}_${method}`);
        const newTab = this.container.querySelector(`[data-method="${method}"]`);

        if (newContent && newTab) {
            newContent.classList.add('active');
            newTab.classList.add('active');
            this.currentMethod = method;
        }

        this.hidePreview();
    }

    setQuickYear(year) {
        const date = `${year}-01-01`;

        switch (this.currentMethod) {
            case 'native':
                const nativeInput = this.container.querySelector('.native-date-input');
                if (nativeInput) {
                    nativeInput.value = date;
                    this.updateFromNative(date);
                }
                break;

            case 'manual':
                const manualInput = this.container.querySelector('.manual-date-input');
                if (manualInput) {
                    manualInput.value = `01/01/${year}`;
                    this.validateManualDate(manualInput.value);
                }
                break;

            case 'selectors':
                const daySelect = this.container.querySelector('[data-type="day"]');
                const monthSelect = this.container.querySelector('[data-type="month"]');
                const yearSelect = this.container.querySelector('[data-type="year"]');

                if (daySelect && monthSelect && yearSelect) {
                    daySelect.value = '1';
                    monthSelect.value = '1';
                    yearSelect.value = year.toString();
                    this.updateFromSelectors();
                }
                break;
        }

        this.showToast(`Año ${year} seleccionado`);
    }

    toggleDecade(decade) {
        const yearsDiv = this.container.querySelector(`#years-${decade}`);
        const arrow = this.container.querySelector(`[data-decade="${decade}"] .decade-arrow`);

        if (yearsDiv && arrow) {
            yearsDiv.classList.toggle('active');
            arrow.textContent = yearsDiv.classList.contains('active') ? '▲' : '▼';
        }
    }

    formatManualInput(input) {
        let value = input.value.replace(/\D/g, ''); // Solo números

        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }
        if (value.length >= 5) {
            value = value.slice(0, 5) + '/' + value.slice(5, 9);
        }

        input.value = value;
    }

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
        this.form = document.getElementById('registration-form');
        this.formSteps = document.querySelectorAll('.form-step');
        this.progressBar = document.getElementById('progress-bar');
        this.progressSteps = document.querySelectorAll('.progress-step');
        this.nextButtons = document.querySelectorAll('.next-step');
        this.prevButtons = document.querySelectorAll('.prev-step');

        this.init();
    }

    init() {
        this.setupStepNavigation();
        this.updateProgress();
        console.log('✅ FormStepsNavigation inicializado');
    }

    setupStepNavigation() {
        // Botones de pasos en el header
        this.progressSteps.forEach(step => {
            step.addEventListener('click', (e) => {
                e.preventDefault();
                const stepNumber = parseInt(e.currentTarget.dataset.step);
                this.goToStep(stepNumber);
            });
        });

        // Botones next/prev
        this.nextButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const nextStep = parseInt(e.currentTarget.dataset.next);

                // Validar paso actual antes de avanzar
                if (this.validateCurrentStep()) {
                    this.goToStep(nextStep);
                } else {
                    this.showNotification('Por favor, complete todos los campos requeridos.', 'error');
                }
            });
        });

        this.prevButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const prevStep = parseInt(e.currentTarget.dataset.prev);
                this.goToStep(prevStep);
            });
        });
    }

    goToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > this.totalSteps) {
            console.error('Número de paso inválido:', stepNumber);
            return;
        }

        console.log(`Navegando del paso ${this.currentStep} al paso ${stepNumber}`);

        // Ocultar paso actual
        this.formSteps.forEach(step => {
            step.classList.remove('active');
        });

        // Mostrar nuevo paso
        const newStepEl = document.querySelector(`[data-step="${stepNumber}"]`);
        if (newStepEl) {
            newStepEl.classList.add('active');
            console.log(`Paso ${stepNumber} activado`);
        } else {
            console.error(`No se encontró el elemento del paso ${stepNumber}`);
        }

        // Actualizar botones de navegación
        this.progressSteps.forEach(step => {
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
        if (!currentStepEl) {
            console.log('No se encontró el paso actual');
            return true;
        }

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

        console.log(`Validación del paso ${this.currentStep}:`, isValid);
        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('form-control-error');

        // Crear o actualizar mensaje de error
        let errorEl = field.parentNode.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'field-error';
            errorEl.style.cssText = `
                color: #ef4444;
                font-size: 12px;
                margin-top: 4px;
                display: block;
            `;
            field.parentNode.appendChild(errorEl);
        }

        errorEl.textContent = message;
        errorEl.classList.add('show');
    }

    clearFieldError(field) {
        field.classList.remove('form-control-error');
        const errorEl = field.parentNode.querySelector('.field-error');
        if (errorEl) {
            errorEl.remove();
        }
    }

    updateProgress() {
        const percentage = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        if (this.progressBar) {
            this.progressBar.style.width = `${Math.max(0, percentage)}%`;
        }
    }

    showNotification(message, type = 'info') {
        // Crear notificación simple
        let notification = document.getElementById('mobile-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'mobile-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: ${type === 'error' ? '#ef4444' : '#10b981'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s;
                max-width: 90%;
                text-align: center;
            `;
            document.body.appendChild(notification);
        }

        notification.textContent = message;
        notification.style.background = type === 'error' ? '#ef4444' : '#10b981';
        notification.style.opacity = '1';

        setTimeout(() => {
            notification.style.opacity = '0';
        }, 3000);
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

        console.log('✅ OfflineDetection inicializado');
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

            console.log('✅ MobileNavigation inicializado');
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

// ===== MANEJO DE ARCHIVOS =====
class FileHandler {
    constructor() {
        this.fileInputs = document.querySelectorAll('input[type="file"]');
        this.init();
    }

    init() {
        this.fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFileSelect(e));

            // Configurar drag and drop
            const container = input.closest('.file-upload-container');
            if (container) {
                this.setupDragAndDrop(container, input);
            }
        });

        console.log('✅ FileHandler inicializado');
    }

    handleFileSelect(event) {
        const input = event.target;
        const files = input.files;

        // Validar archivos
        const validFiles = [];
        const errors = [];

        Array.from(files).forEach(file => {
            const validation = this.validateFile(file);
            if (validation.valid) {
                validFiles.push(file);
            } else {
                errors.push(`${file.name}: ${validation.message}`);
            }
        });

        if (errors.length > 0) {
            this.showNotification(`Errores en archivos: ${errors.join(', ')}`, 'error');
        }

        // Mostrar vista previa
        this.updateFilePreview(input, validFiles);
        this.updateFileLabel(input, validFiles.length);
    }

    validateFile(file) {
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

    updateFilePreview(input, files) {
        const previewContainer = input.closest('.form-group').querySelector('.file-preview');
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        if (files.length > 0) {
            files.forEach((file, index) => {
                const fileElement = this.createFilePreviewElement(file, index, input);
                previewContainer.appendChild(fileElement);
            });
        }
    }

    createFilePreviewElement(file, index, input) {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-item';
        fileDiv.style.cssText = `
            display: flex;
            align-items: center;
            padding: 8px;
            background: #f3f4f6;
            border-radius: 4px;
            margin: 4px 0;
        `;

        const icon = this.getFileIcon(file.type);

        fileDiv.innerHTML = `
            <div style="display: flex; align-items: center; flex: 1;">
                <i class="fas ${icon}" style="margin-right: 8px; color: #6b7280;"></i>
                <div>
                    <div style="font-size: 12px; font-weight: 500;">${file.name}</div>
                    <div style="font-size: 10px; color: #6b7280;">${this.formatFileSize(file.size)}</div>
                </div>
            </div>
            <button type="button" style="
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            " data-index="${index}">×</button>
        `;

        // Agregar funcionalidad de eliminación
        const removeBtn = fileDiv.querySelector('button');
        removeBtn.addEventListener('click', () => this.removeFile(input, index));

        return fileDiv;
    }

    getFileIcon(mimeType) {
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

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    removeFile(input, index) {
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });

        input.files = dt.files;
        this.handleFileSelect({ target: input });
    }

    updateFileLabel(input, fileCount) {
        const label = input.closest('.file-upload-container').querySelector('.file-label span');
        if (label) {
            label.textContent = fileCount > 0 ?
                `${fileCount} archivo(s) seleccionado(s)` :
                'Seleccionar archivos';
        }
    }

    setupDragAndDrop(container, input) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            container.addEventListener(eventName, () => this.highlight(container), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, () => this.unhighlight(container), false);
        });

        container.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            this.handleFileSelect({ target: input });
        }, false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight(container) {
        container.style.background = '#e5f3ff';
        container.style.borderColor = '#3b82f6';
    }

    unhighlight(container) {
        container.style.background = '';
        container.style.borderColor = '';
    }

    showNotification(message, type = 'info') {
        showGlobalNotification(message, type);
    }
}

// ===== INICIALIZACIÓN PRINCIPAL =====
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Inicializando sistema de formulario optimizado...');

    try {
        // Inicializar navegación móvil
        new MobileNavigation();

        // Inicializar detección offline
        new OfflineDetection();

        // Inicializar navegación de pasos (MUY IMPORTANTE)
        const stepsNavigation = new FormStepsNavigation();

        // Inicializar validación de formulario
        new FormValidation();

        // Inicializar manejo de archivos
        new FileHandler();

        // Inicializar campos de fecha con manejo de errores
        try {
            const fechaNacimiento = new UniversalDateInput('fecha_nacimiento', {
                minYear: 1940,
                maxYear: new Date().getFullYear(),
                maxDate: new Date().toISOString().split('T')[0]
            });
            console.log('✅ Campo fecha de nacimiento inicializado');
        } catch (error) {
            console.warn('⚠️ Error inicializando fecha de nacimiento:', error);
        }

        try {
            const fechaIniciacion = new UniversalDateInput('fecha_iniciacion', {
                minYear: 1950,
                maxYear: new Date().getFullYear(),
                maxDate: new Date().toISOString().split('T')[0]
            });
            console.log('✅ Campo fecha de iniciación inicializado');
        } catch (error) {
            console.warn('⚠️ Error inicializando fecha de iniciación:', error);
        }

        // Configuración específica para móviles
        if (DEVICE_CONFIG.isMobile) {
            console.log('📱 Dispositivo móvil detectado');

            // Agregar clase al body para estilos específicos
            document.body.classList.add('mobile-device');

            // Configurar viewport para móviles
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0');
            }

            // Prevenir zoom en inputs
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.style.fontSize = Math.max(16, parseFloat(window.getComputedStyle(input).fontSize)) + 'px';
                });
            });
        }

        console.log('🎉 Sistema de formulario completamente inicializado');

        // Test de navegación de pasos
        setTimeout(() => {
            console.log('🔍 Verificando navegación de pasos...');
            const firstStep = document.querySelector('[data-step="1"]');
            const nextButton = document.querySelector('.next-step[data-next="2"]');

            if (firstStep && nextButton) {
                console.log('✅ Elementos de navegación encontrados');
                console.log('- Paso 1:', firstStep);
                console.log('- Botón siguiente:', nextButton);
            } else {
                console.error('❌ Elementos de navegación NO encontrados');
                console.log('- Paso 1:', firstStep);
                console.log('- Botón siguiente:', nextButton);
            }
        }, 1000);

    } catch (error) {
        console.error('❌ Error crítico al inicializar el sistema:', error);

        // Fallback básico para navegación
        const nextButtons = document.querySelectorAll('.next-step');
        const prevButtons = document.querySelectorAll('.prev-step');

        nextButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const nextStep = parseInt(this.dataset.next);
                const currentStep = document.querySelector('.form-step.active');
                const targetStep = document.querySelector(`[data-step="${nextStep}"]`);

                if (currentStep && targetStep) {
                    currentStep.classList.remove('active');
                    targetStep.classList.add('active');
                    console.log(`Navegación fallback: paso ${nextStep}`);
                }
            });
        });

        prevButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const prevStep = parseInt(this.dataset.prev);
                const currentStep = document.querySelector('.form-step.active');
                const targetStep = document.querySelector(`[data-step="${prevStep}"]`);

                if (currentStep && targetStep) {
                    currentStep.classList.remove('active');
                    targetStep.classList.add('active');
                    console.log(`Navegación fallback: paso ${prevStep}`);
                }
            });
        });

        console.log('🔧 Sistema de navegación fallback activado');
    }
});

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
            console.log('✅ FormValidation inicializado');
        }
    }

    setupSubmitHandler() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            if (this.validateForm()) {
                this.submitForm();
            } else {
                this.showNotification('Por favor, complete todos los campos correctamente.', 'error');
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
                value = `+ 595(${number.slice(0, 3)})`;
                if (number.length > 3) {
                    value += ` ${number.slice(3, 6)} `;
                    if (number.length > 6) {
                        value += `- ${number.slice(6, 9)} `;
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
            errorEl.style.cssText = `
color: #ef4444;
font - size: 12px;
margin - top: 4px;
display: block;
`;
            field.parentNode.appendChild(errorEl);
        }

        errorEl.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('form-control-error');
        const errorEl = field.parentNode.querySelector('.field-error');
        if (errorEl) {
            errorEl.remove();
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
        showGlobalNotification(message, type);
    }
}   