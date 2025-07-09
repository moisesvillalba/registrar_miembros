<?php
// process.php - Versión simplificada y funcional para tu estructura
session_start();

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'includes/flash.php';

// Función para limpiar datos (tu función actual mejorada)
function limpiarDato($dato)
{
    return Validator::sanitizeInput($dato);
}

// Procesar los datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        // VERIFICAR TOKEN CSRF BÁSICO
        if (!CSRFProtection::validateToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token de seguridad inválido. Recargue la página e intente nuevamente.');
        }

        // Crear instancia de la base de datos y obtener conexión
        $database = new Database();
        $db = $database->getConnection();

        // DATOS PERSONALES
        $ci = limpiarDato($_POST["ci"]);
        $nombre = limpiarDato($_POST["nombre"]);
        $apellido = limpiarDato($_POST["apellido"]);
        $fecha_nacimiento = limpiarDato($_POST["fecha_nacimiento"]);
        $lugar = limpiarDato($_POST["lugar"]);
        $profesion = limpiarDato($_POST["profesion"]);
        $direccion = limpiarDato($_POST["direccion"]);
        $telefono = limpiarDato($_POST["telefono"]);
        $ciudad = limpiarDato($_POST["ciudad"]);
        $barrio = limpiarDato($_POST["barrio"]);
        $esposa = isset($_POST["esposa"]) ? limpiarDato($_POST["esposa"]) : "";
        $hijos = isset($_POST["hijos"]) ? limpiarDato($_POST["hijos"]) : "";
        $madre = isset($_POST["madre"]) ? limpiarDato($_POST["madre"]) : "";
        $padre = isset($_POST["padre"]) ? limpiarDato($_POST["padre"]) : "";

        // DATOS LABORALES
        $direccion_laboral = limpiarDato($_POST["direccion_laboral"]);
        $empresa = isset($_POST["empresa"]) ? limpiarDato($_POST["empresa"]) : "";

        // DATOS LOGIALES - Mapear a tu estructura de BD
        $institucion_actual = limpiarDato($_POST["logia_actual"]);
        $nivel_actual = limpiarDato($_POST["grado_masonico"]);
        $nivel_superior = isset($_POST["grado_capitular"]) ? limpiarDato($_POST["grado_capitular"]) : "";
        $fecha_ingreso = limpiarDato($_POST["fecha_iniciacion"]);
        $institucion_ingreso = isset($_POST["logia_iniciacion"]) ? limpiarDato($_POST["logia_iniciacion"]) : "";

        // DATOS MÉDICOS
        $grupo_sanguineo = limpiarDato($_POST["grupo_sanguineo"]);
        $enfermedades_base = isset($_POST["enfermedades_base"]) ? limpiarDato($_POST["enfermedades_base"]) : "";
        $seguro_privado = isset($_POST["seguro_privado"]) ? limpiarDato($_POST["seguro_privado"]) : "No";
        $ips = isset($_POST["ips"]) ? limpiarDato($_POST["ips"]) : "No";
        $alergias = isset($_POST["alergias"]) ? limpiarDato($_POST["alergias"]) : "";
        $numero_emergencia = limpiarDato($_POST["numero_emergencia"]);
        $contacto_emergencia = limpiarDato($_POST["contacto_emergencia"]);

        // CAMPOS ADICIONALES DE TU BD (valores por defecto para campos que tienes pero el formulario no usa)
        $foto_hermano = "";
        $descripcion_otros_trabajos = "";
        $grados_que_tienen = "";
        $logia_perfeccion = "";
        $descripcion_grado_capitular = "";

        // VALIDACIONES BÁSICAS
        $errors = [];

        // Validar campos requeridos
        if (empty($ci)) $errors[] = "CI es requerido";
        if (empty($nombre)) $errors[] = "Nombre es requerido";
        if (empty($apellido)) $errors[] = "Apellido es requerido";
        if (empty($fecha_nacimiento)) $errors[] = "Fecha de nacimiento es requerida";
        if (empty($lugar)) $errors[] = "Lugar de nacimiento es requerido";
        if (empty($profesion)) $errors[] = "Profesión es requerida";
        if (empty($direccion)) $errors[] = "Dirección es requerida";
        if (empty($telefono)) $errors[] = "Teléfono es requerido";
        if (empty($ciudad)) $errors[] = "Ciudad es requerida";
        if (empty($barrio)) $errors[] = "Barrio es requerido";
        if (empty($direccion_laboral)) $errors[] = "Dirección laboral es requerida";
        if (empty($institucion_actual)) $errors[] = "Logia actual es requerida";
        if (empty($nivel_actual)) $errors[] = "Grado masónico es requerido";
        if (empty($fecha_ingreso)) $errors[] = "Fecha de iniciación es requerida";
        if (empty($grupo_sanguineo)) $errors[] = "Grupo sanguíneo es requerido";
        if (empty($numero_emergencia)) $errors[] = "Número de emergencia es requerido";
        if (empty($contacto_emergencia)) $errors[] = "Contacto de emergencia es requerido";

        // Validar formato de CI
        if (!empty($ci) && !Validator::validateCI($ci)) {
            $errors[] = "Formato de CI inválido (debe tener entre 6 y 8 dígitos)";
        }

        // Validar formato de teléfonos
        if (!empty($telefono) && !Validator::validatePhone($telefono)) {
            $errors[] = "Formato de teléfono inválido";
        }

        if (!empty($numero_emergencia) && !Validator::validatePhone($numero_emergencia)) {
            $errors[] = "Formato de número de emergencia inválido";
        }

        // Validar edad
        if (!empty($fecha_nacimiento)) {
            $fechaNac = new DateTime($fecha_nacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
            if ($edad < 18) {
                $errors[] = "Debe ser mayor de 18 años";
            }
            if ($edad > 100) {
                $errors[] = "Fecha de nacimiento no válida";
            }
        }

        // Validar fecha de iniciación
        if (!empty($fecha_ingreso)) {
            $fechaInic = new DateTime($fecha_ingreso);
            $hoy = new DateTime();
            if ($fechaInic > $hoy) {
                $errors[] = "La fecha de iniciación no puede ser futura";
            }

            // Validar edad en iniciación
            if (!empty($fecha_nacimiento)) {
                $fechaNac = new DateTime($fecha_nacimiento);
                $edadInic = $fechaInic->diff($fechaNac)->y;
                if ($edadInic < 18) {
                    $errors[] = "La edad en la fecha de iniciación debe ser mayor a 18 años";
                }
            }
        }

        // Si hay errores, mostrarlos
        if (!empty($errors)) {
            $mensaje_error = implode(", ", $errors);
            throw new Exception($mensaje_error);
        }

        // Manejo de la subida de documentos (tu código actual mejorado)
        $ruta_documentos = "";

        if (isset($_FILES["certificados"]) && is_array($_FILES["certificados"]["name"])) {
            $directorio_destino = "uploads/documentos/";

            // Crear el directorio si no existe
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $rutas_archivos = [];

            // Procesar cada archivo
            for ($i = 0; $i < count($_FILES["certificados"]["name"]); $i++) {
                if ($_FILES["certificados"]["error"][$i] == 0) {
                    $extension = strtolower(pathinfo($_FILES["certificados"]["name"][$i], PATHINFO_EXTENSION));
                    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'pdf'];

                    if (in_array($extension, $extensiones_permitidas)) {
                        if ($_FILES["certificados"]["size"][$i] <= 5 * 1024 * 1024) {
                            $nombre_archivo = uniqid('doc_') . '_' . time() . '.' . $extension;
                            $ruta_archivo = $directorio_destino . $nombre_archivo;

                            if (move_uploaded_file($_FILES["certificados"]["tmp_name"][$i], $ruta_archivo)) {
                                $rutas_archivos[] = $ruta_archivo;
                            }
                        } else {
                            throw new Exception("El archivo " . $_FILES["certificados"]["name"][$i] . " excede el tamaño máximo permitido (5MB).");
                        }
                    } else {
                        throw new Exception("El tipo de archivo " . $_FILES["certificados"]["name"][$i] . " no está permitido. Use JPG, PNG o PDF.");
                    }
                }
            }

            if (!empty($rutas_archivos)) {
                $ruta_documentos = implode(",", $rutas_archivos);
            }
        }

        // Verificar si el CI ya existe
        $check_sql = "SELECT id FROM miembros WHERE ci = :ci";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':ci', $ci);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            throw new Exception("Ya existe un registro con este número de CI. Por favor, verifique los datos.");
        }

        // Comenzar transacción
        $db->beginTransaction();

        // Preparar la consulta SQL EXACTA para tu estructura de BD
        $sql = "INSERT INTO miembros (
                    ci, nombre, apellido, fecha_nacimiento, lugar, profesion, direccion, 
                    telefono, ciudad, barrio, esposa, hijos, madre, padre, foto_hermano,
                    direccion_laboral, empresa, descripcion_otros_trabajos,
                    institucion_actual, nivel_actual, nivel_superior, grados_que_tienen, 
                    logia_perfeccion, descripcion_grado_capitular, fecha_ingreso, institucion_ingreso, documentos,
                    grupo_sanguineo, enfermedades_base, seguro_privado, ips, alergias, numero_emergencia, contacto_emergencia,
                    estado
                ) VALUES (
                    :ci, :nombre, :apellido, :fecha_nacimiento, :lugar, :profesion, :direccion, 
                    :telefono, :ciudad, :barrio, :esposa, :hijos, :madre, :padre, :foto_hermano,
                    :direccion_laboral, :empresa, :descripcion_otros_trabajos,
                    :institucion_actual, :nivel_actual, :nivel_superior, :grados_que_tienen,
                    :logia_perfeccion, :descripcion_grado_capitular, :fecha_ingreso, :institucion_ingreso, :documentos,
                    :grupo_sanguineo, :enfermedades_base, :seguro_privado, :ips, :alergias, :numero_emergencia, :contacto_emergencia,
                    'pendiente'
                )";

        $stmt = $db->prepare($sql);

        // Vincular parámetros EXACTOS para tu estructura
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':profesion', $profesion);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':barrio', $barrio);
        $stmt->bindParam(':esposa', $esposa);
        $stmt->bindParam(':hijos', $hijos);
        $stmt->bindParam(':madre', $madre);
        $stmt->bindParam(':padre', $padre);
        $stmt->bindParam(':foto_hermano', $foto_hermano);
        $stmt->bindParam(':direccion_laboral', $direccion_laboral);
        $stmt->bindParam(':empresa', $empresa);
        $stmt->bindParam(':descripcion_otros_trabajos', $descripcion_otros_trabajos);
        $stmt->bindParam(':institucion_actual', $institucion_actual);
        $stmt->bindParam(':nivel_actual', $nivel_actual);
        $stmt->bindParam(':nivel_superior', $nivel_superior);
        $stmt->bindParam(':grados_que_tienen', $grados_que_tienen);
        $stmt->bindParam(':logia_perfeccion', $logia_perfeccion);
        $stmt->bindParam(':descripcion_grado_capitular', $descripcion_grado_capitular);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        $stmt->bindParam(':institucion_ingreso', $institucion_ingreso);
        $stmt->bindParam(':documentos', $ruta_documentos);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':enfermedades_base', $enfermedades_base);
        $stmt->bindParam(':seguro_privado', $seguro_privado);
        $stmt->bindParam(':ips', $ips);
        $stmt->bindParam(':alergias', $alergias);
        $stmt->bindParam(':numero_emergencia', $numero_emergencia);
        $stmt->bindParam(':contacto_emergencia', $contacto_emergencia);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $db->commit();
            setFlashMessage('success', 'Registro creado exitosamente');

            // ENVIAR RESPUESTA JSON PARA AJAX
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Registro completado exitosamente',
                'redirect' => 'success.php'
            ]);
            exit();
        } else {
            $db->rollBack();
            throw new Exception("Error al registrar los datos.");
        }
    } catch (Exception $e) {
        // Si hay transacción activa, revertir
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }

        // Eliminar archivos subidos en caso de error
        if (isset($rutas_archivos) && !empty($rutas_archivos)) {
            foreach ($rutas_archivos as $ruta) {
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }
        }

        $mensaje_error = $e->getMessage();
        setFlashMessage('error', $mensaje_error);

        // ENVIAR RESPUESTA JSON PARA AJAX
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $mensaje_error
        ]);
        exit();
    }
}

// Si llegamos aquí sin POST, redirigir
header("Location: index.php");
exit();
