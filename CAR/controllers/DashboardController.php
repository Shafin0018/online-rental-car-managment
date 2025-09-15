<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Car.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Availability.php';

class DashboardController {
    private $carModel;
    private $bookingModel;
    private $reviewModel;
    private $availabilityModel;
    
    public function __construct() {
        checkCarOwnerAuth();
        $this->carModel = new Car();
        $this->bookingModel = new Booking();
        $this->reviewModel = new Review();
        $this->availabilityModel = new Availability();
    }
    
    public function dashboard() {
        $owner_id = $_SESSION['user_id'];
        $data = [
            'car_stats' => $this->carModel->getCarStats($owner_id),
            'booking_stats' => $this->bookingModel->getBookingStats($owner_id),
            'review_stats' => $this->reviewModel->getReviewStats($owner_id),
            'total_earnings' => $this->bookingModel->getTotalEarnings($owner_id),
            'recent_bookings' => $this->bookingModel->getRecentBookings($owner_id, 5),
            'recent_reviews' => $this->reviewModel->getRecentReviews($owner_id, 3),
            'pending_reviews' => $this->reviewModel->getReviewsNeedingResponse($owner_id, 3),
            'monthly_earnings' => $this->bookingModel->getMonthlyEarnings($owner_id)
        ];
        include __DIR__ . '/../views/dashboard/index.php';
    }
    
    public function cars() {
        $owner_id = $_SESSION['user_id'];
        $cars = $this->carModel->getCarsByOwner($owner_id);
        include __DIR__ . '/../views/cars/index.php';
    }
    
    public function addCarForm() {
        include __DIR__ . '/../views/cars/add.php';
    }
    
