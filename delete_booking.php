
<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$_GET['id'], $_SESSION['user_id']])) {
            header('Location: bookings.php?success=deleted');
        } else {
            header('Location: bookings.php?error=delete_failed');
        }
    } catch(PDOException $e) {
        header('Location: bookings.php?error=system_error');
    }
} else {
    header('Location: bookings.php');
}
exit;