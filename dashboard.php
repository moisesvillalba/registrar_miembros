<?php
session_start();

// Configuración para producción
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener información del usuario
$user_rol = $_SESSION['rol'];
$user_name = $_SESSION['nombre_completo'];

// Paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filtros
$search = trim($_GET['search'] ?? '');
$logia_filter = trim($_GET['logia'] ?? '');

try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn === null) {
        throw new Exception('No se pudo conectar a la base de datos');
    }

    // Construir consulta con filtros
    $where_conditions = [];
    $params = [];

    if (!empty($search)) {
        $where_conditions[] = "(nombre LIKE ? OR apellido LIKE ? OR ci LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($logia_filter)) {
        $where_conditions[] = "institucion_actual = ?";
        $params[] = $logia_filter;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Contar total de registros
    $count_sql = "SELECT COUNT(*) as total FROM miembros $where_clause";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_records / $per_page);

    // Obtener registros con paginación
    $sql = "SELECT id, ci, nombre, apellido, telefono, ciudad, institucion_actual, nivel_actual, 
                   grupo_sanguineo, fecha_registro 
            FROM miembros 
            $where_clause 
            ORDER BY fecha_registro DESC 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $miembros = $stmt->fetchAll();

    // Obtener logias para filtro
    $logias_stmt = $conn->prepare("SELECT DISTINCT institucion_actual FROM miembros WHERE institucion_actual IS NOT NULL ORDER BY institucion_actual");
    $logias_stmt->execute();
    $logias = $logias_stmt->fetchAll();

} catch (Exception $e) {
    error_log("Error en dashboard: " . $e->getMessage());
    $error = 'Error al cargar los datos';
    $miembros = [];
    $total_records = 0;
    $total_pages = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Registro Masónico</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dashboard-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .filters-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filters-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .filter-group input, .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
        }

        .btn-filter {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            height: 42px;
            white-space: nowrap;
        }

        .btn-clear {
            background: var(--medium-color);
            color: var(--text-color);
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            height: 42px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 14px;
        }

        .table-responsive {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table-header {
            background: var(--light-color);
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }

        th {
            background: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .badge-maestro {
            background: #10b981;
            color: white;
        }

        .badge-companero {
            background: #f59e0b;
            color: white;
        }

        .badge-aprendiz {
            background: #6b7280;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            text-decoration: none;
            color: var(--text-color);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--light-color);
        }

        .pagination .current {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }

            .filters-row {
                flex-direction: column;
            }

            .filter-group {
                min-width: auto;
            }

            .table-responsive {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1400px;">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                <div>
                    <h1 style="margin: 0; font-size: 24px;">Panel de Control</h1>
                    <p style="margin: 5px 0 0 0; opacity: 0.8; font-size: 14px;">
                        Bienvenido, <?php echo htmlspecialchars($user_name); ?> 
                        (<?php echo ucfirst($user_rol); ?>)
                    </p>
                </div>
            </div>
            <div class="dashboard-actions">
                <a href="index.php" class="btn-logout">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </a>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_records; ?></div>
                <div class="stat-label">Total de Miembros</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($logias); ?></div>
                <div class="stat-label">Logias Activas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $page; ?>/<?php echo max(1, $total_pages); ?></div>
                <div class="stat-label">Página Actual</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($miembros); ?></div>
                <div class="stat-label">En Esta Página</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <form method="GET" action="">
                <input type="hidden" name="page" value="1">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="search">Buscar por Nombre, CI o Apellido:</label>
                        <input type="text" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Ingrese término de búsqueda...">
                    </div>
                    <div class="filter-group">
                        <label for="logia">Filtrar por Logia:</label>
                        <select id="logia" name="logia">
                            <option value="">Todas las logias</option>
                            <?php foreach ($logias as $logia): ?>
                                <option value="<?php echo htmlspecialchars($logia['institucion_actual']); ?>"
                                        <?php echo $logia_filter === $logia['institucion_actual'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($logia['institucion_actual']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="dashboard.php" class="btn-clear">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de miembros -->
        <div class="table-responsive">
            <div class="table-header">
                <h3 style="margin: 0;">Miembros Registrados</h3>
                <span style="color: var(--text-muted); font-size: 14px;">
                    <?php echo $total_records; ?> registros encontrados
                </span>
            </div>

            <?php if (!empty($miembros)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>CI</th>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Ciudad</th>
                            <th>Logia</th>
                            <th>Grado</th>
                            <th>Grupo Sanguíneo</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($miembros as $miembro): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($miembro['ci']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($miembro['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($miembro['ciudad']); ?></td>
                                <td style="font-size: 12px;">
                                    <?php echo htmlspecialchars($miembro['institucion_actual']); ?>
                                </td>
                                <td>
                                    <?php
                                    $grado = $miembro['nivel_actual'];
                                    $badge_class = 'badge-aprendiz';
                                    if ($grado === 'maestro') $badge_class = 'badge-maestro';
                                    elseif ($grado === 'companero') $badge_class = 'badge-companero';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars(ucfirst($grado)); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($miembro['grupo_sanguineo']); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($miembro['fecha_registro'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-search" style="font-size: 48px; color: var(--text-muted); margin-bottom: 20px;"></i>
                    <h3>No se encontraron miembros</h3>
                    <p>Intenta modificar los filtros de búsqueda</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&logia=<?php echo urlencode($logia_filter); ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&logia=<?php echo urlencode($logia_filter); ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&logia=<?php echo urlencode($logia_filter); ?>">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>