    public function addCar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cars');
            exit;
        }
        
        $errors = $this->validateCarData($_POST);
        
        if (empty($errors)) {
            $data = $this->prepareCarData($_POST);
            $data['owner_id'] = $_SESSION['user_id'];
            
            if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === 0) {
                $upload_result = $this->uploadCarImage($_FILES['car_image']);
                if ($upload_result['success']) {
                    $data['car_image'] = $upload_result['filename'];
                } else {
                    $errors[] = $upload_result['error'];
                }
            } else {
                $data['car_image'] = null;
            }
            
            if (empty($errors) && $this->carModel->addCar($data)) {
                $_SESSION['success_message'] = 'Car added successfully!';
                header('Location: index.php?page=cars');
                exit;
            } else {
                $errors[] = 'Failed to add car. Please try again.';
            }
        }
        
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: index.php?page=add_car');
        exit;
    }
    
    public function editCarForm() {
        $car_id = $_GET['id'] ?? null;
        if (!$car_id) {
            header('Location: index.php?page=cars');
            exit;
        }
        
        $car = $this->carModel->getCarById($car_id, $_SESSION['user_id']);
        if (!$car) {
            $_SESSION['error_message'] = 'Car not found.';
            header('Location: index.php?page=cars');
            exit;
        }
        
        include(__DIR__ . '/../views/cars/edit.php');
    }
    
    public function editCar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cars');
            exit;
        }
        
        $car_id = $_POST['car_id'] ?? null;
        if (!$car_id) {
            header('Location: index.php?page=cars');
            exit;
        }
        
        $errors = $this->validateCarData($_POST, $car_id);
        
        if (empty($errors)) {
            $data = $this->prepareCarData($_POST);
            
            if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === 0) {
                $upload_result = $this->uploadCarImage($_FILES['car_image']);
                if ($upload_result['success']) {
                    $data['car_image'] = $upload_result['filename'];
                } else {
                    $errors[] = $upload_result['error'];
                }
            }
            
            if (empty($errors) && $this->carModel->updateCar($car_id, $data, $_SESSION['user_id'])) {
                $_SESSION['success_message'] = 'Car updated successfully!';
                header('Location: index.php?page=cars');
                exit;
            } else {
                $errors[] = 'Failed to update car. Please try again.';
            }
        }
        
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: index.php?page=edit_car&id=' . $car_id);
        exit;
    }
    
    public function deleteCar() {
        $car_id = $_POST['car_id'] ?? null;
        if ($car_id && $this->carModel->deleteCar($car_id, $_SESSION['user_id'])) {
            $_SESSION['success_message'] = 'Car deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete car.';
        }
        
        header('Location: index.php?page=cars');
        exit;
    }
    
    public function bookings() {
        $owner_id = $_SESSION['user_id'];
        $status_filter = $_GET['status'] ?? null;
        $bookings = $this->bookingModel->getBookingsByOwner($owner_id, $status_filter);
        include __DIR__ . '/../views/bookings/index.php';
    }
    
    public function viewBooking() {
        $booking_id = $_GET['id'] ?? null;
        if (!$booking_id) {
            header('Location: index.php?page=bookings');
            exit;
        }
        
        $booking = $this->bookingModel->getBookingById($booking_id, $_SESSION['user_id']);
        if (!$booking) {
            $_SESSION['error_message'] = 'Booking not found.';
            header('Location: index.php?page=bookings');
            exit;
        }
        
        include '../views/bookings/view.php';
    }
    
    public function earnings() {
        $owner_id = $_SESSION['user_id'];
        $period = $_GET['period'] ?? 'month';
        $data = [
            'total_earnings' => $this->bookingModel->getTotalEarnings($owner_id),
            'earnings_data' => $this->bookingModel->getEarningsData($owner_id, $period),
            'monthly_earnings' => $this->bookingModel->getMonthlyEarnings($owner_id),
            'booking_stats' => $this->bookingModel->getBookingStats($owner_id)
        ];
        include __DIR__ . '/../views/earnings/index.php';
    }
    
    public function reviews() {
        $owner_id = $_SESSION['user_id'];
        $reviews = $this->reviewModel->getReviewsByOwner($owner_id);
        $stats = $this->reviewModel->getReviewStats($owner_id);
        include __DIR__ . '/../views/reviews/index.php';
    }
    
    public function availability() {
        $owner_id = $_SESSION['user_id'];
        $cars = $this->carModel->getCarsByOwner($owner_id);
        $selected_car = $_GET['car_id'] ?? ($cars[0]['id'] ?? null);
        $blocked_dates = [];
        if ($selected_car) {
            $blocked_dates = $this->availabilityModel->getBlockedDates($selected_car, $owner_id);
        }
        include __DIR__ . '/../views/availability/index.php';
    }
    
    private function validateCarData($data, $exclude_id = null) {
        $errors = [];
        if (empty($data['make'])) $errors[] = 'Make is required.';
        if (empty($data['model'])) $errors[] = 'Model is required.';
        if (empty($data['year']) || !is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > date('Y') + 1) $errors[] = 'Please enter a valid year.';
        if (empty($data['license_plate'])) $errors[] = 'License plate is required.';
        else if ($this->carModel->checkLicensePlateExists($data['license_plate'], $exclude_id)) $errors[] = 'License plate already exists.';
        if (empty($data['daily_rate']) || !is_numeric($data['daily_rate']) || $data['daily_rate'] <= 0) $errors[] = 'Please enter a valid daily rate.';
        if (empty($data['location'])) $errors[] = 'Location is required.';
        return $errors;
    }
    
    private function prepareCarData($data) {
        return [
            'make' => sanitizeInput($data['make']),
            'model' => sanitizeInput($data['model']),
            'year' => (int)$data['year'],
            'color' => sanitizeInput($data['color'] ?? ''),
            'license_plate' => strtoupper(sanitizeInput($data['license_plate'])),
            'daily_rate' => (float)$data['daily_rate'],
            'description' => sanitizeInput($data['description'] ?? ''),
            'location' => sanitizeInput($data['location']),
            'fuel_type' => sanitizeInput($data['fuel_type'] ?? 'petrol'),
            'transmission' => sanitizeInput($data['transmission'] ?? 'manual'),
            'seats' => (int)($data['seats'] ?? 5)
        ];
    }
    
    private function uploadCarImage($file) {
        $upload_dir = __DIR__ . '/../uploads/cars/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $max_size = 5 * 1024 * 1024;
        if (!in_array($file['type'], $allowed_types)) return array('success' => false, 'error' => 'Only JPG, PNG, and GIF images are allowed.');
        if ($file['size'] > $max_size) return array('success' => false, 'error' => 'Image size must be less than 5MB.');
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('car_') . '.' . $extension;
        $filepath = $upload_dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $filepath)) return array('success' => true, 'filename' => $filename);
        else return array('success' => false, 'error' => 'Failed to upload image.');
    }
}
