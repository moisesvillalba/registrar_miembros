<?php
/**
 * Script automático para insertar datos de prueba
 * Archivo: insert_test_data.php
 * Adaptado a la estructura real de la base de datos
 */

session_start();

// Configuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>Inserción Automática de Datos de Prueba</title>\n";
echo "<link rel='stylesheet' href='assets/css/styles.css'>\n";
echo "<style>\n";
echo ".container { max-width: 900px; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }\n";
echo ".success { color: #10b981; background: #f0fdf4; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #10b981; }\n";
echo ".error { color: #ef4444; background: #fef2f2; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ef4444; }\n";
echo ".info { background: #e0f2fe; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #0277bd; }\n";
echo ".member-card { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 8px; border: 1px solid #dee2e6; }\n";
echo ".data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 10px 0; }\n";
echo ".data-item { padding: 10px; background: white; border-radius: 6px; border: 1px solid #e9ecef; }\n";
echo ".data-label { font-weight: 600; color: #495057; font-size: 13px; }\n";
echo ".data-value { color: #212529; margin-top: 2px; font-size: 14px; }\n";
echo ".btn { display: inline-block; padding: 12px 24px; background: #8B0000; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }\n";
echo ".btn:hover { background: #5D0000; }\n";
echo "@media (max-width: 768px) { .data-grid { grid-template-columns: 1fr; } }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<div class='container'>\n";

echo "<h1>🚀 Script de Inserción Automática de Datos de Prueba</h1>\n";
echo "<p>Este script insertará datos realistas de miembros masónicos en tu base de datos para pruebas.</p>\n";

// Incluir database
try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn === null) {
        throw new Exception('No se pudo conectar a la base de datos: ' . ($database->connection_error ?? 'Error desconocido'));
    }

    echo "<div class='success'>✅ Conexión a base de datos exitosa</div>\n";

} catch (Exception $e) {
    echo "<div class='error'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    echo "</div></body></html>";
    exit;
}

