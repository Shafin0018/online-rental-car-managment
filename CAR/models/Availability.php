<?php
require_once __DIR__ . '/../config/database.php';

class Availability {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function setAvailability($car_id, $date, $is_available, $reason = null) {
        $sql = "INSERT INTO car_availability (car_id, date, is_available, reason)
                VALUES (:car_id, :date, :is_available, :reason)
                ON DUPLICATE KEY UPDATE
                is_available = :is_available, reason = :reason";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':car_id' => $car_id,
            ':date' => $date,
            ':is_available' => $is_available,
            ':reason' => $reason
        ]);
    }
    
    public function getAvailability($car_id, $start_date, $end_date) {
        $sql = "SELECT * FROM car_availability 
                WHERE car_id = :car_id 
                AND date BETWEEN :start_date AND :end_date
                ORDER BY date";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':car_id' => $car_id,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        return $stmt->fetchAll();
    }
    
    public function getUnavailableDates($car_id, $owner_id = null) {
        $sql = "SELECT ca.date, ca.reason, ca.is_available
                FROM car_availability ca";
        
        if ($owner_id) {
            $sql .= " JOIN cars c ON ca.car_id = c.id 
                     WHERE ca.car_id = :car_id AND c.owner_id = :owner_id";
            $params = [':car_id' => $car_id, ':owner_id' => $owner_id];
        } else {
            $sql .= " WHERE ca.car_id = :car_id";
            $params = [':car_id' => $car_id];
        }
        
        $sql .= " AND ca.date >= CURDATE() ORDER BY ca.date";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function removeAvailability($car_id, $date, $owner_id) {
        $sql = "DELETE ca FROM car_availability ca
                JOIN cars c ON ca.car_id = c.id
                WHERE ca.car_id = :car_id AND ca.date = :date AND c.owner_id = :owner_id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':car_id' => $car_id,
            ':date' => $date,
            ':owner_id' => $owner_id
        ]);
    }
    
    public function getBlockedDates($car_id, $owner_id) {
        $sql = "SELECT DISTINCT blocked_date as date, 'booking' as type, 'Booked' as reason
                FROM (
                    SELECT DATE_ADD(b.start_date, INTERVAL seq.seq DAY) as blocked_date
                    FROM bookings b
                    JOIN cars c ON b.car_id = c.id
                    JOIN (
                        SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
                        SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION
                        SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
                    ) seq ON DATE_ADD(b.start_date, INTERVAL seq.seq DAY) <= b.end_date
                    WHERE c.id = :car_id AND c.owner_id = :owner_id
                    AND b.status IN ('confirmed', 'pending')
                    AND DATE_ADD(b.start_date, INTERVAL seq.seq DAY) >= CURDATE()
                ) booking_dates
                UNION
                SELECT ca.date, 'manual' as type, COALESCE(ca.reason, 'Manually blocked') as reason
                FROM car_availability ca
                JOIN cars c ON ca.car_id = c.id
                WHERE ca.car_id = :car_id AND c.owner_id = :owner_id
                AND ca.is_available = 0 AND ca.date >= CURDATE()
                ORDER BY date";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':car_id' => $car_id, ':owner_id' => $owner_id]);
        return $stmt->fetchAll();
    }
    
    public function bulkUpdateAvailability($car_id, $dates_data, $owner_id) {
        try {
            $this->conn->beginTransaction();
            
            foreach ($dates_data as $date_info) {
                $sql = "INSERT INTO car_availability (car_id, date, is_available, reason)
                        SELECT :car_id, :date, :is_available, :reason
                        FROM cars WHERE id = :car_id AND owner_id = :owner_id
                        ON DUPLICATE KEY UPDATE
                        is_available = VALUES(is_available), reason = VALUES(reason)";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':car_id' => $car_id,
                    ':date' => $date_info['date'],
                    ':is_available' => $date_info['is_available'],
                    ':reason' => $date_info['reason'],
                    ':owner_id' => $owner_id
                ]);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
