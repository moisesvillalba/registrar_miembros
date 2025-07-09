<?php
// index.php - Formulario de Scroll Infinito Optimizado para Móviles
// Sistema de Registro Masónico - Gran Logia del Paraguay
session_start();

// Configuración para PRODUCCIÓN
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Incluir database con manejo de errores
$database_loaded = false;
$database_status = '';
$total_registros = 0;

try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn !== null) {
        $database_loaded = true;
        $database_status = 'Sistema conectado correctamente';

        // Obtener estadísticas
        $stmt = $conn->query("SELECT COUNT(*) as total FROM miembros");
        $result = $stmt->fetch();
        $total_registros = $result['total'];
    } else {
        $database_loaded = false;
        $database_status = 'Error de conexión a la base de datos';
    }
} catch (Exception $e) {
    error_log("Error cargando database: " . $e->getMessage());
    $database_loaded = false;
    $database_status = 'Error al cargar configuración de base de datos';
}

// Lista de logias masónicas del Paraguay
$logias = [
    "A∴R∴L∴ Nueva Alianza Nº 1",
    "A∴R∴L∴ Renacer Nº 2",
    "A∴R∴L∴ Hermandad Blanca Nº 3",
    "A∴R∴L∴ Apocalipsis Nº 4",
    "A∴R∴L∴ Reconquista Nº 5",
    "A∴R∴L∴ Samael Nº 6",
    "A∴R∴L∴ Cosmos Nº 7",
    "A∴R∴L∴ Phoenix Nº 8",
    "A∴R∴L∴ 777 Nº 9",
    "A∴R∴L∴ Orden de Melquisedec Nº 10",
    "A∴R∴L∴ Hermética Nº 11",
    "A∴R∴L∴ 666 Lucero del Alba Nº 12",
    "A∴R∴L∴ Eligio Ayala Nº 13",
    "A∴R∴L∴ Pitágoras Nº 14",
    "A∴R∴L∴ Lucero del Norte Nº 15",
    "A∴R∴L∴ Priorato del Amambay Nº 17",
    "A∴R∴L∴ Giordano Bruno Nº 18",
    "A∴R∴L∴ Génesis Nº 19",
    "A∴R∴T∴ Nous Nº 20",
    "A∴R∴L∴ Libertad Nº 21",
    "A∴R∴L∴ Osiris Nº 22",
    "A∴R∴L∴ Ñamandú Nº 23",
    "A∴R∴L∴ Templarios Nº 25",
    "A∴R∴L∴ Delfos Nº 26",
    "A∴R∴L∴ Labor y Constancia Nº 32",
    "A∴R∴L∴ Bernardino Caballero Nº 33",
    "A∴R∴L∴ Fernando de la Mora Nº 37",
    "A∴R∴L∴ Seneca Nº 65",
    "A∴R∴L∴ Horus Nº 72",
    "A∴R∴L∴ Zeus Nº 73",
    "A∴R∴L∴ Yguazú Nº 77",
    "A∴R∴L∴ Alejandría Nº 79",
    "A∴R∴L∴ Alan Kardec Nº 97",
    "A∴R∴L∴ Nueva Alianza del Sur Nº 101",
    "A∴R∴L∴ Concordia Nº 111",
    "A∴R∴L∴ Caballeros de la Orden de Escocia Nº 123",
    "A∴R∴T∴ Orden Pitagórica Nº 314",
    "A∴R∴L∴ Alquimia Nº 357",
    "A∴R∴L∴ Caballeros del Templo Nº 358",
    "A∴R∴L∴ Hermandad Pitagórica Nº 531",
    "A∴R∴L∴ Paraná Nº 717",
    "A∴R∴L∴ Orden del Temple Nº 775",
    "A∴R∴L∴ Caballeros de la Luz"
];

// Ciudades comunes del Paraguay
$ciudades = [
    "Asunción",
    "Ciudad del Este",
    "Encarnación",
    "San Lorenzo",
    "Luque",
    "Capiatá",
    "Lambaré",
    "Fernando de la Mora",
    "Mariano Roque Alonso",
    "Villa Elisa",
    "Ñemby",
    "San Antonio",
    "Limpio",
    "Itauguá",
    "Areguá"
];

