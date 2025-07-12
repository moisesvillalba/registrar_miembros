<?php
// index.php - Formulario Masónico Completo y Optimizado
session_start();

// Configuración básica
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Variables básicas
$database_loaded = false;
$database_status = 'Formulario funcionando sin base de datos';

// Intentar conectar base de datos (opcional)
try {
    if (file_exists('config/database.php')) {
        include 'config/database.php';
        if (class_exists('Database')) {
            $db = new Database();
            if ($db->getConnection()) {
                $database_loaded = true;
                $database_status = 'Formulario de Registro de Miembros - R∴E∴A∴A∴';
            }
        }
    }
} catch (Exception $e) {
    // Continuar sin base de datos
}

// Datos estáticos
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
    // Logias adicionales que faltan
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
    "A∴R∴L∴ Caballeros de la Luz",
    "A∴R∴L∴ Centauros Nº 999",
];


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Masónico | Gran Logia del Paraguay</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Estilos optimizados para scroll infinito */
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 1rem 120px;
        }

        .section {
            background: white;
            margin: 1.5rem 0;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.6s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .progress-sticky {
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-bar-container {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #8B0000, #FF6B6B);
            border-radius: 4px;
            width: 0%;
            transition: width 0.5s;
        }

        .submit-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 1rem;
            z-index: 1000;
            border-top: 1px solid #e5e7eb;
        }

        .submit-btn {
            width: 100%;
            background: #10b981;
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

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
            transition: all 0.3s;
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

        .field-completed {
            border-color: #10b981 !important;
            background: #f0fdf4;
        }

        .field-error {
            border-color: #ef4444 !important;
            background: #fef2f2;
        }

        input,
        select,
        textarea {
            font-size: 16px !important;
        }

        @media (max-width: 640px) {
            .container {
                padding: 0 0.5rem 120px;
            }

            .section {
                margin: 1rem 0;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="assets/images/logo_header_100x100_lg.png" alt="Logo" width="80" height="80">
        <h1>Gran Logia de Libres y Aceptados Masones</h1>
        <h2>de la República del Paraguay</h2>
        <p>Formulario de Registro de Miembros - R∴E∴A∴A∴</p>

        <div style="background: <?php echo $database_loaded ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $database_loaded ? '#155724' : '#721c24'; ?>; padding: 8px 12px; border-radius: 4px; margin: 10px 0;">
            <i class="fas <?php echo $database_loaded ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
            <?php echo $database_status; ?>
        </div>
    </div>

    <!-- Barra de progreso -->
    <div class="progress-sticky">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span>Progreso del formulario</span>
            <span id="progress-percentage">0%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
    </div>

    <!-- Contenedor principal -->
    <div class="container">
        <form action="processv2.php" method="POST" enctype="multipart/form-data" id="registration-form">

            <!-- SECCIÓN 1: DATOS PERSONALES -->
            <section class="section">
                <h2><i class="fas fa-user"></i> DATOS PERSONALES</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ci">CI:<span class="required">*</span></label>
                        <input type="text" id="ci" name="ci" class="form-control" required placeholder="1234567" maxlength="8">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre:<span class="required">*</span></label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="apellido">Apellido:<span class="required">*</span></label>
                        <input type="text" id="apellido" name="apellido" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha Nacimiento:<span class="required">*</span></label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lugar">Lugar Nacimiento:<span class="required">*</span></label>
                        <input type="text" id="lugar" name="lugar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="profesion">Profesión:<span class="required">*</span></label>
                        <input type="text" id="profesion" name="profesion" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="direccion">Dirección:<span class="required">*</span></label>
                        <input type="text" id="direccion" name="direccion" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ciudad">Ciudad:<span class="required">*</span></label>
                        <input type="text" id="ciudad" name="ciudad" class="form-control" required placeholder="Ingrese su ciudad">
                    </div>
                    <div class="form-group">
                        <label for="barrio">Barrio:<span class="required">*</span></label>
                        <input type="text" id="barrio" name="barrio" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono:<span class="required">*</span></label>
                        <input type="tel" id="telefono" name="telefono" class="form-control" required placeholder="+595 9XX XXX XXX">
                    </div>
                </div>

                <h3 style="color: #8B0000; margin-top: 2rem;">Datos Familiares (Opcional)</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="esposa">Esposa:</label>
                        <input type="text" id="esposa" name="esposa" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="hijos">Hijos:</label>
                        <textarea id="hijos" name="hijos" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="madre">Madre:</label>
                        <input type="text" id="madre" name="madre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="padre">Padre:</label>
                        <input type="text" id="padre" name="padre" class="form-control">
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 2: DATOS LABORALES -->
            <section class="section">
                <h2><i class="fas fa-briefcase"></i> DATOS LABORALES</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="direccion_laboral">Dirección Laboral:<span class="required">*</span></label>
                        <input type="text" id="direccion_laboral" name="direccion_laboral" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa:</label>
                        <input type="text" id="empresa" name="empresa" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descripcion_otros_trabajos">Otros Trabajos:</label>
                        <textarea id="descripcion_otros_trabajos" name="descripcion_otros_trabajos" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </section>

            <!--            SECCIÓN 3: DATOS LOGIALES
            <section class="section">
                <h2><i class="fas fa-university"></i> DATOS LOGIALES</h2>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #856404;"></i>
                    <strong style="color: #856404;">Importante:</strong> Los campos marcados con * son obligatorios para completar el registro.
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="logia_actual">Logia Actual:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <select id="logia_actual" name="logia_actual" class="form-control" required style="border: 2px solid #ffc107;">
                            <option value="">⚠️ SELECCIONE SU LOGIA ACTUAL</option>
                            <?php foreach ($logias as $logia): ?>
                                <option value="<?php echo $logia; ?>"><?php echo $logia; ?></option>
                            <?php endforeach; ?>
                            <option value="Otra">Otra (especificar en observaciones)</option>
                        </select>
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio</small>
                    </div>
                    <div class="form-group">
                        <label for="grado_masonico">Grado Masónico:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <select id="grado_masonico" name="grado_masonico" class="form-control" required style="border: 2px solid #ffc107;">
                            <option value="">⚠️ SELECCIONE SU GRADO</option>
                            <option value="aprendiz">Aprendiz (1°)</option>
                            <option value="companero">Compañero (2°)</option>
                            <option value="maestro">Maestro (3°)</option>
                        </select>
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_iniciacion">Fecha de Iniciación:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <input type="date" id="fecha_iniciacion" name="fecha_iniciacion" class="form-control" required
                            style="border: 2px solid #ffc107;" min="1950-01-01" max="<?php echo date('Y-m-d'); ?>">
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio - Fecha cuando fue iniciado en la masonería</small>
                    </div>
                    <div class="form-group">
                        <label for="logia_iniciacion">Logia de Iniciación:</label>
                        <select id="logia_iniciacion" name="logia_iniciacion" class="form-control">
                            <option value="">Seleccione (opcional)</option>
                            <?php foreach ($logias as $logia): ?>
                                <option value="<?php echo $logia; ?>"><?php echo $logia; ?></option>
                            <?php endforeach; ?>
                            <option value="Otra">Otra (especificar en observaciones)</option>
                            <option value="Exterior">Exterior del país</option>
                        </select>
                        <small style="color: #6c757d;">Opcional - Donde fue iniciado</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grados_que_tienen">Grados que Posee:</label>
                        <textarea id="grados_que_tienen" name="grados_que_tienen" class="form-control" rows="2"
                            placeholder="Ejemplo: 3°, 14°, 18°, 30°, 32°"></textarea>
                        <small style="color: #6c757d;">Opcional - Liste todos los grados masónicos que posee</small>
                    </div>
                    <div class="form-group">
                        <label for="logia_perfeccion">Logia de Perfección:</label>
                        <input type="text" id="logia_perfeccion" name="logia_perfeccion" class="form-control"
                            placeholder="Nombre de la logia de perfección">
                        <small style="color: #6c757d;">Opcional</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descripcion_grado_capitular">Descripción del Grado Capitular:</label>
                        <textarea id="descripcion_grado_capitular" name="descripcion_grado_capitular" class="form-control" rows="3"
                            placeholder="Describa experiencias, responsabilidades o ceremonias relacionadas con grados capitulares..."></textarea>
                        <small style="color: #6c757d;">Opcional</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="certificados">Certificados Masónicos:</label>
                        <input type="file" id="certificados" name="certificados[]" multiple
                            accept="image/*,application/pdf" class="form-control">
                        <small style="color: #6c757d;">Opcional - Fotos de certificados, diplomas, documentos masónicos (PDF, JPG, PNG)</small>
                    </div>
                </div>
            </section>
 -->
            <!-- SECCIÓN 3: DATOS LOGIALES -->
            <section class="section">
                <h2><i class="fas fa-university"></i> DATOS LOGIALES</h2>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #856404;"></i>
                    <strong style="color: #856404;">Importante:</strong> Los campos marcados con * son obligatorios para completar el registro.
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="logia_actual">Logia Actual:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <select id="logia_actual" name="institucion_actual" class="form-control" required style="border: 2px solid #ffc107;">
                            <option value="">LOGIA ACTUAL</option>
                            <?php foreach ($logias as $logia): ?>
                                <option value="<?php echo $logia; ?>"><?php echo $logia; ?></option>
                            <?php endforeach; ?>
                            <option value="Otra">Otra (especificar en observaciones)</option>
                        </select>
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio</small>
                    </div>
                    <div class="form-group">
                        <label for="grado_masonico">Grado Masónico:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <select id="grado_masonico" name="nivel_actual" class="form-control" required style="border: 2px solid #ffc107;">
                            <option value="">⚠️ SELECCIONE SU GRADO</option>
                            <option value="aprendiz">Aprendiz (1°)</option>
                            <option value="companero">Compañero (2°)</option>
                            <option value="maestro">Maestro (3°)</option>
                        </select>
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_iniciacion">Fecha de Iniciación:<span class="required" style="color: #dc3545; font-weight: bold;">*</span></label>
                        <input type="date" id="fecha_iniciacion" name="fecha_ingreso" class="form-control" required
                            style="border: 2px solid #ffc107;" min="1950-01-01" max="<?php echo date('Y-m-d'); ?>">
                        <small style="color: #dc3545; font-weight: 500;">⚠️ Este campo es obligatorio - Fecha cuando fue iniciado en la masonería</small>
                    </div>
                    <div class="form-group">
                        <label for="logia_iniciacion">Logia de Iniciación:</label>
                        <select id="logia_iniciacion" name="logia_iniciacion" class="form-control">
                            <option value="">Seleccione (opcional)</option>
                            <?php foreach ($logias as $logia): ?>
                                <option value="<?php echo $logia; ?>"><?php echo $logia; ?></option>
                            <?php endforeach; ?>
                            <option value="Otra">Otra (especificar en observaciones)</option>
                            <option value="Exterior">Exterior del país</option>
                        </select>
                        <small style="color: #6c757d;">Opcional - Donde fue iniciado</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grados_que_tienen">Grados que Posee:</label>
                        <textarea id="grados_que_tienen" name="grados_que_tienen" class="form-control" rows="2"
                            placeholder="Ejemplo: 3°, 14°, 18°, 30°, 32°"></textarea>
                        <small style="color: #6c757d;">Opcional - Liste todos los grados masónicos que posee</small>
                    </div>
                    <div class="form-group">
                        <label for="logia_perfeccion">Logia de Perfección:</label>
                        <input type="text" id="logia_perfeccion" name="logia_perfeccion" class="form-control"
                            placeholder="Nombre de la logia de perfección">
                        <small style="color: #6c757d;">Opcional</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descripcion_grado_capitular">Descripción del Grado Capitular:</label>
                        <textarea id="descripcion_grado_capitular" name="descripcion_grado_capitular" class="form-control" rows="3"
                            placeholder="Describa experiencias, responsabilidades o ceremonias relacionadas con grados capitulares..."></textarea>
                        <small style="color: #6c757d;">Opcional</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="certificados">Certificados Masónicos:</label>
                        <input type="file" id="certificados" name="certificados[]" multiple
                            accept="image/*,application/pdf" class="form-control">
                        <small style="color: #6c757d;">Opcional - Fotos de certificados, diplomas, documentos masónicos (PDF, JPG, PNG)</small>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 4: DATOS MÉDICOS -->
            <section class="section">
                <h2><i class="fas fa-heartbeat"></i> DATOS MÉDICOS</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grupo_sanguineo">Grupo Sanguíneo:<span class="required">*</span></label>
                        <select id="grupo_sanguineo" name="grupo_sanguineo" class="form-control" required>
                            <option value="">Seleccione</option>
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
                        <label for="enfermedades_base">Enfermedades:</label>
                        <textarea id="enfermedades_base" name="enfermedades_base" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Seguro Privado:</label>
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <label><input type="radio" name="seguro_privado" value="Si"> Sí</label>
                            <label><input type="radio" name="seguro_privado" value="No" checked> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>IPS:</label>
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <label><input type="radio" name="ips" value="Si"> Sí</label>
                            <label><input type="radio" name="ips" value="No" checked> No</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="alergias">Alergias:</label>
                        <textarea id="alergias" name="alergias" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="numero_emergencia">Teléfono Emergencia:<span class="required">*</span></label>
                        <input type="tel" id="numero_emergencia" name="numero_emergencia" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="contacto_emergencia">Contacto Emergencia:<span class="required">*</span></label>
                        <input type="text" id="contacto_emergencia" name="contacto_emergencia" class="form-control" required>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 5: ADICIONAL -->
            <section class="section">
                <h2><i class="fas fa-plus-circle"></i> INFORMACIÓN ADICIONAL</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="foto_hermano">Foto del Hermano:</label>
                        <input type="file" id="foto_hermano" name="foto_hermano" accept="image/*" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <!-- Términos -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin: 2rem 0;">
                    <label style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <input type="checkbox" id="acepta_terminos" name="acepta_terminos" value="1" required style="margin-top: 0.25rem;">
                        <span style="font-size: 0.9rem; line-height: 1.5;">
                            <span class="required">*</span> Acepto los términos y condiciones del registro.
                            Confirmo que toda la información es veraz y autorizo el procesamiento de datos para fines logiales.
                        </span>
                    </label>
                </div>
            </section>

        </form>
    </div>

    <!-- Botón envío fijo -->
    <div class="submit-fixed">
        <button type="submit" form="registration-form" class="submit-btn" id="submit-button" disabled>
            <i class="fas fa-save"></i>
            <span>Completar Registro</span>
        </button>
    </div>

    <script>
        let totalFields = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const requiredFields = document.querySelectorAll('[required]');
            totalFields = requiredFields.length;

            // Seguimiento de progreso
            document.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('input', updateProgress);
                field.addEventListener('change', updateProgress);
            });

            // Formateo de teléfonos
            document.querySelectorAll('#telefono, #numero_emergencia').forEach(input => {
                input.addEventListener('input', formatPhone);
            });

            // Formateo CI
            document.getElementById('ci').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 8);
            });

            // Validación edad
            document.getElementById('fecha_nacimiento').addEventListener('change', function(e) {
                const age = Math.floor((new Date() - new Date(e.target.value)) / (365.25 * 24 * 60 * 60 * 1000));
                if (age < 18) {
                    showNotification('Debe ser mayor de 18 años', 'error');
                    e.target.classList.add('field-error');
                } else {
                    e.target.classList.remove('field-error');
                }
            });

            // Envío formulario
            document.getElementById('registration-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Limpiar errores previos
                document.querySelectorAll('.field-error').forEach(field => {
                    field.classList.remove('field-error');
                    field.style.border = '';
                });

                if (validateForm()) {
                    submitForm();
                } else {
                    showNotification('❌ Por favor complete todos los campos obligatorios marcados con (*)', 'error');
                }
            });

            updateProgress();
            showNotification('Por favor complete cada item del formulario', 'success');
        });

        function updateProgress() {
            const requiredFields = document.querySelectorAll('[required]');
            let completed = 0;

            requiredFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (field.checked) {
                        completed++;
                        field.closest('div').classList.add('field-completed');
                    }
                } else {
                    if (field.value.trim()) {
                        completed++;
                        field.classList.add('field-completed');
                        field.classList.remove('field-error');
                    } else {
                        field.classList.remove('field-completed');
                    }
                }
            });

            const percentage = Math.round((completed / totalFields) * 100);
            document.getElementById('progress-bar').style.width = percentage + '%';
            document.getElementById('progress-percentage').textContent = percentage + '%';

            const submitBtn = document.getElementById('submit-button');
            if (percentage >= 80) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fas fa-save"></i><span>Enviar Registro (${percentage}%)</span>`;
            } else {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-edit"></i><span>Complete más campos (${percentage}%)</span>`;
            }
        }

        function formatPhone(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0') && value.length <= 10) {
                value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
            }
            e.target.value = value;
        }

        function validateForm() {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            let missingFields = [];

            requiredFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (!field.checked) {
                        field.closest('div').classList.add('field-error');
                        missingFields.push('Términos y Condiciones');
                        isValid = false;
                    }
                } else {
                    if (!field.value.trim()) {
                        field.classList.add('field-error');
                        field.style.border = '2px solid #dc3545';

                        // Agregar nombre del campo faltante
                        const label = field.closest('.form-group').querySelector('label');
                        if (label) {
                            missingFields.push(label.textContent.replace('*', '').replace(':', ''));
                        }
                        isValid = false;
                    } else {
                        field.style.border = '';
                    }
                }
            });

            if (!isValid) {
                const message = `Faltan campos obligatorios:\n• ${missingFields.join('\n• ')}`;
                showNotification(message, 'error');

                // Hacer scroll al primer campo faltante
                const firstError = document.querySelector('.field-error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }
            }

            return isValid;
        }

        function submitForm() {
            const form = document.getElementById('registration-form');
            const submitBtn = document.getElementById('submit-button');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div style="width:20px;height:20px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></div><span>Enviando...</span>';

            fetch('processv2.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('✅')) {
                        showNotification('¡Registro enviado exitosamente!', 'success');
                        window.open('', '_blank').document.write(data);
                        setTimeout(() => {
                            form.reset();
                            updateProgress();
                        }, 3000);
                    } else {
                        window.open('', '_blank').document.write(data);
                        showNotification('Revisar resultados en nueva ventana', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error de conexión', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    updateProgress();
                });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" style="margin-left: auto; background: none; border: none; font-size: 1.2rem; cursor: pointer;">×</button>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

</body>

</html>