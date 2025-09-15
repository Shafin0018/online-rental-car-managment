<?php
require_once __DIR__ . '/../config/database.php';

class Booking {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function getBookingsByOwner($owner_id, $status = null, $limit = null) {
        $sql = "SELECT b.*, c.make, c.model, c.year, c.license_plate, 
                       u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
                FROM bookings b
                LEFT JOIN cars c ON b.car_id = c.id
                LEFT JOIN users u ON b.customer_id = u.id
                WHERE b.owner_id = ?";
        
        $params = array($owner_id);
        
        if ($status) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        if ($limit && is_numeric($limit)) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getBookingById($booking_id, $owner_id) {
        $sql = "SELECT b.*, c.make, c.model, c.year, c.license_plate, c.car_image,
                       u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
                FROM bookings b
                LEFT JOIN cars c ON b.car_id = c.id
                LEFT JOIN users u ON b.customer_id = u.id
                WHERE b.id = ? AND b.owner_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($booking_id, $owner_id));
        return $stmt->fetch();
    }
    
    public function updateBookingStatus($booking_id, $status, $owner_id) {
        $sql = "UPDATE bookings SET status = ? WHERE id = ? AND owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array($status, $booking_id, $owner_id));
    }
    
    public function getBookingStats($owner_id) {
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                FROM bookings WHERE owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        
        $result = $stmt->fetch();
        if (!$result) {
            return array(
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
                'cancelled_bookings' => 0
            );
        }
        return $result;
    }
    
    public function getEarningsData($owner_id, $period = 'month') {
        switch ($period) {
            case 'week':
                $date_format = '%Y-%u';
                $date_interval = 'WEEK';
                $interval_count = 12;
                break;
            case 'year':
                $date_format = '%Y';
                $date_interval = 'YEAR';
                $interval_count = 5;
                break;
            default:
                $date_format = '%Y-%m';
                $date_interval = 'MONTH';
                $interval_count = 12;
        }
        
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '$date_format') as period,
                    COALESCE(SUM(total_amount), 0) as total_earnings,
                    COUNT(*) as booking_count
                FROM bookings 
                WHERE owner_id = ? 
                AND status IN ('confirmed', 'completed')
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval_count $date_interval)
                GROUP BY DATE_FORMAT(created_at, '$date_format')
                ORDER BY period DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        return $stmt->fetchAll();
    }
    
    public function getTotalEarnings($owner_id, $status = null) {
        if ($status === null) {
            $status = array('confirmed', 'completed');
        }
        
        if (!is_array($status)) {
            $status = array($status);
        }
        
        $placeholders = implode(',', array_fill(0, count($status), '?'));
        $sql = "SELECT 
                    COALESCE(SUM(total_amount), 0) as total_earnings,
                    COUNT(*) as total_bookings
                FROM bookings 
                WHERE owner_id = ? AND status IN ($placeholders)";
        
        $params = array_merge(array($owner_id), $status);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        if (!$result) {
            return array('total_earnings' => 0, 'total_bookings' => 0);
        }
        return $result;
    }
    
    public function getRecentBookings($owner_id, $limit = 5) {
        return $this->getBookingsByOwner($owner_id, null, $limit);
    }
    
    public function getMonthlyEarnings($owner_id) {
        $sql = "SELECT 
                    MONTH(created_at) as month,
                    YEAR(created_at) as year,
                    COALESCE(SUM(total_amount), 0) as earnings
                FROM bookings 
                WHERE owner_id = ? 
                AND status IN ('confirmed', 'completed')
                AND YEAR(created_at) = YEAR(CURDATE())
                GROUP BY YEAR(created_at), MONTH(created_at)
                ORDER BY year, month";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        return $stmt->fetchAll();
    }
}
