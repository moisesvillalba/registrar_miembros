<?php
/**
 * CONFIGURACIÓN FLEXIBLE DE CAMPOS DEL FORMULARIO
 * config/form_settings.php
 * Permite cambiar entre combo/select y campos de texto libre
 */

// ============================================================================
// CONFIGURACIÓN DE COMBOS vs TEXTBOX
// true = Mostrar como combo/select con opciones predefinidas
// false = Mostrar como campo de texto libre
// ============================================================================

// Datos Personales
define('USE_CIUDAD_COMBO', false);           // Lista de ciudades de Paraguay
define('USE_PROFESION_COMBO', false);        // Lista de profesiones comunes

// Datos Logiales/Masónicos
define('USE_NIVEL_ACTUAL_COMBO', true);      // Niveles educativos
define('USE_NIVEL_SUPERIOR_COMBO', true);    // Grados masónicos superiores

// Datos Médicos
define('USE_GRUPO_SANGUINEO_COMBO', true);   // Tipos de sangre (A+, A-, etc.)

// ============================================================================
// CONFIGURACIÓN DE ARCHIVOS
// ============================================================================

// Configuración de subida de archivos
define('MAX_FILE_SIZE_MB', 5);               // Tamaño máximo en MB
define('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif'); // Tipos de imagen permitidos
define('ALLOWED_DOC_TYPES', 'pdf,doc,docx'); // Tipos de documento permitidos

// Rutas de archivos
define('UPLOAD_PATH_FOTOS', 'uploads/fotos/');
define('UPLOAD_PATH_DOCUMENTOS', 'uploads/documentos/');

// ============================================================================
// OPCIONES PARA LOS COMBOS
// ============================================================================

// Ciudades principales de Paraguay
$CIUDADES_PARAGUAY = [
    'Asunción',
    'Ciudad del Este',
    'San Lorenzo',
    'Luque',
    'Capiatá',
    'Lambaré',
    'Fernando de la Mora',
    'Nemby',
    'Encarnación',
    'Pedro Juan Caballero',
    'Coronel Oviedo',
    'Concepción',
    'Villarrica',
    'Caaguazú',
    'Paraguarí',
    'Itauguá',
    'Mariano Roque Alonso',
    'Ñemby',
    'Villa Elisa',
    'Caacupé'
];

// Profesiones comunes
$PROFESIONES_COMUNES = [
    'Abogado/a',
    'Médico/a',
    'Ingeniero/a',
    'Contador/a',
    'Professor/a',
    'Comerciante',
    'Empresario/a',
    'Arquitecto/a',
    'Economista',
    'Administrador/a',
    'Técnico/a',
    'Empleado Público',
    'Empleado Privado',
    'Jubilado/a',
    'Estudiante',
    'Independiente',
    'Otro'
];

// Niveles educativos
$NIVELES_EDUCATIVOS = [
    'Primaria',
    'Secundaria',
    'Técnico Superior',
    'Universitario',
    'Postgrado',
    'Maestría',
    'Doctorado'
];

// Grados masónicos superiores del REAA (Rito Escocés Antiguo y Aceptado)
$GRADOS_SUPERIORES = [
    // Logia de Perfección (4° - 14°)
    '4° - Maestro Secreto',
    '5° - Maestro Perfecto',
    '6° - Secretario Íntimo',
    '7° - Preboste y Juez',
    '8° - Intendente de los Edificios',
    '9° - Maestro Elegido de los Nueve',
    '10° - Ilustre Elegido de los Quince',
    '11° - Sublime Caballero Elegido',
    '12° - Gran Maestro Arquitecto',
    '13° - Real Arco',
    '14° - Gran Elegido Perfecto y Sublime Masón',
    
    // Capítulo Rosacruz (15° - 18°)
    '15° - Caballero de Oriente',
    '16° - Príncipe de Jerusalén',
    '17° - Caballero de Oriente y Occidente',
    '18° - Soberano Príncipe Rosacruz',
    
    // Areópago (19° - 30°)
    '19° - Gran Pontífice',
    '20° - Venerable Gran Maestro',
    '21° - Noaquita o Caballero Prusiano',
    '22° - Caballero del Real Hacha',
    '23° - Jefe del Tabernáculo',
    '24° - Príncipe del Tabernáculo',
    '25° - Caballero de la Serpiente de Bronce',
    '26° - Príncipe de la Merced',
    '27° - Soberano Comendador del Templo',
    '28° - Caballero del Sol',
    '29° - Gran Caballero Escocés de San Andrés',
    '30° - Gran Elegido Caballero Kadosh',
    
    // Consistorio (31° - 32°)
    '31° - Gran Inspector Inquisidor Comendador',
    '32° - Sublime Príncipe del Real Secreto',
    
    // Grado Supremo
    '33° - Soberano Gran Inspector General',
    
    // Opciones especiales
    'Múltiples Grados',
    'Otro Rito',
    'En Proceso',
    'No Aplica'
];

// Tipos de sangre
$GRUPOS_SANGUINEOS = [
    'A+', 'A-',
    'B+', 'B-',
    'AB+', 'AB-',
    'O+', 'O-'
];

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Verificar si un campo debe usar combo
 */