// Datos de prueba realistas basados en tu estructura de BD
$miembros_prueba = [
    [
        'ci' => '1234567',
        'nombre' => 'Juan Carlos',
        'apellido' => 'González Martínez',
        'fecha_nacimiento' => '1980-05-15',
        'lugar' => 'Asunción',
        'profesion' => 'Ingeniero Civil',
        'direccion' => 'Av. Eusebio Ayala 1234, c/ Brasil',
        'telefono' => '+595 (981) 123-456',
        'ciudad' => 'Asunción',
        'barrio' => 'Centro',
        'esposa' => 'María Elena Rodríguez',
        'hijos' => 'Carlos González (15 años), Ana González (12 años)',
        'madre' => 'Rosa Martínez de González',
        'padre' => 'Pedro González López',
        'direccion_laboral' => 'Av. España 567, Torre Empresarial',
        'empresa' => 'Constructora del Este S.A.',
        'descripcion_otros_trabajos' => 'Consultoría independiente en proyectos de infraestructura',
        'institucion_actual' => 'A∴R∴L∴ Nueva Alianza Nº 1',
        'nivel_actual' => 'maestro',
        'fecha_ingreso' => '2010-03-20',
        'institucion_ingreso' => 'A∴R∴L∴ Nueva Alianza Nº 1',
        'grados_que_tienen' => '3°, 14°, 18°, 30°',
        'logia_perfeccion' => 'Logia de Perfección Pitágoras Nº 1',
        'descripcion_grado_capitular' => 'Participa activamente en ceremonias del Arco Real, responsable de instrucción de nuevos compañeros.',
        'grupo_sanguineo' => 'O+',
        'enfermedades_base' => 'Hipertensión controlada',
        'seguro_privado' => 'Si',
        'ips' => 'Si',
        'alergias' => 'Penicilina',
        'numero_emergencia' => '+595 (982) 654-321',
        'contacto_emergencia' => 'María Elena Rodríguez (Esposa)'
    ],
    [
        'ci' => '2345678',
        'nombre' => 'Pedro Antonio',
        'apellido' => 'Martínez Silva',
        'fecha_nacimiento' => '1975-08-22',
        'lugar' => 'San Lorenzo',
        'profesion' => 'Abogado',
        'direccion' => 'Calle Brasil 890, Barrio San Roque',
        'telefono' => '+595 (983) 789-012',
        'ciudad' => 'San Lorenzo',
        'barrio' => 'San Roque',
        'esposa' => 'Ana Beatriz Fernández',
        'hijos' => 'Pedro Martínez Jr. (18 años), Sofía Martínez (16 años)',
        'madre' => 'Carmen Silva de Martínez',
        'padre' => 'Antonio Martínez Pérez',
        'direccion_laboral' => 'Av. Mariscal López 123, Edificio Professional',
        'empresa' => 'Estudio Jurídico Martínez & Asociados',
        'descripcion_otros_trabajos' => 'Mediador certificado en conflictos comerciales',
        'institucion_actual' => 'A∴R∴L∴ Renacer Nº 2',
        'nivel_actual' => 'companero',
        'fecha_ingreso' => '2015-07-10',
        'institucion_ingreso' => 'A∴R∴L∴ Renacer Nº 2',
        'grados_que_tienen' => '3°, 14°',
        'logia_perfeccion' => 'Logia de Perfección Salomón Nº 2',
        'descripcion_grado_capitular' => 'En proceso de instrucción para grados superiores, asiste regularmente a tenidas de instrucción.',
        'grupo_sanguineo' => 'A+',
        'enfermedades_base' => '',
        'seguro_privado' => 'Si',
        'ips' => 'No',
        'alergias' => '',
        'numero_emergencia' => '+595 (984) 345-678',
        'contacto_emergencia' => 'Ana Beatriz Fernández (Esposa)'
    ],
    [
        'ci' => '3456789',
        'nombre' => 'Roberto Miguel',
        'apellido' => 'Fernández Castro',
        'fecha_nacimiento' => '1970-12-03',
        'lugar' => 'Ciudad del Este',
        'profesion' => 'Médico Cirujano',
        'direccion' => 'Av. Monseñor Rodríguez 456',
        'telefono' => '+595 (985) 234-567',
        'ciudad' => 'Ciudad del Este',
        'barrio' => 'Centro',
        'esposa' => 'Claudia Morales',
        'hijos' => 'Roberto Fernández (20 años), Lucia Fernández (17 años), Diego Fernández (14 años)',
        'madre' => 'Isabel Castro de Fernández',
        'padre' => 'Miguel Fernández Romero',
        'direccion_laboral' => 'Hospital Regional de Ciudad del Este',
        'empresa' => 'Instituto Médico del Este',
        'descripcion_otros_trabajos' => 'Cirugías privadas los fines de semana, clínica particular',
        'institucion_actual' => 'A∴R∴L∴ Hermandad Blanca Nº 3',
        'nivel_actual' => 'maestro',
        'fecha_ingreso' => '2005-11-15',
        'institucion_ingreso' => 'A∴R∴L∴ Hermandad Blanca Nº 3',
        'grados_que_tienen' => '3°, 14°, 18°, 30°, 32°',
        'logia_perfeccion' => 'Logia de Perfección Hiram Nº 3',
        'descripcion_grado_capitular' => 'Past Master de su logia, instructor de grados simbólicos, miembro activo del Supremo Consejo.',
        'grupo_sanguineo' => 'B+',
        'enfermedades_base' => 'Diabetes tipo 2 controlada',
        'seguro_privado' => 'Si',
        'ips' => 'Si',
        'alergias' => 'Ibuprofeno, Aspirina',
        'numero_emergencia' => '+595 (986) 456-789',
        'contacto_emergencia' => 'Claudia Morales (Esposa)'
    ],
    [
        'ci' => '4567890',
        'nombre' => 'Luis Fernando',
        'apellido' => 'Benítez Ocampo',
        'fecha_nacimiento' => '1985-04-18',
        'lugar' => 'Encarnación',
        'profesion' => 'Contador Público',
        'direccion' => 'Calle Memmel 789, Barrio San Isidro',
        'telefono' => '+595 (987) 345-678',
        'ciudad' => 'Encarnación',
        'barrio' => 'San Isidro',
        'esposa' => '',
        'hijos' => '',
        'madre' => 'Teresa Ocampo de Benítez',
        'padre' => 'Fernando Benítez Villalba',
        'direccion_laboral' => 'Av. Irrazábal 234, Oficina 501',
        'empresa' => 'Estudio Contable Benítez',
        'descripcion_otros_trabajos' => 'Asesoría tributaria para pequeñas empresas',
        'institucion_actual' => 'A∴R∴L∴ Apocalipsis Nº 4',
        'nivel_actual' => 'aprendiz',
        'fecha_ingreso' => '2023-09-21',
        'institucion_ingreso' => 'A∴R∴L∴ Apocalipsis Nº 4',
        'grados_que_tienen' => '3°',
        'logia_perfeccion' => '',
        'descripcion_grado_capitular' => 'Recién iniciado, en proceso de aprendizaje de los rituales básicos.',
        'grupo_sanguineo' => 'AB+',
        'enfermedades_base' => '',
        'seguro_privado' => 'No',
        'ips' => 'Si',
        'alergias' => '',
        'numero_emergencia' => '+595 (988) 567-890',
        'contacto_emergencia' => 'Teresa Ocampo (Madre)'
    ],
    [
        'ci' => '5678901',
        'nombre' => 'Carlos Alberto',
        'apellido' => 'Rojas Mendoza',
        'fecha_nacimiento' => '1972-09-10',
        'lugar' => 'Luque',
        'profesion' => 'Arquitecto',
        'direccion' => 'Barrio Mburicaó, Casa 123',
        'telefono' => '+595 (989) 456-789',
        'ciudad' => 'Luque',
        'barrio' => 'Mburicaó',
        'esposa' => 'Mónica Sánchez',
        'hijos' => 'Carla Rojas (22 años), Pablo Rojas (19 años)',
        'madre' => 'Elena Mendoza de Rojas',
        'padre' => 'Alberto Rojas García',
        'direccion_laboral' => 'Av. Aviadores del Chaco 567',
        'empresa' => 'Rojas Arquitectura & Diseño',
        'descripcion_otros_trabajos' => 'Diseño de interiores, restauración de edificios históricos',
        'institucion_actual' => 'A∴R∴L∴ Reconquista Nº 5',
        'nivel_actual' => 'maestro',
        'fecha_ingreso' => '2008-05-25',
        'institucion_ingreso' => 'A∴R∴L∴ Reconquista Nº 5',
        'grados_que_tienen' => '3°, 14°, 18°',
        'logia_perfeccion' => 'Logia de Perfección Cibeles Nº 4',
        'descripcion_grado_capitular' => 'Especialista en simbolismo masónico, dicta conferencias sobre arquitectura sagrada.',
        'grupo_sanguineo' => 'O-',
        'enfermedades_base' => 'Asma leve',
        'seguro_privado' => 'Si',
        'ips' => 'No',
        'alergias' => 'Polen, ácaros',
        'numero_emergencia' => '+595 (990) 123-456',
        'contacto_emergencia' => 'Mónica Sánchez (Esposa)'
    ]
];

