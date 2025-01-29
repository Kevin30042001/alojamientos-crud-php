<?php
session_start();
require_once '../config/database.php';

// Verificar si es administrador
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die(json_encode(['error' => 'No autorizado']));
}

if (isset($_FILES['image'])) {
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
    if (!in_array($file['type'], $allowedTypes)) {
        die(json_encode(['error' => 'Solo se permiten archivos JPG, JPEG & PNG']));
    }
    
    // Verificar tamaño (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        die(json_encode(['error' => 'El archivo es demasiado grande. Máximo 5MB']));
    }
    
    // Subir archivo
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        echo json_encode(['url' => 'assets/uploads/' . $fileName]);
    } else {
        echo json_encode(['error' => 'Error al subir el archivo']);
    }
} else {
    echo json_encode(['error' => 'No se recibió ningún archivo']);
}