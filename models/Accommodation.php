<?php
class Accommodation {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllAccommodations() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM accommodations WHERE status = 'available' ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting accommodations: " . $e->getMessage());
            return [];
        }
    }
    
    public function addAccommodation($name, $description, $price, $image_url = null) {
        try {
            $sql = "INSERT INTO accommodations (name, description, price, image_url) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $description, $price, $image_url]);
        } catch(PDOException $e) {
            error_log("Error adding accommodation: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAccommodationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM accommodations WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting accommodation: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateAccommodation($id, $name, $description, $price, $image_url = null) {
        try {
            $sql = "UPDATE accommodations SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $description, $price, $image_url, $id]);
        } catch(PDOException $e) {
            error_log("Error updating accommodation: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteAccommodation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM accommodations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting accommodation: " . $e->getMessage());
            return false;
        }
    }
    
    public function searchAccommodations($search = null, $min_price = null, $max_price = null) {
        try {
            $sql = "SELECT * FROM accommodations WHERE status = 'available'";
            $params = [];
            
            if ($search) {
                $sql .= " AND (name LIKE ? OR description LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($min_price !== null) {
                $sql .= " AND price >= ?";
                $params[] = $min_price;
            }
            
            if ($max_price !== null) {
                $sql .= " AND price <= ?";
                $params[] = $max_price;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error searching accommodations: " . $e->getMessage());
            return [];
        }
    }
}