<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $booking_id = $_GET['id'];
    $status = $_GET['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $booking_id]);
        header('Location: view_bookings.php?success=1');
    } catch(PDOException $e) {
        header('Location: view_bookings.php?error=1');
    }
} else {
    header('Location: view_bookings.php');
}
exit;