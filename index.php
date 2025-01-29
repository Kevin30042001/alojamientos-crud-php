<?php
session_start();
require_once 'config/database.php';

// Obtener alojamientos de la base de datos
$stmt = $pdo->query("SELECT * FROM accommodations WHERE status = 'available' ORDER BY created_at DESC");
$accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alojamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Alojamientos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/">Panel Admin</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="user_profile.php">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bookings.php">Mis Reservas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Alojamientos Disponibles</h1>
        
        <div class="row">
            <?php foreach($accommodations as $accommodation): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($accommodation['image_url'] ?? 'assets/default-accommodation.jpg'); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($accommodation['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($accommodation['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($accommodation['description']); ?></p>
                            <p class="card-text"><strong>Precio:</strong> $<?php echo htmlspecialchars($accommodation['price']); ?>/noche</p>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <div class="d-flex gap-2">
                                    <a href="book.php?id=<?php echo $accommodation['id']; ?>" class="btn btn-primary">Reservar</a>
                                    <form action="select_accommodation.php" method="POST" class="d-inline">
                                        <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id']; ?>">
                                        <button type="submit" class="btn btn-success">Seleccionar</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="d-grid gap-2">
                                    <a href="login.php" class="btn btn-secondary">Inicia sesión para reservar</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>