// Configurar fechas para validación
$fecha_actual = date('Y-m-d');
$fecha_minima_nacimiento = '1940-01-01';
$fecha_minima_iniciacion = '1980-01-01';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <meta name="description" content="Sistema de registro integral para miembros masónicos. Formulario optimizado para dispositivos móviles.">
    <meta name="theme-color" content="#8B0000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Registro Masónico | Gran Logia del Paraguay</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- Font Awesome -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="assets/images/logo_header_100x100_lg.png">

    <!-- Estilos adicionales para scroll infinito -->
    <style>
        /* ===== OVERRIDES PARA SCROLL INFINITO ===== */
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 1rem;
            padding-bottom: 120px;
        }

        .form-container {
            padding: 0;
        }

        .section {
            background: white;
            margin: 1.5rem 0;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            animation: slideInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .section:nth-child(1) {
            animation-delay: 0.1s;
        }

        .section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .section:nth-child(3) {
            animation-delay: 0.3s;
        }

        .section:nth-child(4) {
            animation-delay: 0.4s;
        }

        .section:nth-child(5) {
            animation-delay: 0.5s;
        }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Barra de progreso pegajosa */
        .progress-sticky {
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .progress-text {
            font-weight: 500;
            color: #6b7280;
        }

        .progress-percentage {
            font-weight: 600;
            color: var(--primary-color);
        }

        .progress-bar-container {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 4px;
            width: 0%;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Botón de envío fijo */
        .submit-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            z-index: 1000;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
        }

        .submit-btn {
            width: 100%;
            background: var(--success-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 56px;
        }

        .submit-btn:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Notificaciones móviles */
        .notification {
            position: fixed;
            top: 20px;
            left: 1rem;
            right: 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            padding: 1rem;
            transform: translateY(-100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.warning {
            border-left: 4px solid #f59e0b;
        }

        /* Estados de campos */
        .field-completed {
            border-color: #10b981 !important;
            background: #f0fdf4;
        }

        .field-error {
            border-color: #ef4444 !important;
            background: #fef2f2;
        }

        /* Optimizaciones móviles adicionales */
        @media (max-width: 640px) {
            .container {
                padding: 0 0.5rem;
                padding-bottom: 120px;
            }

            .section {
                margin: 1rem 0;
                border-radius: 8px;
            }

            .form-row {
                flex-direction: column;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .progress-sticky {
                padding: 0.75rem;
            }
        }

        /* Prevenir zoom en inputs iOS */
        input,
        select,
        textarea {
            font-size: 16px !important;
            transform: translateZ(0);
        }

        /* Loading states */
        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Barra de navegación -->
    <nav class="nav-container">
        <div class="logo">
            <img src="assets/images/logo_header_100x100_lg.png" alt="Logo Masónico" width="40" height="40">
            <a href="index.php">G∴L∴ de L∴ y A∴M∴ del Paraguay</a>
        </div>
        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-menu" id="main-menu">
            <li><a href="index.php" class="active" aria-current="page">Registro</a></li>
            <li><a href="test_database.php">Test BD</a></li>
        </ul>
    </nav>

    <!-- Header principal -->
    <div class="header">
        <div class="header-logo">
            <img src="assets/images/logo_header_100x100_lg.png" alt="Logo Masónico" width="80" height="80">
        </div>
        <h1>Gran Logia de Libres y Aceptados Masones</h1>
        <h2 style="font-size: 1.2rem; margin-top: 0.5rem; opacity: 0.9; font-weight: 400; font-style: italic;">
            de la República del Paraguay
        </h2>
        <p>Formulario de Registro de Miembros - R∴E∴A∴A∴</p>

        <!-- Indicador de estado de database -->
        <?php if ($database_loaded): ?>
            <div class="status-indicator status-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $database_status; ?> (<?php echo $total_registros; ?> registros)
            </div>
        <?php else: ?>
            <div class="status-indicator status-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $database_status; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Barra de progreso pegajosa -->
    <div class="progress-sticky" id="progress-container">
        <div class="progress-info">
            <span class="progress-text">Progreso del formulario</span>
            <span class="progress-percentage" id="progress-percentage">0%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
    </div>

    <!-- Contenedor principal -->
    <div class="container">
        <div class="form-container">
            <!-- Formulario de registro -->
            <form action="processv2.php" method="POST" enctype="multipart/form-data" id="registration-form" novalidate>

                <!-- SECCIÓN 1: DATOS PERSONALES -->
                <section class="section" data-section="personal">
                    <h2 class="section-title">
                        <i class="fas fa-user"></i> DATOS PERSONALES
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ci">CI:<span class="required">*</span></label>
                            <input type="text" inputmode="numeric" id="ci" name="ci" class="form-control" required
                                placeholder="Ej: 1234567" autocomplete="off" pattern="[0-9]*" maxlength="8">
                            <small class="form-help">Número de Cédula de Identidad sin puntos</small>
                        </div>

                        <div class="form-group">
                            <label for="nombre">Nombre:<span class="required">*</span></label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required
                                placeholder="Ingrese su nombre" autocomplete="given-name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="apellido">Apellido:<span class="required">*</span></label>
                            <input type="text" id="apellido" name="apellido" class="form-control" required
                                placeholder="Ingrese su apellido" autocomplete="family-name">
                        </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento:<span class="required">*</span></label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required
                                autocomplete="bday" max="<?php echo $fecha_actual; ?>" min="<?php echo $fecha_minima_nacimiento; ?>">
                            <small class="form-help">Debe ser mayor de 18 años</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="lugar">Lugar de Nacimiento:<span class="required">*</span></label>
                            <input type="text" id="lugar" name="lugar" class="form-control" required
                                placeholder="Ciudad, país de nacimiento">
                        </div>

                        <div class="form-group">
                            <label for="profesion">Profesión:<span class="required">*</span></label>
                            <input type="text" id="profesion" name="profesion" class="form-control" required
                                placeholder="Su profesión u oficio">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="direccion">Dirección:<span class="required">*</span></label>
                            <input type="text" id="direccion" name="direccion" class="form-control" required
                                placeholder="Dirección de residencia" autocomplete="street-address">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ciudad">Ciudad:<span class="required">*</span></label>
                            <select id="ciudad" name="ciudad" class="form-control" required>
                                <option value="">Seleccione ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <option value="<?php echo htmlspecialchars($ciudad); ?>"><?php echo htmlspecialchars($ciudad); ?></option>
                                <?php endforeach; ?>
                                <option value="Otra">Otra ciudad</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="barrio">Barrio:<span class="required">*</span></label>
                            <input type="text" id="barrio" name="barrio" class="form-control" required
                                placeholder="Barrio o sector">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono:<span class="required">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="telefono" name="telefono" class="form-control" required
                                    placeholder="+595 9XX XXX XXX" autocomplete="tel">
                            </div>
                        </div>
                    </div>

                    <!-- Datos familiares opcionales -->
                    <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem; font-size: 1.1rem;">
                        <i class="fas fa-heart"></i> Datos Familiares (Opcional)
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="esposa">Esposa:</label>
                            <input type="text" id="esposa" name="esposa" class="form-control"
                                placeholder="Nombre de esposa">
                        </div>

                        <div class="form-group">
                            <label for="hijos">Hijos:</label>
                            <textarea id="hijos" name="hijos" class="form-control" rows="2"
                                placeholder="Nombres de hijos, separados por comas"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="madre">Madre:</label>
                            <input type="text" id="madre" name="madre" class="form-control"
                                placeholder="Nombre de madre">
                        </div>

                        <div class="form-group">
                            <label for="padre">Padre:</label>
                            <input type="text" id="padre" name="padre" class="form-control"
                                placeholder="Nombre de padre">
                        </div>
                    </div>
                </section>

                <!-- SECCIÓN 2: DATOS LABORALES -->
                <section class="section" data-section="laboral">
                    <h2 class="section-title">
                        <i class="fas fa-briefcase"></i> DATOS LABORALES
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="direccion_laboral">Dirección Laboral:<span class="required">*</span></label>
                            <input type="text" id="direccion_laboral" name="direccion_laboral" class="form-control" required
                                placeholder="Dirección de su trabajo" autocomplete="work street-address">
                        </div>

                        <div class="form-group">
                            <label for="empresa">Empresa:</label>
                            <input type="text" id="empresa" name="empresa" class="form-control"
                                placeholder="Nombre de la empresa" autocomplete="organization">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="descripcion_otros_trabajos">Otros Trabajos o Actividades:</label>
                            <textarea id="descripcion_otros_trabajos" name="descripcion_otros_trabajos" class="form-control" rows="3"
                                placeholder="Describa trabajos adicionales, negocios propios, actividades comerciales, etc."></textarea>
                        </div>
                    </div>
                </section>

                <!-- NOTA: Las siguientes secciones se agregan en las próximas etapas -->
                <!-- SECCIÓN 3: DATOS LOGIALES -->
                <!-- SECCIÓN 4: DATOS MÉDICOS -->
                <!-- SECCIÓN 5: INFORMACIÓN ADICIONAL -->

            </form>
        </div>
    </div>

    <!-- Botón de envío fijo -->
    <div class="submit-fixed">
        <button type="submit" form="registration-form" class="submit-btn" id="submit-button" disabled>
            <i class="fas fa-save"></i>
            <span>Completar Registro</span>
        </button>
    </div>

    <!-- JavaScript funcional -->
    <script>
        // Variables globales
        let formProgress = 0;
        let totalFields = 0;
        let completedFields = 0;

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            setupProgressTracking();
            setupMobileOptimizations();
            setupMobileNavigation();
            setupFormSubmission();

            console.log('🚀 ETAPA 1 - Formulario base inicializado correctamente');
        });

        function initializeForm() {
            console.log('🚀 Formulario de scroll infinito iniciado');

            // Contar campos totales requeridos
            const requiredFields = document.querySelectorAll('[required]');
            totalFields = requiredFields.length;

            console.log(`📊 Total de campos requeridos: ${totalFields}`);
        }

        function setupProgressTracking() {
            // Configurar seguimiento de progreso
            const fields = document.querySelectorAll('input, select, textarea');

            fields.forEach(field => {
                field.addEventListener('input', updateProgress);
                field.addEventListener('change', updateProgress);
            });
        }

        function updateProgress() {
            const requiredFields = document.querySelectorAll('[required]');
            let completed = 0;

            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    completed++;
                    field.classList.add('field-completed');
                    field.classList.remove('field-error');
                } else {
                    field.classList.remove('field-completed');
                }
            });

            completedFields = completed;
            const percentage = Math.round((completed / totalFields) * 100);

            // Actualizar barra de progreso
            const progressBar = document.getElementById('progress-bar');
            const progressPercentage = document.getElementById('progress-percentage');

            if (progressBar && progressPercentage) {
                progressBar.style.width = percentage + '%';
                progressPercentage.textContent = percentage + '%';
            }

            // Habilitar/deshabilitar botón de envío
            const submitButton = document.getElementById('submit-button');
            if (submitButton) {
                if (percentage >= 70) { // Permitir envío con 70% completado
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save"></i><span>Enviar Registro (' + percentage + '%)</span>';
                } else {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-edit"></i><span>Complete más campos (' + percentage + '%)</span>';
                }
            }
        }

        function setupMobileOptimizations() {
            // Optimizaciones específicas para móviles

            // 1. Prevenir zoom en inputs
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    if (window.innerWidth < 768) {
                        document.body.style.transform = 'scale(1)';
                    }
                });
            });

            // 2. Formateo automático de teléfono
            const phoneInput = document.getElementById('telefono');
            if (phoneInput) {
                phoneInput.addEventListener('input', formatPhoneNumber);
            }

            // 3. Formateo de CI
            const ciInput = document.getElementById('ci');
            if (ciInput) {
                ciInput.addEventListener('input', formatCI);
            }

            // 4. Validación de edad mínima en fecha de nacimiento
            const birthDateInput = document.getElementById('fecha_nacimiento');
            if (birthDateInput) {
                birthDateInput.addEventListener('change', validateAge);
            }
        }

        function formatPhoneNumber(e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.startsWith('595')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                // Formato nacional paraguayo
                if (value.length > 10) value = value.slice(0, 10);
                value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
            } else if (value.length > 0) {
                // Agregar prefijo móvil
                value = '0' + value;
                if (value.length > 10) value = value.slice(0, 10);
            }

            e.target.value = value;
        }

        function formatCI(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.slice(0, 8);
            }
            e.target.value = value;
        }

        function validateAge(e) {
            const birthDate = new Date(e.target.value);
            const today = new Date();
            const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));

            if (age < 18) {
                showNotification('Debe ser mayor de 18 años para registrarse', 'error');
                e.target.classList.add('field-error');
            } else {
                e.target.classList.remove('field-error');
                e.target.classList.add('field-completed');
            }
        }

        function setupMobileNavigation() {
            // Navegación móvil
            const navToggle = document.querySelector('.nav-toggle');
            const navMenu = document.getElementById('main-menu');

            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    const isOpen = navMenu.classList.contains('active');
                    this.setAttribute('aria-expanded', isOpen);
                });

                // Cerrar menú al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                        navMenu.classList.remove('active');
                        navToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        }

        function setupFormSubmission() {
            // Envío del formulario básico
            const form = document.getElementById('registration-form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (validateBasicForm()) {
                        submitForm();
                    } else {
                        showNotification('Por favor, complete todos los campos requeridos', 'error');
                    }
                });
            }
        }

        function validateBasicForm() {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('field-error');
                    isValid = false;
                } else {
                    field.classList.remove('field-error');
                    field.classList.add('field-completed');
                }
            });

            return isValid;
        }

        function submitForm() {
            const form = document.getElementById('registration-form');
            const submitButton = document.getElementById('submit-button');

            // Mostrar estado de carga
            submitButton.disabled = true;
            submitButton.innerHTML = '<div class="spinner"></div><span>Enviando...</span>';

            // Crear FormData
            const formData = new FormData(form);

            // Enviar formulario
            fetch('processv2.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('✅') && data.includes('Registro Exitoso')) {
                        showNotification('¡Registro enviado exitosamente!', 'success');

                        // Mostrar resultado en nueva ventana
                        const newWindow = window.open('', '_blank');
                        newWindow.document.write(data);

                        // Limpiar formulario después de 3 segundos
                        setTimeout(() => {
                            form.reset();
                            updateProgress();
                            showNotification('Formulario limpio para nuevo registro', 'info');
                        }, 3000);
                    } else {
                        // Mostrar resultado aunque haya errores
                        const newWindow = window.open('', '_blank');
                        newWindow.document.write(data);

                        showNotification('Formulario procesado. Revise la ventana de resultados.', 'warning');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión. Por favor, intente nuevamente.', 'error');
                })
                .finally(() => {
                    // Restaurar botón
                    submitButton.disabled = false;
                    updateProgress(); // Esto restaurará el estado correcto del botón
                });
        }

        function showNotification(message, type = 'info') {
            // Crear notificación móvil
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            const icon = type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" style="margin-left: auto; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: inherit;">×</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Mostrar notificación
            setTimeout(() => notification.classList.add('show'), 100);

            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Inicialización completa al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 ETAPA 1 - Iniciando formulario base...');

            initializeForm();
            setupProgressTracking();
            setupMobileOptimizations();
            setupMobileNavigation();
            setupFormSubmission();

            // Actualizar progreso inicial
            setTimeout(updateProgress, 500);

            console.log('✅ ETAPA 1 - Formulario base completamente inicializado');

            // Notificación de bienvenida
            setTimeout(() => {
                showNotification('Formulario listo para usar. Complete los campos para continuar.', 'info');
            }, 1000);
        });

        // Detectar conexión
        window.addEventListener('online', () => showNotification('Conexión restaurada', 'success'));
        window.addEventListener('offline', () => showNotification('Sin conexión. Los datos se guardarán cuando se restaure.', 'warning'));
    </script>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="assets/images/logo_header_100x100_lg.png" alt="Logo" width="40" height="40">
            </div>
            <div class="footer-text">
                <p><strong>Gran Logia de Libres y Aceptados Masones de la República del Paraguay</strong></p>
                <p>R∴E∴A∴A∴ • Fundada 1895-1996 • Oriente de Asunción</p>
                <p>&copy; <?php echo date('Y'); ?> Todos los derechos reservados.</p>
            </div>
            <div class="footer-links">
                <a href="#" style="color: #FFD700;">Libertad</a> •
                <a href="#" style="color: #FFD700;">Igualdad</a> •
                <a href="#" style="color: #FFD700;">Fraternidad</a>
            </div>
        </div>
    </footer>

</body>

</html>