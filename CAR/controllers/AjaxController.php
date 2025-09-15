<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Car.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Availability.php';

header('Content-Type: application/json');
checkCarOwnerAuth();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'toggle_car_availability':
            handleToggleCarAvailability();
            break;
        case 'update_booking_status':
            handleUpdateBookingStatus();
            break;
        case 'respond_to_review':
            handleRespondToReview();
            break;
        case 'set_availability':
            handleSetAvailability();
            break;
        case 'get_earnings_chart':
            handleGetEarningsChart();
            break;
        case 'get_booking_details':
            handleGetBookingDetails();
            break;
        case 'delete_car':
            handleDeleteCar();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

function handleToggleCarAvailability() {
    $car_id = $_POST['car_id'] ?? null;
    $owner_id = $_SESSION['user_id'];
    
    if (!$car_id) {
        echo json_encode(['success' => false, 'message' => 'Car ID is required']);
        return;
    }
    
    $carModel = new Car();
    if ($carModel->toggleAvailability($car_id, $owner_id)) {
        $car = $carModel->getCarById($car_id, $owner_id);
        echo json_encode([
            'success' => true, 
            'message' => 'Car availability updated successfully',
            'is_available' => (bool)$car['is_available']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update car availability']);
    }
}

function handleUpdateBookingStatus() {
    $booking_id = $_POST['booking_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $owner_id = $_SESSION['user_id'];
    
    if (!$booking_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Booking ID and status are required']);
        return;
    }
    
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    $bookingModel = new Booking();
    $booking = $bookingModel->getBookingById($booking_id, $owner_id);
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        return;
    }
    
    if ($bookingModel->updateBookingStatus($booking_id, $status, $owner_id)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Booking status updated successfully',
            'new_status' => $status,
            'booking_id' => $booking_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
    }
}

function handleRespondToReview() {
    $review_id = $_POST['review_id'] ?? null;
    $response = trim($_POST['response'] ?? '');
    $owner_id = $_SESSION['user_id'];
    
    if (!$review_id || empty($response)) {
        echo json_encode(['success' => false, 'message' => 'Review ID and response are required']);
        return;
    }
    
    if (strlen($response) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Response must be less than 1000 characters']);
        return;
    }
    
    $reviewModel = new Review();
    if ($reviewModel->addResponse($review_id, $response, $owner_id)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Response added successfully',
            'response' => htmlspecialchars($response),
            'response_date' => date('M d, Y g:i A')
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add response']);
    }
}

function handleSetAvailability() {
    $car_id = $_POST['car_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $is_available = isset($_POST['is_available']) ? (bool)$_POST['is_available'] : true;
    $reason = trim($_POST['reason'] ?? '');
    $owner_id = $_SESSION['user_id'];
    
    if (!$car_id || !$date) {
        echo json_encode(['success' => false, 'message' => 'Car ID and date are required']);
        return;
    }
    
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        return;
    }
    
    if ($date_obj < new DateTime('today')) {
        echo json_encode(['success' => false, 'message' => 'Cannot set availability for past dates']);
        return;
    }
    
    $carModel = new Car();
    $car = $carModel->getCarById($car_id, $owner_id);
    if (!$car) {
        echo json_encode(['success' => false, 'message' => 'Car not found']);
        return;
    }
    
    $availabilityModel = new Availability();
    if ($is_available) {
        if ($availabilityModel->removeAvailability($car_id, $date, $owner_id)) {
            echo json_encode(['success' => true, 'message' => 'Date marked as available']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update availability']);
        }
    } else {
        if ($availabilityModel->setAvailability($car_id, $date, false, $reason)) {
            echo json_encode(['success' => true, 'message' => 'Date marked as unavailable']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update availability']);
        }
    }
}

function handleGetEarningsChart() {
    $period = $_GET['period'] ?? 'month';
    $owner_id = $_SESSION['user_id'];
    
    $bookingModel = new Booking();
    $earnings_data = $bookingModel->getEarningsData($owner_id, $period);
    
    $labels = [];
    $values = [];
    
    foreach ($earnings_data as $data) {
        $labels[] = $data['period'];
        $values[] = (float)$data['total_earnings'];
    }
    
    echo json_encode([
        'success' => true,
        'labels' => array_reverse($labels),
        'values' => array_reverse($values)
    ]);
}

function handleGetBookingDetails() {
    $booking_id = $_GET['booking_id'] ?? null;
    $owner_id = $_SESSION['user_id'];
    
    if (!$booking_id) {
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        return;
    }
    
    $bookingModel = new Booking();
    $booking = $bookingModel->getBookingById($booking_id, $owner_id);
    
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'booking' => [
            'id' => $booking['id'],
            'car' => $booking['make'] . ' ' . $booking['model'] . ' (' . $booking['year'] . ')',
            'license_plate' => $booking['license_plate'],
            'customer_name' => $booking['customer_name'],
            'customer_email' => $booking['customer_email'],
            'customer_phone' => $booking['customer_phone'],
            'start_date' => formatDate($booking['start_date']),
            'end_date' => formatDate($booking['end_date']),
            'total_days' => $booking['total_days'],
            'daily_rate' => formatCurrency($booking['daily_rate']),
            'total_amount' => formatCurrency($booking['total_amount']),
            'status' => $booking['status'],
            'special_requests' => $booking['special_requests'],
            'booking_date' => formatDate($booking['booking_date'])
        ]
    ]);
}

function handleDeleteCar() {
    $car_id = $_POST['car_id'] ?? null;
    $owner_id = $_SESSION['user_id'];
    
    if (!$car_id) {
        echo json_encode(['success' => false, 'message' => 'Car ID is required']);
        return;
    }
    
    $carModel = new Car();
    if ($carModel->deleteCar($car_id, $owner_id)) {
        echo json_encode(['success' => true, 'message' => 'Car deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete car']);
    }
}
?>
