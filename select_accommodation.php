<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['accommodation_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_selections (user_id, accommodation_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_POST['accommodation_id']]);
    header('Location: user_profile.php');
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}