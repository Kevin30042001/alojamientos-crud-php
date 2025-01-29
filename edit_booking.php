
<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$booking = null;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("
        SELECT b.*, a.name as accommodation_name, a.price as price_per_night
        FROM bookings b
        JOIN accommodations a ON b.accommodation_id = a.id
        WHERE b.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    try {
        // Calcular nuevo precio total
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $days = $check_in_date->diff($check_out_date)->days;
        $total_price = $booking['price_per_night'] * $days;
        
        $stmt = $pdo->prepare("
            UPDATE bookings 
            SET check_in = ?, check_out = ?, total_price = ?
            WHERE id = ? AND user_id = ?
        ");
        
        if ($stmt->execute([$check_in, $check_out, $total_price, $booking_id, $_SESSION['user_id']])) {
            header('Location: bookings.php?success=updated');
            exit;
        } else {
            $error = "Error al actualizar la reserva";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Editar Reserva</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($booking): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($booking['accommodation_name']); ?></h5>
                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="check_in" class="form-label">Fecha de entrada</label>
                            <input type="date" class="form-control" id="check_in" name="check_in"
                                   value="<?php echo $booking['check_in']; ?>"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="check_out" class="form-label">Fecha de salida</label>
                            <input type="date" class="form-control" id="check_out" name="check_out"
                                   value="<?php echo $booking['check_out']; ?>"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Actualizar Reserva</button>
                        <a href="bookings.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Reserva no encontrada</div>
            <a href="bookings.php" class="btn btn-primary">Volver a Mis Reservas</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

