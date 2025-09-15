<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Booking.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'car_owner') {
    echo "Not logged in as car owner";
    exit;
}

echo "<h2>🔍 Booking Update Debug Tool</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_ajax'])) {
    echo "<h3>📡 Testing AJAX Endpoint:</h3>";
    
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $owner_id = $_SESSION['user_id'];
    
    echo "<p><strong>Received data:</strong></p>";
    echo "<ul>";
    echo "<li>Booking ID: " . htmlspecialchars($booking_id) . "</li>";
    echo "<li>Status: " . htmlspecialchars($status) . "</li>";
    echo "<li>Owner ID: " . htmlspecialchars($owner_id) . "</li>";
    echo "</ul>";
    
    try {
        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingById($booking_id, $owner_id);
        if ($booking) {
            echo "<p>✅ Booking found: " . htmlspecialchars($booking['id']) . "</p>";
            echo "<p>Current status: " . htmlspecialchars($booking['status']) . "</p>";
            
            $result = $bookingModel->updateBookingStatus($booking_id, $status, $owner_id);
            if ($result) {
                echo "<p>✅ Status update successful!</p>";
                $updatedBooking = $bookingModel->getBookingById($booking_id, $owner_id);
                echo "<p>New status: " . htmlspecialchars($updatedBooking['status']) . "</p>";
            } else {
                echo "<p>❌ Status update failed</p>";
            }
        } else {
            echo "<p>❌ Booking not found or doesn't belong to you</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<hr>";
}

$bookingModel = new Booking();
$bookings = $bookingModel->getBookingsByOwner($_SESSION['user_id']);

echo "<h3>📋 Your Bookings:</h3>";

if (empty($bookings)) {
    echo "<p>❌ No bookings found</p>";
    echo "<p>Let's create a test booking...</p>";
    
    try {
        $conn = getDBConnection();
        $sql = "INSERT INTO bookings (car_id, customer_id, owner_id, start_date, end_date, total_days, daily_rate, total_amount, status) 
                SELECT 
                    c.id, 
                    (SELECT id FROM users WHERE role = 'customer' LIMIT 1),
                    ?, 
                    CURDATE(), 
                    DATE_ADD(CURDATE(), INTERVAL 3 DAY), 
                    3, 
                    c.daily_rate, 
                    c.daily_rate * 3, 
                    'pending'
                FROM cars c 
                WHERE c.owner_id = ? 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute(array($_SESSION['user_id'], $_SESSION['user_id']));
        
        if ($result) {
            echo "<p>✅ Test booking created! Refresh the page.</p>";
        } else {
            echo "<p>❌ Failed to create test booking</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌  Error creating test booking: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Status</th><th>Customer</th><th>Car</th><th>Amount</th><th>Actions</th></tr>";
    
    foreach ($bookings as $booking) {
        echo "<tr>";
        echo "<td>{$booking['id']}</td>";
        echo "<td><span style='background: " . getStatusColor($booking['status']) . "; color: white; padding: 2px 6px; border-radius: 3px;'>{$booking['status']}</span></td>";
        echo "<td>" . htmlspecialchars($booking['customer_name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars(($booking['make'] ?? '') . ' ' . ($booking['model'] ?? '')) . "</td>";
        echo "<td>$" . number_format($booking['total_amount'], 2) . "</td>";
        echo "<td>";
        
        if ($booking['status'] === 'pending') {
            echo "<form method='POST' style='display: inline; margin-right: 5px;'>";
            echo "<input type='hidden' name='test_ajax' value='1'>";
            echo "<input type='hidden' name='booking_id' value='{$booking['id']}'>";
            echo "<input type='hidden' name='status' value='confirmed'>";
            echo "<button type='submit' style='background: green; color: white; border: none; padding: 5px 10px; cursor: pointer;'>✓ Confirm</button>";
            echo "</form>";
            
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='test_ajax' value='1'>";
            echo "<input type='hidden' name='booking_id' value='{$booking['id']}'>";
            echo "<input type='hidden' name='status' value='cancelled'>";
            echo "<button type='submit' style='background: red; color: white; border: none; padding: 5px 10px; cursor: pointer;'>✗ Cancel</button>";
            echo "</form>";
        } else {
            echo "No actions";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function getStatusColor($status) {
    $colors = array(
        'pending' => '#f59e0b',
        'confirmed' => '#3b82f6',
        'completed' => '#10b981',
        'cancelled' => '#ef4444'
    );
    return isset($colors[$status]) ? $colors[$status] : '#6b7280';
}
?>

<h3>🧪 JavaScript Test:</h3>
<div id="js-test-area">
    <button onclick="testAjaxCall()" style="background: blue; color: white; border: none; padding: 10px 20px; cursor: pointer;">
        Test AJAX Call
    </button>
    <div id="ajax-result" style="margin-top: 10px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;"></div>
</div>

<script>
function testAjaxCall() {
    const resultDiv = document.getElementById('ajax-result');
    resultDiv.innerHTML = 'Testing AJAX call...';
    
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_booking_status&booking_id=1&status=confirmed'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        resultDiv.innerHTML = '<strong>Raw Response:</strong><br><pre>' + text + '</pre>';
        try {
            const data = JSON.parse(text);
            resultDiv.innerHTML += '<br><strong>Parsed JSON:</strong><br><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (e) {
            resultDiv.innerHTML += '<br><strong>JSON Parse Error:</strong> ' + e.message;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        resultDiv.innerHTML = '<strong>Error:</strong> ' + error.message;
    });
}

console.log('Debug script loaded');
console.log('Session user:', <?php echo json_encode($_SESSION['user_id'] ?? null); ?>);
console.log('Session role:', <?php echo json_encode($_SESSION['role'] ?? null); ?>);

setTimeout(() => {
    const buttons = document.querySelectorAll('.update-booking-status');
    console.log('Found booking status buttons:', buttons.length);
    
    buttons.forEach((btn, index) => {
        console.log(`Button ${index}:`, {
            bookingId: btn.getAttribute('data-booking-id'),
            status: btn.getAttribute('data-status'),
            onclick: btn.onclick
        });
    });
}, 1000);
</script>

<hr>
<p><a href="index.php?page=bookings">← Back to Bookings Page</a></p>
