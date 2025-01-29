-- admin/delete_accommodation.php --
<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Primero eliminar las reservas asociadas
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE accommodation_id = ?");
        $stmt->execute([$id]);
        
        // Luego eliminar el alojamiento
        $stmt = $pdo->prepare("DELETE FROM accommodations WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: index.php?success=1');
    } catch(PDOException $e) {
        header('Location: index.php?error=1');
    }
} else {
    header('Location: index.php');
}
exit;