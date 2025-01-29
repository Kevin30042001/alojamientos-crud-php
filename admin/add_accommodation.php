-- admin/add_accommodation.php --
<?php
session_start();
require_once '../config/database.php';
require_once '../models/Accommodation.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accommodation = new Accommodation($pdo);
    
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = null;
    
    // Manejar la subida de imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $fileName = time() . '_' . basename($file['name']);
        $uploadDir = '../assets/uploads/';
        $uploadFile = $uploadDir . $fileName;
        
        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Verificar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (in_array($file['type'], $allowedTypes) && move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $image_url = 'assets/uploads/' . $fileName;
        }
    }
    
    if ($accommodation->addAccommodation($name, $description, $price, $image_url)) {
        header('Location: index.php?success=1');
    } else {
        header('Location: index.php?error=1');
    }
    exit;
}