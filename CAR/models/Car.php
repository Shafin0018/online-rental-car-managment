<?php
require_once __DIR__ . '/../config/database.php';

class Car {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function addCar($data) {
        $sql = "INSERT INTO cars (owner_id, make, model, year, color, license_plate, 
                daily_rate, description, car_image, location, fuel_type, transmission, seats)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute(array(
            $data['owner_id'],
            $data['make'],
            $data['model'],
            $data['year'],
            $data['color'],
            $data['license_plate'],
            $data['daily_rate'],
            $data['description'],
            $data['car_image'],
            $data['location'],
            $data['fuel_type'],
            $data['transmission'],
            $data['seats']
        ));
    }
    
    public function getCarsByOwner($owner_id) {
        $sql = "SELECT * FROM cars WHERE owner_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        return $stmt->fetchAll();
    }
    
    public function getCarById($car_id, $owner_id = null) {
        $sql = "SELECT * FROM cars WHERE id = ?";
        $params = array($car_id);
        
        if ($owner_id) {
            $sql .= " AND owner_id = ?";
            $params[] = $owner_id;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function updateCar($car_id, $data, $owner_id) {
        $sql = "UPDATE cars SET make = ?, model = ?, year = ?, 
                color = ?, license_plate = ?, daily_rate = ?,
                description = ?, location = ?, fuel_type = ?,
                transmission = ?, seats = ?";
        
        $params = array(
            $data['make'],
            $data['model'],
            $data['year'],
            $data['color'],
            $data['license_plate'],
            $data['daily_rate'],
            $data['description'],
            $data['location'],
            $data['fuel_type'],
            $data['transmission'],
            $data['seats']
        );
        
        if (!empty($data['car_image'])) {
            $sql .= ", car_image = ?";
            $params[] = $data['car_image'];
        }
        
        $sql .= " WHERE id = ? AND owner_id = ?";
        $params[] = $car_id;
        $params[] = $owner_id;
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function deleteCar($car_id, $owner_id) {
        $sql = "DELETE FROM cars WHERE id = ? AND owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array($car_id, $owner_id));
    }
    
    public function toggleAvailability($car_id, $owner_id) {
        $sql = "UPDATE cars SET is_available = NOT is_available 
                WHERE id = ? AND owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array($car_id, $owner_id));
    }
    
    public function checkLicensePlateExists($license_plate, $exclude_id = null) {
        $sql = "SELECT id FROM cars WHERE license_plate = ?";
        $params = array($license_plate);
        
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }
    
    public function getCarStats($owner_id) {
        $sql = "SELECT 
                    COUNT(*) as total_cars,
                    SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_cars,
                    COALESCE(AVG(daily_rate), 0) as avg_daily_rate
                FROM cars WHERE owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        
        $result = $stmt->fetch();
        if (!$result) {
            return array(
                'total_cars' => 0,
                'available_cars' => 0,
                'avg_daily_rate' => 0
            );
        }
        return $result;
    }
}
