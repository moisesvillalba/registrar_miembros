<?php
/**
 * SCRIPT DE PRUEBA - INSERCIÓN AUTOMÁTICA
 * Gran Logia de Libres y Aceptados Masones del Paraguay
 * 
 * USAR SOLO PARA PRUEBAS - ELIMINAR EN PRODUCCIÓN
 */

// Configuración para mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    // Incluir conexión a base de datos
    require_once 'config/database.php';
    
    echo "<h1>🧪 PRUEBA DE BASE DE DATOS</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .test-container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #10b981; background: #d1fae5; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #3b82f6; background: #dbeafe; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #f59e0b; background: #fef3c7; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-info { background: #3b82f6; color: white; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>";
    
    echo "<div class='test-container'>";
    
    // 1. PROBAR CONEXIÓN
    echo "<h2>1️⃣ PROBANDO CONEXIÓN A BASE DE DATOS</h2>";
    
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn === null) {
        throw new Exception("❌ No se pudo conectar a la base de datos");
    }
    
    echo "<div class='success'>✅ Conexión exitosa a la base de datos</div>";
    
    // 2. VERIFICAR TABLA
    echo "<h2>2️⃣ VERIFICANDO TABLA 'miembros'</h2>";
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'miembros'");
    if ($tableCheck->num_rows === 0) {
        echo "<div class='warning'>⚠️ Tabla 'miembros' no existe. Creando...</div>";
        $database->initializeDatabase();
        echo "<div class='success'>✅ Tabla 'miembros' creada correctamente</div>";
    } else {
        echo "<div class='success'>✅ Tabla 'miembros' existe</div>";
    }
    
    // 3. MOSTRAR ESTRUCTURA DE TABLA
    echo "<h2>3️⃣ ESTRUCTURA DE LA TABLA</h2>";
    
    $structure = $conn->query("DESCRIBE miembros");
    echo "<pre>";
    echo "Campo\t\t\tTipo\t\t\tNulo\tClave\n";
    echo "------------------------------------------------\n";
    while ($row = $structure->fetch_assoc()) {
        echo sprintf("%-20s %-20s %-8s %-8s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key']
        );
    }
    echo "</pre>";
    
    // 4. DATOS DE PRUEBA
    echo "<h2>4️⃣ INSERTANDO DATOS DE PRUEBA</h2>";
    
    // Generar CI único basado en timestamp
    $testCI = date('Ymd') . rand(100, 999);
    
    $testData = [
        'ci' => $testCI,
        'nombre' => 'Juan Carlos',
        'apellido' => 'Pérez González',
        'fecha_nacimiento' => '1985-03-15',
        'lugar' => 'Asunción',
        'profesion' => 'Ingeniero Civil',
        'direccion' => 'Av. España 1234',
        'telefono' => '+595 981 123456',
        'ciudad' => 'Asunción',
        'barrio' => 'Villa Morra',
        'esposa' => 'María Elena Rodríguez',
        'hijos' => 'Carlos Pérez (15 años), Ana Pérez (12 años)',
        'madre' => 'Elena González',
        'padre' => 'Carlos Pérez',
        'direccion_laboral' => 'Mcal. López 567, Asunción',
        'empresa' => 'Constructora Paraguaya S.A.',
        'logia_actual' => 'A∴R∴L∴ Nueva Alianza Nº 1',
        'grado_masonico' => 'maestro',
        'fecha_iniciacion' => '2010-06-21',
        'logia_iniciacion' => 'A∴R∴L∴ Nueva Alianza Nº 1',
        'certificados' => '',
        'grupo_sanguineo' => 'O+',
        'enfermedades_base' => 'Hipertensión controlada',
        'seguro_privado' => 'Si',
        'ips' => 'Si',
        'alergias' => 'Penicilina',
        'numero_emergencia' => '+595 981 654321',
        'contacto_emergencia' => 'María Elena Rodríguez (Esposa)',
        'foto_hermano' => '',
        'descripcion_otros_trabajos' => 'Consultor independiente en construcción',
        'grados_que_tienen' => '3°, 14°, 18°',
        'logia_perfeccion' => 'Logia de Perfección Asunción',
        'descripcion_grado_capitular' => 'Participante activo en ceremonias capitulares'
    ];
    
    echo "<div class='info'>📋 Datos de prueba preparados para CI: <strong>$testCI</strong></div>";
    
    // 5. VERIFICAR SI YA EXISTE
    $checkStmt = $conn->prepare("SELECT ci FROM miembros WHERE ci = ?");
    $checkStmt->bind_param("s", $testCI);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo "<div class='warning'>⚠️ CI $testCI ya existe. Generando nuevo CI...</div>";
        $testCI = date('Ymd') . rand(1000, 9999);
        $testData['ci'] = $testCI;
        echo "<div class='info'>🔄 Nuevo CI generado: <strong>$testCI</strong></div>";
    }
    $checkStmt->close();
    
    // 6. INSERTAR DATOS
    echo "<h3>Insertando registro...</h3>";
    
    $sql = "INSERT INTO miembros (
        ci, nombre, apellido, fecha_nacimiento, lugar, profesion,
        direccion, telefono, ciudad, barrio, esposa, hijos, madre, padre,
        direccion_laboral, empresa, logia_actual, grado_masonico,
        fecha_iniciacion, logia_iniciacion, certificados, grupo_sanguineo,
        enfermedades_base, seguro_privado, ips, alergias,
        numero_emergencia, contacto_emergencia, foto_hermano,
        descripcion_otros_trabajos, grados_que_tienen, logia_perfeccion,
        descripcion_grado_capitular, fecha_registro
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param(
        "sssssssssssssssssssssssssssssssss",
        $testData['ci'],
        $testData['nombre'],
        $testData['apellido'],
        $testData['fecha_nacimiento'],
        $testData['lugar'],
        $testData['profesion'],
        $testData['direccion'],
        $testData['telefono'],
        $testData['ciudad'],
        $testData['barrio'],
        $testData['esposa'],
        $testData['hijos'],
        $testData['madre'],
        $testData['padre'],
        $testData['direccion_laboral'],
        $testData['empresa'],
        $testData['logia_actual'],
        $testData['grado_masonico'],
        $testData['fecha_iniciacion'],
        $testData['logia_iniciacion'],
        $testData['certificados'],
        $testData['grupo_sanguineo'],
        $testData['enfermedades_base'],
        $testData['seguro_privado'],
        $testData['ips'],
        $testData['alergias'],
        $testData['numero_emergencia'],
        $testData['contacto_emergencia'],
        $testData['foto_hermano'],
        $testData['descripcion_otros_trabajos'],
        $testData['grados_que_tienen'],
        $testData['logia_perfeccion'],
        $testData['descripcion_grado_capitular']
    );
    
    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
        echo "<div class='success'>🎉 ¡INSERCIÓN EXITOSA!</div>";
        echo "<div class='info'>📝 ID del registro: <strong>$insertId</strong></div>";
        echo "<div class='info'>🆔 CI insertado: <strong>$testCI</strong></div>";
        
        // 7. VERIFICAR INSERCIÓN
        echo "<h2>5️⃣ VERIFICANDO INSERCIÓN</h2>";
        
        $verifyStmt = $conn->prepare("SELECT * FROM miembros WHERE ci = ?");
        $verifyStmt->bind_param("s", $testCI);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyResult->num_rows > 0) {
            $insertedData = $verifyResult->fetch_assoc();
            echo "<div class='success'>✅ Registro verificado correctamente</div>";
            
            echo "<h3>📊 Datos insertados:</h3>";
            echo "<pre>";
            foreach ($insertedData as $key => $value) {
                if (!in_array($key, ['id', 'fecha_registro', 'fecha_actualizacion', 'activo'])) {
                    echo sprintf("%-25s: %s\n", ucfirst(str_replace('_', ' ', $key)), $value ?: '(vacío)');
                }
            }
            echo "</pre>";
            
        } else {
            echo "<div class='error'>❌ Error: No se pudo verificar la inserción</div>";
        }
        $verifyStmt->close();
        
    } else {
        throw new Exception("Error en la inserción: " . $stmt->error);
    }
    
    $stmt->close();
    
    // 8. ESTADÍSTICAS DE LA TABLA
    echo "<h2>6️⃣ ESTADÍSTICAS DE LA TABLA</h2>";
    
    $countResult = $conn->query("SELECT COUNT(*) as total FROM miembros");
    $countData = $countResult->fetch_assoc();
    echo "<div class='info'>📈 Total de registros en la tabla: <strong>" . $countData['total'] . "</strong></div>";
    
    $lastRecords = $conn->query("SELECT ci, nombre, apellido, fecha_registro FROM miembros ORDER BY fecha_registro DESC LIMIT 5");
    echo "<h3>🕒 Últimos 5 registros:</h3>";
    echo "<pre>";
    echo "CI\t\tNombre\t\t\tFecha Registro\n";
    echo "--------------------------------------------------------\n";
    while ($record = $lastRecords->fetch_assoc()) {
        echo sprintf("%-15s %-25s %s\n", 
            $record['ci'], 
            $record['nombre'] . ' ' . $record['apellido'],
            $record['fecha_registro']
        );
    }
    echo "</pre>";
    
    // 9. ACCIONES
    echo "<h2>7️⃣ ACCIONES</h2>";
    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<a href='index.php' class='btn btn-success'>🏠 Ir al Formulario Principal</a>";
    echo "<a href='test_database.php' class='btn btn-info'>🔄 Ejecutar Prueba Nuevamente</a>";
    echo "<button onclick='deleteTestRecord()' class='btn btn-danger'>🗑️ Eliminar Registro de Prueba</button>";
    echo "</div>";
    
    // Script para eliminar registro de prueba
    echo "<script>
    function deleteTestRecord() {
        if (confirm('¿Está seguro de eliminar el registro de prueba CI: $testCI?')) {
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete&ci=$testCI'
            })
            .then(response => response.text())
            .then(data => {
                alert('Registro eliminado');
                location.reload();
            })
            .catch(error => {
                alert('Error eliminando registro: ' + error);
            });
        }
    }
    </script>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>❌ <strong>ERROR:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='info'>🔧 <strong>Posibles soluciones:</strong>
    <ul>
        <li>Verificar credenciales en config/database.php</li>
        <li>Asegurar que la base de datos existe</li>
        <li>Verificar permisos del usuario de BD</li>
        <li>Comprobar que MySQL esté ejecutándose</li>
    </ul>
    </div>";
}

