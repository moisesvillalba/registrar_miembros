<?php
// Iniciar sesión
session_start();

// Configuración para PRODUCCIÓN
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Incluir database con manejo de errores
$database_loaded = false;
$database_status = '';

try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn !== null) {
        $database_loaded = true;
        //  $database_status = 'Conexión exitosa a la base de datos';
        $database_status = 'Bienvenido al formulario de registro';
    } else {
        $database_loaded = false;
        $database_status = 'Error de conexión a la base de datos';
    }
} catch (Exception $e) {
    error_log("Error cargando database: " . $e->getMessage());
    $database_loaded = false;
    $database_status = 'Error al cargar configuración de base de datos';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Sistema de registro integral para miembros. Complete sus datos personales, laborales, logiales y médicos.">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Formulario de Registro | Gran Logia de Libres y Aceptados Masones del Paraguay</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- Font Awesome -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="assets/images/logo_header_100x100_lg.png">

    <!-- CSS móvil específico inline para optimización -->
    <style>
        /* ===== OPTIMIZACIONES MÓVILES ESPECÍFICAS ===== */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        input,
        textarea,
        select {
            -webkit-user-select: text;
            user-select: text;
        }

        /* ===== LOADING STATES ===== */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
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

        /* ===== OFFLINE INDICATOR ===== */
        .offline-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ef4444;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            z-index: 9999;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }

        .offline-indicator.show {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- Indicador offline -->
    <div id="offline-indicator" class="offline-indicator">
        Sin conexión a internet. Sus datos se guardarán localmente.
    </div>

    <!-- Loading overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Barra de navegación -->
    <nav class="nav-container">
        <div class="logo">
            <img src="assets/images/logo_header_100x100_lg.png" alt="Logo Masónico" width="40" height="40" style="margin-right: 10px;">
            <a href="index.php">
                G∴L∴ de L∴ y A∴M∴ del Paraguay
            </a>
        </div>
        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-menu" id="main-menu">
            <li><a href="index.php" class="active" aria-current="page">Inicio</a></li>
            <li><a href="#">Contacto</a></li>
        </ul>
    </nav>

    <main class="container">
        <div class="header">
            <div class="header-logo">
                <img src="assets/images/logo_header_100x100_lg.png" alt="Logo Masónico" width="80" height="80" style="border: 3px solid #FFD700; border-radius: 50%; background: white; padding: 5px;">
            </div>
            <h1>Gran Logia de Libres y Aceptados Masones</h1>
            <h2 style="font-size: 1.2rem; margin-top: 0.5rem; opacity: 0.9; font-weight: 400; font-style: italic;">de la República del Paraguay</h2>
            <p>Formulario de Registro de Miembros - R∴E∴A∴A∴</p>

            <!-- Indicador de estado de database -->
            <?php if ($database_loaded): ?>
                <div style="background: #d4edda; color: #155724; padding: 8px 12px; border-radius: 4px; margin: 10px 0; font-size: 0.9rem;">
                    <i class="fas fa-check-circle"></i> <?php echo $database_status; ?>
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; color: #721c24; padding: 8px 12px; border-radius: 4px; margin: 10px 0; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $database_status; ?>
                    <br><small>El formulario funcionará en modo sin base de datos</small>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <!-- Pasos del formulario -->
            <div class="form-steps-container">
                <div class="progress-container">
                    <div class="progress-line"></div>
                    <div class="progress-bar" id="progress-bar"></div>
                </div>
                <div class="steps-nav">
                    <button class="progress-step active" data-step="1" aria-label="Ir a Datos Personales" type="button">
                        <div class="step-icon">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                        <div class="step-label">Datos Personales</div>
                    </button>
                    <button class="progress-step" data-step="2" aria-label="Ir a Datos Laborales" type="button">
                        <div class="step-icon">
                            <i class="fas fa-briefcase" aria-hidden="true"></i>
                        </div>
                        <div class="step-label">Datos Laborales</div>
                    </button>
                    <button class="progress-step" data-step="3" aria-label="Ir a Datos Logiales" type="button">
                        <div class="step-icon">
                            <i class="fas fa-university" aria-hidden="true"></i>
                        </div>
                        <div class="step-label">Datos Logiales</div>
                    </button>
                    <button class="progress-step" data-step="4" aria-label="Ir a Datos Médicos" type="button">
                        <div class="step-icon">
                            <i class="fas fa-heartbeat" aria-hidden="true"></i>
                        </div>
                        <div class="step-label">Datos Médicos</div>
                    </button>
                </div>
            </div>

            <!-- Formulario de registro -->
            <form action="processv2.php" method="POST" enctype="multipart/form-data" id="registration-form" novalidate>
                <!-- PASO 1: DATOS PERSONALES -->
                <section class="form-step active" data-step="1" aria-labelledby="paso1-titulo">
                    <div class="section">
                        <h2 class="section-title" id="paso1-titulo">
                            <i class="fas fa-user" aria-hidden="true"></i> DATOS PERSONALES
                        </h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ci">CI:<span class="required">*</span></label>
                                <input type="text" inputmode="numeric" id="ci" name="ci" class="form-control" required
                                    placeholder="Ingrese solo números" autocomplete="off" pattern="[0-9]*">
                                <small class="form-help">Número de Cédula de Identidad sin puntos</small>
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre:<span class="required">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required
                                    placeholder="Ingrese su nombre" autocomplete="given-name">
                            </div>

                            <div class="form-group">
                                <label for="apellido">Apellido:<span class="required">*</span></label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required
                                    placeholder="Ingrese su apellido" autocomplete="family-name">
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- FECHA DE NACIMIENTO OPTIMIZADA -->
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de Nacimiento:<span class="required">*</span></label>
                                <?php
                                // Configuración dinámica de fechas
                                $fecha_actual = new DateTime();
                                $fecha_maxima = $fecha_actual->format('Y-m-d');
                                $ano_actual = $fecha_actual->format('Y');
                                $ano_minimo = 1940;

                                // Calcular años para botones rápidos (generaciones comunes)
                                $anos_comunes = [];
                                for ($i = $ano_minimo; $i <= $ano_actual; $i += 5) {
                                    $anos_comunes[] = $i;
                                }
                                // Agregar algunos años específicos importantes
                                $anos_especiales = [1979, 1985, 1990, 1995, 2000];
                                foreach ($anos_especiales as $ano) {
                                    if ($ano <= $ano_actual && !in_array($ano, $anos_comunes)) {
                                        $anos_comunes[] = $ano;
                                    }
                                }
                                sort($anos_comunes);
                                ?>
                                <div class="universal-date-field"
                                    data-field="fecha_nacimiento"
                                    data-required="true"
                                    data-min-year="<?php echo $ano_minimo; ?>"
                                    data-max-year="<?php echo $ano_actual; ?>">
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control enhanced-date" required
                                        autocomplete="bday" max="<?php echo $fecha_maxima; ?>" min="<?php echo $ano_minimo; ?>-01-01">

                                    <div class="date-input-methods" id="fecha_nacimiento_methods" data-max-date="<?php echo $fecha_maxima; ?>">
                                        <!-- Tabs de métodos -->
                                        <div class="date-method-tabs">
                                            <button type="button" class="date-method-tab active" data-method="native">Calendario</button>
                                            <button type="button" class="date-method-tab" data-method="manual">Manual</button>
                                            <button type="button" class="date-method-tab" data-method="selectors">Listas</button>
                                        </div>

                                        <!-- Método 1: Calendario nativo -->
                                        <div class="date-method-content active" id="fecha_nacimiento_native">
                                            <input type="date"
                                                class="native-date-input"
                                                min="<?php echo $ano_minimo; ?>-01-01"
                                                max="<?php echo $fecha_maxima; ?>"
                                                data-target="fecha_nacimiento">

                                            <div class="quick-years-section">
                                                <div class="quick-years-label">Años comunes</div>
                                                <div class="quick-years-grid">
                                                    <?php foreach ($anos_comunes as $ano): ?>
                                                        <button type="button" class="quick-year-btn" data-year="<?php echo $ano; ?>"><?php echo $ano; ?></button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Método 2: Entrada manual -->
                                        <div class="date-method-content" id="fecha_nacimiento_manual">
                                            <div class="manual-date-container">
                                                <input type="text"
                                                    class="manual-date-input"
                                                    placeholder="DD/MM/AAAA"
                                                    maxlength="10"
                                                    inputmode="numeric"
                                                    data-target="fecha_nacimiento"
                                                    data-max-date="<?php echo $fecha_maxima; ?>">
                                                <button type="button" class="manual-clear-btn" aria-label="Limpiar fecha">×</button>
                                            </div>
                                        </div>

                                        <!-- Método 3: Selectores -->
                                        <div class="date-method-content" id="fecha_nacimiento_selectors">
                                            <div class="date-selectors-grid">
                                                <select class="date-selector" data-type="day" data-target="fecha_nacimiento">
                                                    <option value="">Día</option>
                                                    <?php for ($d = 1; $d <= 31; $d++): ?>
                                                        <option value="<?php echo $d; ?>"><?php echo str_pad($d, 2, '0', STR_PAD_LEFT); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <select class="date-selector" data-type="month" data-target="fecha_nacimiento">
                                                    <option value="">Mes</option>
                                                    <?php
                                                    $meses = [
                                                        1 => 'Enero',
                                                        2 => 'Febrero',
                                                        3 => 'Marzo',
                                                        4 => 'Abril',
                                                        5 => 'Mayo',
                                                        6 => 'Junio',
                                                        7 => 'Julio',
                                                        8 => 'Agosto',
                                                        9 => 'Septiembre',
                                                        10 => 'Octubre',
                                                        11 => 'Noviembre',
                                                        12 => 'Diciembre'
                                                    ];
                                                    foreach ($meses as $num => $nombre):
                                                    ?>
                                                        <option value="<?php echo $num; ?>"><?php echo $nombre; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="date-selector" data-type="year" data-target="fecha_nacimiento">
                                                    <option value="">Año</option>
                                                    <?php for ($y = $ano_actual; $y >= $ano_minimo; $y--): ?>
                                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Preview de la fecha -->
                                        <div class="date-preview" id="fecha_nacimiento_preview">
                                            <div class="formatted-date"></div>
                                            <div class="age-info"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lugar">Lugar:<span class="required">*</span></label>
                                <input type="text" id="lugar" name="lugar" class="form-control" required
                                    placeholder="Lugar de nacimiento">
                            </div>

                            <div class="form-group">
                                <label for="profesion">Profesión:<span class="required">*</span></label>
                                <input type="text" id="profesion" name="profesion" class="form-control" required
                                    placeholder="Ingrese su profesión">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="direccion">Dirección:<span class="required">*</span></label>
                                <input type="text" id="direccion" name="direccion" class="form-control" required
                                    placeholder="Dirección de residencia" autocomplete="street-address">
                            </div>

                            <div class="form-group">
                                <label for="telefono">Teléfono:<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <input type="tel" id="telefono" name="telefono" class="form-control" required
                                        placeholder="+595 (9XX) XXX-XXX" autocomplete="tel">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ciudad">Ciudad:<span class="required">*</span></label>
                                <input type="text" id="ciudad" name="ciudad" class="form-control" required
                                    placeholder="Ciudad actual" list="ciudades-comunes" autocomplete="address-level2">
                                <datalist id="ciudades-comunes">
                                    <option value="Asunción">
                                    <option value="Ciudad del Este">
                                    <option value="Encarnación">
                                    <option value="San Lorenzo">
                                    <option value="Luque">
                                    <option value="Capiatá">
                                    <option value="Lambaré">
                                    <option value="Fernando de la Mora">
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label for="barrio">Barrio:<span class="required">*</span></label>
                                <input type="text" id="barrio" name="barrio" class="form-control" required
                                    placeholder="Barrio o sector">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="esposa">Esposa:</label>
                                <input type="text" id="esposa" name="esposa" class="form-control"
                                    placeholder="Nombre de esposa">
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="hijos">Hijos:</label>
                                <textarea id="hijos" name="hijos" class="form-control" rows="2"
                                    placeholder="Nombres de hijos, separados por comas"></textarea>
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="madre">Madre:</label>
                                <input type="text" id="madre" name="madre" class="form-control"
                                    placeholder="Nombre de madre">
                                <small class="form-help">Opcional</small>
                            </div>

                            <div class="form-group">
                                <label for="padre">Padre:</label>
                                <input type="text" id="padre" name="padre" class="form-control"
                                    placeholder="Nombre de padre">
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="step-navigation">
                            <div></div> <!-- Espacio vacío para alineación -->
                            <button type="button" class="btn btn-primary next-step" data-next="2">
                                Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </section>
                <!-- PASO 2: DATOS LABORALES -->
                <section class="form-step" data-step="2" aria-labelledby="paso2-titulo">
                    <div class="section">
                        <h2 class="section-title" id="paso2-titulo">
                            <i class="fas fa-briefcase" aria-hidden="true"></i> DATOS LABORALES
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
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="step-navigation">
                            <button type="button" class="btn btn-outline prev-step" data-prev="1">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="3">
                                Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </section>
                <!-- PASO 3: DATOS LOGIALES -->
                <section class="form-step" data-step="3" aria-labelledby="paso3-titulo">
                    <div class="section">
                        <h2 class="section-title" id="paso3-titulo">
                            <i class="fas fa-monument" aria-hidden="true"></i> DATOS LOGIALES
                        </h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="logia_actual">Logia Actual:<span class="required">*</span></label>
                                <select id="logia_actual" name="logia_actual" class="form-control" required>
                                    <option value="">Seleccione Logia</option>
                                    <?php
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
                                    foreach ($logias as $logia):
                                    ?>
                                        <option value="<?php echo htmlspecialchars($logia); ?>"><?php echo htmlspecialchars($logia); ?></option>
                                    <?php endforeach; ?>
                                    <option value="Otra">Otra (especificar en observaciones)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="grado_masonico">Grado Masónico:<span class="required">*</span></label>
                                <select id="grado_masonico" name="grado_masonico" class="form-control" required>
                                    <option value="">Seleccione Grado</option>
                                    <option value="aprendiz">Aprendiz</option>
                                    <option value="companero">Compañero</option>
                                    <option value="maestro">Maestro</option>
                                </select>
                            </div>
                        </div>

                        <!-- FECHA DE INICIACIÓN -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_iniciacion">Fecha de Iniciación:<span class="required">*</span></label>
                                <?php
                                // Configuración específica para fecha de iniciación
                                $fecha_iniciacion_min = 1950; // Las logias modernas empezaron después de 1950
                                $anos_iniciacion_comunes = [];

                                // Generar años más frecuentes para iniciaciones (cada 5 años desde 1980)
                                for ($i = 1980; $i <= $ano_actual; $i += 5) {
                                    $anos_iniciacion_comunes[] = $i;
                                }
                                // Agregar años recientes más detallados
                                $anos_recientes = [$ano_actual - 4, $ano_actual - 3, $ano_actual - 2, $ano_actual - 1, $ano_actual];
                                foreach ($anos_recientes as $ano) {
                                    if ($ano >= $fecha_iniciacion_min && !in_array($ano, $anos_iniciacion_comunes)) {
                                        $anos_iniciacion_comunes[] = $ano;
                                    }
                                }
                                sort($anos_iniciacion_comunes);
                                ?>
                                <div class="universal-date-field"
                                    data-field="fecha_iniciacion"
                                    data-required="true"
                                    data-min-year="<?php echo $fecha_iniciacion_min; ?>"
                                    data-max-year="<?php echo $ano_actual; ?>">
                                    <input type="date" id="fecha_iniciacion" name="fecha_iniciacion" class="form-control enhanced-date" required
                                        max="<?php echo $fecha_maxima; ?>" min="<?php echo $fecha_iniciacion_min; ?>-01-01">

                                    <div class="date-input-methods" id="fecha_iniciacion_methods" data-max-date="<?php echo $fecha_maxima; ?>">
                                        <!-- Tabs de métodos -->
                                        <div class="date-method-tabs">
                                            <button type="button" class="date-method-tab active" data-method="native">Calendario</button>
                                            <button type="button" class="date-method-tab" data-method="manual">Manual</button>
                                            <button type="button" class="date-method-tab" data-method="selectors">Listas</button>
                                        </div>

                                        <!-- Método 1: Calendario nativo -->
                                        <div class="date-method-content active" id="fecha_iniciacion_native">
                                            <input type="date"
                                                class="native-date-input"
                                                min="<?php echo $fecha_iniciacion_min; ?>-01-01"
                                                max="<?php echo $fecha_maxima; ?>"
                                                data-target="fecha_iniciacion">

                                            <div class="quick-years-section">
                                                <div class="quick-years-label">Años frecuentes de iniciación</div>
                                                <div class="quick-years-grid">
                                                    <?php foreach ($anos_iniciacion_comunes as $ano): ?>
                                                        <button type="button" class="quick-year-btn" data-year="<?php echo $ano; ?>"><?php echo $ano; ?></button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Método 2: Entrada manual -->
                                        <div class="date-method-content" id="fecha_iniciacion_manual">
                                            <div class="manual-date-container">
                                                <input type="text"
                                                    class="manual-date-input"
                                                    placeholder="DD/MM/AAAA"
                                                    maxlength="10"
                                                    inputmode="numeric"
                                                    data-target="fecha_iniciacion"
                                                    data-max-date="<?php echo $fecha_maxima; ?>">
                                                <button type="button" class="manual-clear-btn" aria-label="Limpiar fecha">×</button>
                                            </div>
                                        </div>

                                        <!-- Método 3: Selectores -->
                                        <div class="date-method-content" id="fecha_iniciacion_selectors">
                                            <div class="date-selectors-grid">
                                                <select class="date-selector" data-type="day" data-target="fecha_iniciacion">
                                                    <option value="">Día</option>
                                                    <?php for ($d = 1; $d <= 31; $d++): ?>
                                                        <option value="<?php echo $d; ?>"><?php echo str_pad($d, 2, '0', STR_PAD_LEFT); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <select class="date-selector" data-type="month" data-target="fecha_iniciacion">
                                                    <option value="">Mes</option>
                                                    <?php foreach ($meses as $num => $nombre): ?>
                                                        <option value="<?php echo $num; ?>"><?php echo $nombre; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="date-selector" data-type="year" data-target="fecha_iniciacion">
                                                    <option value="">Año</option>
                                                    <?php for ($y = $ano_actual; $y >= $fecha_iniciacion_min; $y--): ?>
                                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Preview de la fecha -->
                                        <div class="date-preview" id="fecha_iniciacion_preview">
                                            <div class="formatted-date"></div>
                                            <div class="age-info"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="logia_iniciacion">Logia de Iniciación:</label>
                                <select id="logia_iniciacion" name="logia_iniciacion" class="form-control">
                                    <option value="">Seleccione Logia</option>
                                    <?php foreach ($logias as $logia): ?>
                                        <option value="<?php echo htmlspecialchars($logia); ?>"><?php echo htmlspecialchars($logia); ?></option>
                                    <?php endforeach; ?>
                                    <option value="Otra">Otra (especificar en observaciones)</option>
                                    <option value="Exterior del país">Exterior del país</option>
                                </select>
                                <small class="form-help">Opcional</small>
                            </div>

                            <div class="form-group">
                                <label for="certificados">Foto de Certificados:</label>
                                <div class="file-upload-container">
                                    <input type="file" id="certificados" name="certificados[]" multiple
                                        class="form-control file-input"
                                        accept="image/jpeg,image/png,application/pdf">
                                    <label for="certificados" class="file-label">
                                        <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
                                        <span>Seleccionar archivos</span>
                                    </label>
                                    <div class="file-preview"></div>
                                </div>
                                <small class="form-help">Formatos permitidos: PDF, JPG, PNG. Máximo 5MB. (Opcional)</small>
                            </div>
                        </div>

                        <div class="step-navigation">
                            <button type="button" class="btn btn-outline prev-step" data-prev="2">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="4">
                                Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </section>
                <!-- PASO 4: DATOS MÉDICOS Y CAMPOS ADICIONALES -->
                <section class="form-step" data-step="4" aria-labelledby="paso4-titulo">
                    <div class="section">
                        <h2 class="section-title" id="paso4-titulo">
                            <i class="fas fa-heartbeat" aria-hidden="true"></i> DATOS MÉDICOS
                        </h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="grupo_sanguineo">Grupo Sanguíneo:<span class="required">*</span></label>
                                <select id="grupo_sanguineo" name="grupo_sanguineo" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="enfermedades_base">Enfermedades Base:</label>
                                <textarea id="enfermedades_base" name="enfermedades_base" class="form-control" rows="2"
                                    placeholder="Liste enfermedades crónicas o de base"></textarea>
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <fieldset class="radio-fieldset">
                                    <legend>¿Cuenta con Seguro Privado?</legend>
                                    <div class="radio-options">
                                        <div class="radio-group">
                                            <input type="radio" id="seguro_si" name="seguro_privado" value="Si">
                                            <label for="seguro_si">Sí</label>
                                        </div>
                                        <div class="radio-group">
                                            <input type="radio" id="seguro_no" name="seguro_privado" value="No" checked>
                                            <label for="seguro_no">No</label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="form-group">
                                <fieldset class="radio-fieldset">
                                    <legend>¿IPS?</legend>
                                    <div class="radio-options">
                                        <div class="radio-group">
                                            <input type="radio" id="ips_si" name="ips" value="Si">
                                            <label for="ips_si">Sí</label>
                                        </div>
                                        <div class="radio-group">
                                            <input type="radio" id="ips_no" name="ips" value="No" checked>
                                            <label for="ips_no">No</label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="alergias">Alergias:</label>
                                <textarea id="alergias" name="alergias" class="form-control" rows="2"
                                    placeholder="Liste medicamentos o sustancias a las que es alérgico"></textarea>
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="numero_emergencia">Número en caso de Emergencias:<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <input type="tel" id="numero_emergencia" name="numero_emergencia" class="form-control" required
                                        placeholder="+595 (9XX) XXX-XXX">
                                </div>
                                <small class="form-help">Teléfono de contacto en caso de emergencia</small>
                            </div>

                            <div class="form-group">
                                <label for="contacto_emergencia">Contacto en Caso de Emergencias:<span class="required">*</span></label>
                                <input type="text" id="contacto_emergencia" name="contacto_emergencia" class="form-control" required
                                    placeholder="Nombre del contacto de emergencia">
                            </div>
                        </div>

                        <!-- SECCIÓN: CAMPOS ADICIONALES NUEVOS -->
                        <h3 class="section-subtitle" style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 8px;">
                            <i class="fas fa-plus-circle" aria-hidden="true"></i> Información Adicional
                        </h3>

                        <!-- Foto del Hermano -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="foto_hermano">Foto del Hermano:</label>
                                <div class="file-upload-container">
                                    <input type="file" id="foto_hermano" name="foto_hermano"
                                        class="form-control file-input"
                                        accept="image/jpeg,image/png">
                                    <label for="foto_hermano" class="file-label">
                                        <i class="fas fa-camera" aria-hidden="true"></i>
                                        <span>Seleccionar foto</span>
                                    </label>
                                    <div class="file-preview"></div>
                                </div>
                                <small class="form-help">JPG o PNG, máximo 2MB. (Opcional)</small>
                            </div>
                        </div>

                        <!-- Otros trabajos -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descripcion_otros_trabajos">Descripción de trabajos, negocios u otras actividades:</label>
                                <textarea id="descripcion_otros_trabajos" name="descripcion_otros_trabajos" class="form-control" rows="3"
                                    placeholder="Describa aquí trabajos adicionales, negocios propios, actividades comerciales, etc."></textarea>
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <!-- Grados masónicos adicionales -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="grados_que_tienen">Grados que Tienen:</label>
                                <textarea id="grados_que_tienen" name="grados_que_tienen" class="form-control" rows="2"
                                    placeholder="Ej: 3°, 14°, 18°, 30°, 32°"></textarea>
                                <small class="form-help">Liste los grados masónicos que posee. Opcional</small>
                            </div>

                            <div class="form-group">
                                <label for="logia_perfeccion">Logia de Perfección:</label>
                                <input type="text" id="logia_perfeccion" name="logia_perfeccion" class="form-control"
                                    placeholder="Nombre de la logia de perfección">
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <!-- Descripción grado capitular -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descripcion_grado_capitular">Descripción del Grado Capitular:</label>
                                <textarea id="descripcion_grado_capitular" name="descripcion_grado_capitular" class="form-control" rows="4"
                                    placeholder="Describa ceremonias, responsabilidades, experiencias relacionadas con grados capitulares..."></textarea>
                                <small class="form-help">Opcional</small>
                            </div>
                        </div>

                        <div class="step-navigation">
                            <button type="button" class="btn btn-outline prev-step" data-prev="3">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="submit" class="btn btn-success" id="submit-button">
                                <i class="fas fa-save" aria-hidden="true"></i> Enviar Formulario
                            </button>
                        </div>

                        <!-- Indicador de campos requeridos -->
                        <div class="required-fields-note">
                            <span class="required">*</span> Campos requeridos
                        </div>
                    </div>
                </section>
            </form>
        </div>

        <!-- Notificaciones -->
        <div id="notification-container" aria-live="polite"></div>
    </main>

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

    <!-- JavaScript -->
    <script src="assets/js/mobile-form-optimization.js"></script>
</body>

</html>