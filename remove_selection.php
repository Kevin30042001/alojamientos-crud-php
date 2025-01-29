<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['accommodation_id'])) {
    header('Location: user_profile.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM user_selections WHERE user_id = ? AND accommodation_id = ?");
    $stmt->execute([$_SESSION['user_id'], $_POST['accommodation_id']]);
    header('Location: user_profile.php');
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}