<?php
session_start();
require_once 'config/database.php';
require_once 'models/Accommodation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener alojamientos seleccionados por el usuario
$stmt = $pdo->prepare("
    SELECT a.* 
    FROM accommodations a 
    JOIN user_selections us ON a.id = us.accommodation_id 
    WHERE us.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$selectedAccommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Alojamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Alojamientos</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Mi Perfil</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

        <h3>Mis Alojamientos Seleccionados</h3>
        <div class="row">
            <?php if (empty($selectedAccommodations)): ?>
                <div class="col-12">
                    <p>No has seleccionado ningún alojamiento aún.</p>
                </div>
            <?php else: ?>
                <?php foreach($selectedAccommodations as $accommodation): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($accommodation['image_url'] ?? 'assets/default-accommodation.jpg'); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($accommodation['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($accommodation['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($accommodation['description']); ?></p>
                                <p class="card-text"><strong>Precio:</strong> $<?php echo htmlspecialchars($accommodation['price']); ?>/noche</p>
                                <form action="remove_selection.php" method="POST">
                                    <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Eliminar de mi selección</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>