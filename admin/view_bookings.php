<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

// Obtener todas las reservas con información de usuarios y alojamientos
$stmt = $pdo->prepare("
    SELECT 
        b.*,
        a.name as accommodation_name,
        a.price as price_per_night,
        u.username,
        u.email
    FROM bookings b
    JOIN accommodations a ON b.accommodation_id = a.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Panel de Administración</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Gestionar Alojamientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="view_bookings.php">Ver Reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Gestión de Reservas</h2>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">No hay reservas en el sistema</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Alojamiento</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Precio Total</th>
                                    <th>Estado</th>
                                    <th>Fecha de Reserva</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($booking['username']); ?>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['accommodation_name']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking['check_in'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking['check_out'])); ?></td>
                                        <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if($booking['status'] !== 'confirmed'): ?>
                                                    <a href="update_booking_status.php?id=<?php echo $booking['id']; ?>&status=confirmed" 
                                                       class="btn btn-sm btn-success">Confirmar</a>
                                                <?php endif; ?>
                                                <?php if($booking['status'] !== 'cancelled'): ?>
                                                    <a href="update_booking_status.php?id=<?php echo $booking['id']; ?>&status=cancelled" 
                                                       class="btn btn-sm btn-danger">Cancelar</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>