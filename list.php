<?php
// Iniciar sesión
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    // No está autenticado, redirigir al login
    setFlashMessage('error', 'Debe iniciar sesión para acceder');
    header("Location: login.php");
    exit();
}

// Incluir configuración de base de datos
require_once 'config/database.php';
require_once 'includes/flash.php';

// Crear instancia de base de datos
$database = new Database();
$db = $database->getConnection();

// Configuración de paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para obtener total de registros
$sql_total = "SELECT COUNT(*) as total FROM miembros";
$stmt_total = $db->prepare($sql_total);
$stmt_total->execute();
$total_registros = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para obtener registros paginados
$sql = "SELECT id, ci, CONCAT(nombre, ' ', apellido) as nombre_completo, 
               fecha_nacimiento, telefono, institucion_actual, nivel_actual, 
               fecha_registro 
        FROM miembros 
        ORDER BY fecha_registro DESC 
        LIMIT :inicio, :registros_por_pagina";

$stmt = $db->prepare($sql);
$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindParam(':registros_por_pagina', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();

// Procesar búsqueda
$busqueda = '';
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $busqueda = $_GET['buscar'];
    
    $sql_busqueda = "SELECT id, ci, CONCAT(nombre, ' ', apellido) as nombre_completo, 
                           fecha_nacimiento, telefono, institucion_actual, nivel_actual, 
                           fecha_registro 
                    FROM miembros 
                    WHERE ci LIKE :busqueda OR nombre LIKE :busqueda OR 
                          apellido LIKE :busqueda OR institucion_actual LIKE :busqueda 
                    ORDER BY fecha_registro DESC";
    
    $stmt = $db->prepare($sql_busqueda);
    $param_busqueda = "%" . $busqueda . "%";
    $stmt->bindParam(':busqueda', $param_busqueda);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Miembros | Sistema de Registro</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Lista de Miembros</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </header>

        <?php 
        // Mostrar mensaje flash si existe
        $flashMessage = getFlashMessage();
        if ($flashMessage): 
        ?>
            <div class="mensaje-container <?php echo $flashMessage['tipo']; ?>">
                <?php echo $flashMessage['mensaje']; ?>
            </div>
        <?php endif; ?>

        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <input type="text" name="buscar" placeholder="Buscar miembros..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>

        <?php if ($stmt->rowCount() > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>CI</th>
                            <th>Nombre Completo</th>
                            <th>Fecha Nacimiento</th>
                            <th>Teléfono</th>
                            <th>Institución</th>
                            <th>Nivel</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['ci']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre_completo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($fila['fecha_nacimiento'])); ?></td>
                                <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($fila['institucion_actual']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nivel_actual']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($fila['fecha_registro'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="ver_registro.php?id=<?php echo $fila['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar_registro.php?id=<?php echo $fila['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="eliminar_registro.php?id=<?php echo $fila['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este registro?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!isset($_GET['buscar']) || empty($_GET['buscar'])): ?>
                <div class="pagination">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=1" class="btn btn-secondary">&laquo; Primera</a>
                        <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="btn btn-secondary">&lsaquo; Anterior</a>
                    <?php endif; ?>

                    <?php
                    $inicio_paginacion = max(1, $pagina_actual - 2);
                    $fin_paginacion = min($inicio_paginacion + 4, $total_paginas);
                    
                    for ($i = $inicio_paginacion; $i <= $fin_paginacion; $i++):
                    ?>
                        <?php if ($i == $pagina_actual): ?>
                            <span class="btn btn-primary active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?php echo $i; ?>" class="btn btn-secondary"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="btn btn-secondary">Siguiente &rsaquo;</a>
                        <a href="?pagina=<?php echo $total_paginas; ?>" class="btn btn-secondary">Última &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info">
                No se encontraron registros.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Opcional: Confirmación de eliminación
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('.btn-danger');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('¿Está seguro de eliminar este registro?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>