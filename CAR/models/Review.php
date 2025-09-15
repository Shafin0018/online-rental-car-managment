<?php
require_once __DIR__ . '/../config/database.php';

class Review {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function getReviewsByOwner($owner_id, $limit = null) {
        $sql = "SELECT r.*, c.make, c.model, c.year, c.license_plate,
                       u.full_name as customer_name, u.email as customer_email,
                       b.start_date, b.end_date
                FROM reviews r
                LEFT JOIN cars c ON r.car_id = c.id
                LEFT JOIN users u ON r.customer_id = u.id
                LEFT JOIN bookings b ON r.booking_id = b.id
                WHERE r.owner_id = ?
                ORDER BY r.created_at DESC";
        
        $params = array($owner_id);
        
        if ($limit && is_numeric($limit)) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getReviewById($review_id, $owner_id) {
        $sql = "SELECT r.*, c.make, c.model, c.year, c.license_plate,
                       u.full_name as customer_name, u.email as customer_email,
                       b.start_date, b.end_date
                FROM reviews r
                LEFT JOIN cars c ON r.car_id = c.id
                LEFT JOIN users u ON r.customer_id = u.id
                LEFT JOIN bookings b ON r.booking_id = b.id
                WHERE r.id = ? AND r.owner_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($review_id, $owner_id));
        return $stmt->fetch();
    }
    
    public function addResponse($review_id, $response, $owner_id) {
        $sql = "UPDATE reviews SET owner_response = ?, response_date = NOW()
                WHERE id = ? AND owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array($response, $review_id, $owner_id));
    }
    
    public function updateResponse($review_id, $response, $owner_id) {
        $sql = "UPDATE reviews SET owner_response = ?, response_date = NOW()
                WHERE id = ? AND owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array($response, $review_id, $owner_id));
    }
    
    public function getReviewStats($owner_id) {
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    COALESCE(AVG(rating), 0) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star,
                    SUM(CASE WHEN owner_response IS NOT NULL THEN 1 ELSE 0 END) as responded_reviews
                FROM reviews WHERE owner_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        
        $result = $stmt->fetch();
        if (!$result) {
            return array(
                'total_reviews' => 0,
                'average_rating' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0,
                'responded_reviews' => 0
            );
        }
        return $result;
    }
    
    public function getReviewsNeedingResponse($owner_id, $limit = 10) {
        $sql = "SELECT r.*, c.make, c.model, c.year, c.license_plate,
                       u.full_name as customer_name, u.email as customer_email
                FROM reviews r
                LEFT JOIN cars c ON r.car_id = c.id
                LEFT JOIN users u ON r.customer_id = u.id
                WHERE r.owner_id = ? AND r.owner_response IS NULL
                ORDER BY r.created_at DESC
                LIMIT " . intval($limit);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        return $stmt->fetchAll();
    }
    
    public function getRecentReviews($owner_id, $limit = 5) {
        return $this->getReviewsByOwner($owner_id, $limit);
    }
    
    public function getCarRatings($owner_id) {
        $sql = "SELECT c.id, c.make, c.model, c.year,
                       COUNT(r.id) as review_count,
                       COALESCE(AVG(r.rating), 0) as average_rating
                FROM cars c
                LEFT JOIN reviews r ON c.id = r.car_id
                WHERE c.owner_id = ?
                GROUP BY c.id, c.make, c.model, c.year
                HAVING COUNT(r.id) > 0
                ORDER BY average_rating DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($owner_id));
        return $stmt->fetchAll();
    }
}