// Manejar eliminación de registro de prueba
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        require_once 'config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        $deleteStmt = $conn->prepare("DELETE FROM miembros WHERE ci = ?");
        $deleteStmt->bind_param("s", $_POST['ci']);
        
        if ($deleteStmt->execute()) {
            echo "success";
        } else {
            echo "error: " . $deleteStmt->error;
        }
        
        $deleteStmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
    exit;
}

echo "</div>";
?>

<div style="margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 10px; border-left: 5px solid #f59e0b;">
    <h3 style="color: #92400e; margin-top: 0;">⚠️ IMPORTANTE - SEGURIDAD</h3>
    <p style="color: #92400e; margin-bottom: 0;">
        <strong>Este script es solo para pruebas de desarrollo.</strong><br>
        Elimine este archivo (<code>test_database.php</code>) antes de subir a producción.
    </p>
</div>

<div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-radius: 10px; border-left: 5px solid #0ea5e9;">
    <h4 style="color: #0c4a6e; margin-top: 0;">📋 CHECKLIST DE VERIFICACIÓN:</h4>
    <ul style="color: #0c4a6e;">
        <li>✅ Conexión a base de datos funcionando</li>
        <li>✅ Tabla 'miembros' creada o verificada</li>
        <li>✅ Inserción de datos exitosa</li>
        <li>✅ Verificación de datos insertados</li>
        <li>✅ Sistema listo para uso en formulario</li>
    </ul>
</div>