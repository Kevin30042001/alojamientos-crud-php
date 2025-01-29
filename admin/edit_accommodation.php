<?php
session_start();
require_once '../config/database.php';
require_once '../models/Accommodation.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

$accommodation = new Accommodation($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'] ?? null;
    
    if ($accommodation->updateAccommodation($id, $name, $description, $price, $image_url)) {
        $success = "Alojamiento actualizado exitosamente";
    } else {
        $error = "Error al actualizar el alojamiento";
    }
}

// Obtener datos del alojamiento
$acc = null;
if (isset($_GET['id'])) {
    $acc = $accommodation->getAccommodationById($_GET['id']);
}

if (!$acc) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alojamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Editar Alojamiento</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="mb-3">
            <input type="hidden" name="id" value="<?php echo $acc['id']; ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($acc['name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" 
                          rows="3" required><?php echo htmlspecialchars($acc['description']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Precio por noche</label>
                <input type="number" class="form-control" id="price" name="price" 
                       value="<?php echo htmlspecialchars($acc['price']); ?>" step="0.01" required>
            </div>
            
            <div class="mb-3">
                <label for="image_url" class="form-label">URL de la imagen</label>
                <input type="text" class="form-control" id="image_url" name="image_url" 
                       value="<?php echo htmlspecialchars($acc['image_url'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>