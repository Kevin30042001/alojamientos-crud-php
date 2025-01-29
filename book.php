<?php
session_start();
require_once 'config/database.php';
require_once 'models/Accommodation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$accommodation = null;

if (isset($_GET['id'])) {
    $acc = new Accommodation($pdo);
    $accommodation = $acc->getAccommodationById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $accommodation_id = $_POST['accommodation_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Calcular precio total
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $days = $check_in_date->diff($check_out_date)->days;
        $total_price = $accommodation['price'] * $days;
        
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, accommodation_id, check_in, check_out, total_price) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$user_id, $accommodation_id, $check_in, $check_out, $total_price])) {
            $success = "Reserva realizada con éxito";
            header("refresh:2;url=bookings.php");
        } else {
            $error = "Error al realizar la reserva";
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
    <title>Realizar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Realizar Reserva</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($accommodation): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($accommodation['name']); ?></h3>
                    <p class="card-text"><?php echo htmlspecialchars($accommodation['description']); ?></p>
                    <p class="card-text"><strong>Precio por noche:</strong> $<?php echo htmlspecialchars($accommodation['price']); ?></p>
                </div>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id']; ?>">
                
                <div class="mb-3">
                    <label for="check_in" class="form-label">Fecha de entrada</label>
                    <input type="date" class="form-control" id="check_in" name="check_in" 
                           min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="check_out" class="form-label">Fecha de salida</label>
                    <input type="date" class="form-control" id="check_out" name="check_out" 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
                    <a href="index.php" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Alojamiento no encontrado</div>
            <a href="index.php" class="btn btn-primary">Volver al inicio</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validación de fechas
    document.getElementById('check_in').addEventListener('change', function() {
        let checkIn = new Date(this.value);
        let checkOut = document.getElementById('check_out');
        checkOut.min = new Date(checkIn.getTime() + 86400000).toISOString().split('T')[0];
        if (checkOut.value && new Date(checkOut.value) <= checkIn) {
            checkOut.value = checkOut.min;
        }
    });
    </script>
</body>
</html>