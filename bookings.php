-- bookings.php --
<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener reservas
$query = "SELECT b.*, a.name, a.price, a.image_url 
          FROM bookings b 
          JOIN accommodations a ON b.accommodation_id = a.id";

// Si es administrador, ver todas las reservas, si no, solo las del usuario
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $query .= " WHERE b.user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistema de Alojamientos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/index.php">Panel Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/view_bookings.php">Ver Reservas</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_profile.php">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="bookings.php">Mis Reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4"><?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Todas las Reservas' : 'Mis Reservas'; ?></h2>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">No hay reservas disponibles</div>
        <?php else: ?>
            <div class="row">
                <?php foreach($bookings as $booking): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?php echo $booking['image_url'] ?? 'assets/uploads/default-accommodation.jpg'; ?>" 
                                         class="img-fluid rounded-start h-100 object-fit-cover" 
                                         alt="<?php echo htmlspecialchars($booking['name']); ?>"
                                         style="min-height: 200px;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($booking['name']); ?></h5>
                                        <div class="booking-details">
                                            <p class="card-text">
                                                <strong>Fecha de entrada:</strong><br>
                                                <?php echo date('d/m/Y', strtotime($booking['check_in'])); ?>
                                            </p>
                                            <p class="card-text">
                                                <strong>Fecha de salida:</strong><br>
                                                <?php echo date('d/m/Y', strtotime($booking['check_out'])); ?>
                                            </p>
                                            <p class="card-text">
                                                <strong>Precio total:</strong><br>
                                                $<?php echo number_format($booking['total_price'], 2); ?>
                                            </p>
                                            <p class="card-text">
                                                <strong>Estado:</strong><br>
                                                <span class="badge <?php echo $booking['status'] == 'confirmed' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </p>
                                            
                                            <!-- Botones de acción -->
                                            <div class="mt-3">
                                                <?php if($booking['status'] !== 'cancelled'): ?>
                                                    <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" 
                                                       class="btn btn-primary btn-sm">Editar</a>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete(<?php echo $booking['id']; ?>)">
                                                        Eliminar
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                                    <div class="mt-2">
                                                        <a href="admin/update_booking_status.php?id=<?php echo $booking['id']; ?>&status=confirmed" 
                                                           class="btn btn-success btn-sm">Confirmar</a>
                                                        <a href="admin/update_booking_status.php?id=<?php echo $booking['id']; ?>&status=cancelled" 
                                                           class="btn btn-warning btn-sm">Cancelar</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function confirmDelete(bookingId) {
        if (confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
            window.location.href = 'delete_booking.php?id=' + bookingId;
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>