echo "<div class='info'>\n";
echo "<p><strong>📊 Se insertarán " . count($miembros_prueba) . " miembros de prueba</strong></p>\n";
echo "<p>Estos datos incluyen información completa de todas las secciones del formulario.</p>\n";
echo "</div>\n";

// Preparar consulta SQL con todos los campos de tu BD
$sql = "INSERT INTO miembros (
    ci, nombre, apellido, fecha_nacimiento, lugar, profesion, direccion, telefono, ciudad, barrio,
    esposa, hijos, madre, padre, direccion_laboral, empresa, descripcion_otros_trabajos,
    institucion_actual, nivel_actual, fecha_ingreso, institucion_ingreso,
    grados_que_tienen, logia_perfeccion, descripcion_grado_capitular,
    grupo_sanguineo, enfermedades_base, seguro_privado, ips, alergias,
    numero_emergencia, contacto_emergencia
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$insertados = 0;
$errores = 0;

foreach ($miembros_prueba as $miembro) {
    try {
        // Verificar si ya existe el CI
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM miembros WHERE ci = ?");
        $check_stmt->execute([$miembro['ci']]);
        
        if ($check_stmt->fetchColumn() > 0) {
            echo "<div class='member-card'>\n";
            echo "<h4 style='color: #f59e0b;'>⚠️ CI Duplicado: " . htmlspecialchars($miembro['ci']) . "</h4>\n";
            echo "<p><strong>" . htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']) . "</strong> ya existe en la base de datos.</p>\n";
            echo "</div>\n";
            continue;
        }

        // Insertar miembro
        $resultado = $stmt->execute([
            $miembro['ci'], $miembro['nombre'], $miembro['apellido'], $miembro['fecha_nacimiento'],
            $miembro['lugar'], $miembro['profesion'], $miembro['direccion'], $miembro['telefono'],
            $miembro['ciudad'], $miembro['barrio'], $miembro['esposa'], $miembro['hijos'],
            $miembro['madre'], $miembro['padre'], $miembro['direccion_laboral'], $miembro['empresa'],
            $miembro['descripcion_otros_trabajos'], $miembro['institucion_actual'], $miembro['nivel_actual'],
            $miembro['fecha_ingreso'], $miembro['institucion_ingreso'], $miembro['grados_que_tienen'],
            $miembro['logia_perfeccion'], $miembro['descripcion_grado_capitular'], $miembro['grupo_sanguineo'],
            $miembro['enfermedades_base'], $miembro['seguro_privado'], $miembro['ips'], $miembro['alergias'],
            $miembro['numero_emergencia'], $miembro['contacto_emergencia']
        ]);

        if ($resultado) {
            $nuevo_id = $conn->lastInsertId();
            $insertados++;

            echo "<div class='member-card' style='border-left: 4px solid #10b981;'>\n";
            echo "<h4 style='color: #10b981;'>✅ Insertado: " . htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']) . "</h4>\n";
            
            echo "<div class='data-grid'>\n";
            echo "<div class='data-item'><div class='data-label'>ID:</div><div class='data-value'>#" . $nuevo_id . "</div></div>\n";
            echo "<div class='data-item'><div class='data-label'>CI:</div><div class='data-value'>" . htmlspecialchars($miembro['ci']) . "</div></div>\n";
            echo "<div class='data-item'><div class='data-label'>Profesión:</div><div class='data-value'>" . htmlspecialchars($miembro['profesion']) . "</div></div>\n";
            echo "<div class='data-item'><div class='data-label'>Logia:</div><div class='data-value'>" . htmlspecialchars($miembro['institucion_actual']) . "</div></div>\n";
            echo "<div class='data-item'><div class='data-label'>Grado:</div><div class='data-value'>" . htmlspecialchars($miembro['nivel_actual']) . "</div></div>\n";
            echo "<div class='data-item'><div class='data-label'>Grupo Sanguíneo:</div><div class='data-value'>" . htmlspecialchars($miembro['grupo_sanguineo']) . "</div></div>\n";
            echo "</div>\n";
            echo "</div>\n";
        }

    } catch (Exception $e) {
        $errores++;
        echo "<div class='member-card' style='border-left: 4px solid #ef4444;'>\n";
        echo "<h4 style='color: #ef4444;'>❌ Error: " . htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']) . "</h4>\n";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
        echo "</div>\n";
    }
}

