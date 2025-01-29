<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($username, $email, $password) {
        try {
            // Verificar si el usuario existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                return false;
            }
            
            // Crear usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$username, $email, $hashed_password]);
        } catch(PDOException $e) {
            error_log("Error en registro: " . $e->getMessage());
            return false;
        }
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, is_admin FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }
}