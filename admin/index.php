<?php
session_start();
require_once '../config/database.php';
require_once '../models/Accommodation.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

$accommodation = new Accommodation($pdo);
$accommodations = $accommodation->getAllAccommodations();

// Mensajes de éxito o error
$success = isset($_GET['success']) ? "Operación realizada con éxito" : "";
$error = isset($_GET['error']) ? "Ocurrió un error en la operación" : "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Sistema de Alojamientos</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Volver al Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Cerrar Sesión</a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link" href="view_bookings.php">Ver Reservas</a>
</li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Panel de Administración</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Agregar Nuevo Alojamiento</h3>
            </div>
            <div class="card-body">
                <form action="add_accommodation.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Alojamiento</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Precio por noche</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Imagen del Alojamiento</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png" onchange="previewImage(this)">
                            </div>
                            
                            <div id="imagePreview" class="mt-3 text-center">
                                <!-- La vista previa de la imagen se mostrará aquí -->
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Agregar Alojamiento</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Alojamientos Existentes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($accommodations as $acc): ?>
                            <tr>
                                <td><?php echo $acc['id']; ?></td>
                                <td>
                                    <img src="../<?php echo $acc['image_url'] ?? 'assets/uploads/default-accommodation.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($acc['name']); ?>"
                                         class="img-thumbnail" style="width: 100px;">
                                </td>
                                <td><?php echo htmlspecialchars($acc['name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($acc['description'], 0, 100)) . '...'; ?></td>
                                <td>$<?php echo number_format($acc['price'], 2); ?></td>
                                <td>
                                    <a href="edit_accommodation.php?id=<?php echo $acc['id']; ?>" 
                                       class="btn btn-sm btn-primary mb-1">Editar</a>
                                    <button onclick="confirmDelete(<?php echo $acc['id']; ?>)" 
                                            class="btn btn-sm btn-danger mb-1">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImage()">
                        Eliminar imagen
                    </button>
                `;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImage() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').innerHTML = '';
    }

    function confirmDelete(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este alojamiento?')) {
            window.location.href = `delete_accommodation.php?id=${id}`;
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