// Estadísticas finales
echo "<div class='success'>\n";
echo "<h3>📈 Resumen de Inserción</h3>\n";
echo "<ul>\n";
echo "<li><strong>Miembros insertados:</strong> " . $insertados . "</li>\n";
echo "<li><strong>Errores:</strong> " . $errores . "</li>\n";
echo "<li><strong>Total procesados:</strong> " . count($miembros_prueba) . "</li>\n";
echo "</ul>\n";
echo "</div>\n";

// Mostrar total actual en la BD
try {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM miembros");
    $count_stmt->execute();
    $total_miembros = $count_stmt->fetch()['total'];
    
    echo "<div class='info'>\n";
    echo "<p><strong>📊 Total de miembros en la base de datos:</strong> " . $total_miembros . "</p>\n";
    echo "</div>\n";
} catch (Exception $e) {
    echo "<p style='color: #f59e0b;'>⚠️ No se pudo obtener el total de miembros</p>\n";
}

echo "<div style='margin-top: 30px; text-align: center;'>\n";
echo "<a href='index.php' class='btn'>🔙 Ir al Formulario</a>\n";
echo "<a href='processv2.php' class='btn' style='background: #6c757d;'>🔧 Probar processv2.php</a>\n";
echo "</div>\n";

echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; font-size: 12px; color: #6c757d; text-align: center;'>\n";
echo "<p><strong>Script de Prueba - Sistema de Registro Masónico</strong></p>\n";
echo "<p>Datos insertados el " . date('d/m/Y H:i:s') . " | Estructura de BD verificada</p>\n";
echo "</div>\n";

echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
?>