function useComboForField($fieldName) {
    $combos = [
        'ciudad' => USE_CIUDAD_COMBO,
        'profesion' => USE_PROFESION_COMBO,
        'nivel_actual' => USE_NIVEL_ACTUAL_COMBO,
        'nivel_superior' => USE_NIVEL_SUPERIOR_COMBO,
        'grupo_sanguineo' => USE_GRUPO_SANGUINEO_COMBO,
    ];
    
    return $combos[$fieldName] ?? true;
}

/**
 * Obtener opciones para un campo específico
 */
function getOptionsForField($fieldName) {
    global $CIUDADES_PARAGUAY, $PROFESIONES_COMUNES, $NIVELES_EDUCATIVOS, 
           $GRADOS_SUPERIORES, $GRUPOS_SANGUINEOS;
    
    $options = [
        'ciudad' => $CIUDADES_PARAGUAY,
        'profesion' => $PROFESIONES_COMUNES,
        'nivel_actual' => $NIVELES_EDUCATIVOS,
        'nivel_superior' => $GRADOS_SUPERIORES,
        'grupo_sanguineo' => $GRUPOS_SANGUINEOS,
    ];
    
    return $options[$fieldName] ?? [];
}

/**
 * Renderizar campo combo o textbox según configuración
 */
function renderSelectField($fieldName, $selectedValue = '', $required = false, $extraAttributes = '') {
    $useCombo = useComboForField($fieldName);
    $requiredAttr = $required ? 'required' : '';
    $class = "form-control";
    
    if ($useCombo) {
        $options = getOptionsForField($fieldName);
        $html = "<select name='$fieldName' id='$fieldName' class='$class' $requiredAttr $extraAttributes>";
        $html .= "<option value=''>Seleccionar...</option>";
        
        foreach ($options as $option) {
            $selected = ($selectedValue == $option) ? 'selected' : '';
            $html .= "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
        }
        
        $html .= "</select>";
    } else {
        $html = "<input type='text' name='$fieldName' id='$fieldName' class='$class' value='" . htmlspecialchars($selectedValue) . "' $requiredAttr $extraAttributes>";
    }
    
    return $html;
}

/**
 * Obtener configuración completa
 */
function getAllSettings() {
    return [
        'USE_CIUDAD_COMBO' => USE_CIUDAD_COMBO,
        'USE_PROFESION_COMBO' => USE_PROFESION_COMBO,
        'USE_NIVEL_ACTUAL_COMBO' => USE_NIVEL_ACTUAL_COMBO,
        'USE_NIVEL_SUPERIOR_COMBO' => USE_NIVEL_SUPERIOR_COMBO,
        'USE_GRUPO_SANGUINEO_COMBO' => USE_GRUPO_SANGUINEO_COMBO,
        'MAX_FILE_SIZE_MB' => MAX_FILE_SIZE_MB,
        'ALLOWED_IMAGE_TYPES' => ALLOWED_IMAGE_TYPES,
        'ALLOWED_DOC_TYPES' => ALLOWED_DOC_TYPES,
    ];
}

/**
 * Validar tipo de archivo
 */
function validateFileType($filename, $type = 'image') {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if ($type === 'image') {
        $allowed = explode(',', ALLOWED_IMAGE_TYPES);
    } else {
        $allowed = explode(',', ALLOWED_DOC_TYPES);
    }
    
    return in_array($extension, $allowed);
}

/**
 * Validar tamaño de archivo
 */
function validateFileSize($fileSize) {
    $maxSize = MAX_FILE_SIZE_MB * 1024 * 1024; // Convertir MB a bytes
    return $fileSize <= $maxSize;
}

// ============================================================================
// CONSTANTES ADICIONALES
// ============================================================================

// Mensajes de validación
define('MSG_FILE_TOO_LARGE', 'El archivo es demasiado grande. Máximo ' . MAX_FILE_SIZE_MB . 'MB permitido.');
define('MSG_INVALID_IMAGE_TYPE', 'Tipo de imagen no válido. Permitidos: ' . ALLOWED_IMAGE_TYPES);
define('MSG_INVALID_DOC_TYPE', 'Tipo de documento no válido. Permitidos: ' . ALLOWED_DOC_TYPES);

// Configuración de display
define('SHOW_PHOTO_PREVIEW', true);          // Mostrar preview de fotos
define('SHOW_FILE_UPLOAD_PROGRESS', true);   // Mostrar progreso de subida
define('ENABLE_DRAG_DROP', true);            // Habilitar drag & drop

// ============================================================================
// DEBUG MODE (Cambiar a false en producción)
// ============================================================================
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

?>

<!-- 
===============================================================================
CONFIGURACIÓN FLEXIBLE COMPLETADA

Campos configurables:
✅ Ciudad (Lista Paraguay / Texto libre)
✅ Profesión (Lista común / Texto libre)  
✅ Nivel Actual (Lista educativa / Texto libre)
✅ Nivel Superior (Grados masónicos / Texto libre)
✅ Grupo Sanguíneo (Tipos sangre / Texto libre)

Uso:
- Cambiar define() para alternar combo/textbox
- Usar renderSelectField() en formularios
- Validaciones automáticas incluidas

===============================================================================
-->