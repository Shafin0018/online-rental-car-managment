<?php
// views/dashboard/index.php - Main dashboard view
$page_title = "Dashboard";
include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Dashboard Overview</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                </div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>


        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Total Cars</h3>
                    <div class="stat-icon primary">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $data['car_stats']['total_cars'] ?? 0; ?></div>
                <div class="stat-change">
                    <?php 
                    $available = $data['car_stats']['available_cars'] ?? 0;
                    $total = $data['car_stats']['total_cars'] ?? 0;
                    echo $available . ' available';
                    ?>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Total Bookings</h3>
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $data['booking_stats']['total_bookings'] ?? 0; ?></div>
                <div class="stat-change">
                    <?php echo $data['booking_stats']['pending_bookings'] ?? 0; ?> pending
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Total Earnings</h3>
                    <div class="stat-icon warning">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo formatCurrency($data['total_earnings']['total_earnings'] ?? 0); ?></div>
                <div class="stat-change positive">
                    From <?php echo $data['total_earnings']['total_bookings'] ?? 0; ?> completed bookings
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">Average Rating</h3>
                    <div class="stat-icon info">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?php 
                    $avg_rating = $data['review_stats']['average_rating'] ?? 0;
                    echo number_format($avg_rating, 1);
                    ?>
                </div>
                <div class="stat-change">
                    From <?php echo $data['review_stats']['total_reviews'] ?? 0; ?> reviews
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Monthly Earnings Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Earnings</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earnings-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Booking Status Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Status Distribution</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="bookings-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Bookings</h3>
                    <a href="index.php?page=bookings" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($data['recent_bookings'])): ?>
                        <p class="text-muted text-center">No recent bookings found.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Car</th>
                                        <th>Customer</th>
                                        <th>Dates</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['recent_bookings'] as $booking): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($booking['license_plate']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                            <td>
                                                <?php echo formatDate($booking['start_date']); ?><br>
                                                <small class="text-muted">to <?php echo formatDate($booking['end_date']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo getStatusColor($booking['status']); ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatCurrency($booking['total_amount']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reviews</h3>
                    <a href="index.php?page=reviews" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($data['recent_reviews'])): ?>
                        <p class="text-muted text-center">No recent reviews found.</p>
                    <?php else: ?>
                        <?php foreach ($data['recent_reviews'] as $review): ?>
                            <div class="review-item" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                                <div class="d-flex justify-between items-center mb-2">
                                    <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#f59e0b' : '#d1d5db'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars(substr($review['comment'], 0, 100)); ?>...</p>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($review['make'] . ' ' . $review['model']); ?> • 
                                    <?php echo formatDate($review['created_at']); ?>
                                </small>
                                <?php if (empty($review['owner_response'])): ?>
                                    <div class="mt-2">
                                        <span class="badge badge-warning">Needs Response</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3">
                    <a href="index.php?page=add_car" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Car
                    </a>
                    <a href="index.php?page=bookings&status=pending" class="btn btn-warning">
                        <i class="fas fa-clock"></i> View Pending Bookings (<?php echo $data['booking_stats']['pending_bookings'] ?? 0; ?>)
                    </a>
                    <a href="index.php?page=reviews" class="btn btn-info">
                        <i class="fas fa-comment"></i> Respond to Reviews
                    </a>
                    <a href="index.php?page=availability" class="btn btn-success">
                        <i class="fas fa-calendar-alt"></i> Manage Availability
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Pass booking stats to JavaScript for charts
window.bookingStats = <?php echo json_encode($data['booking_stats']); ?>;

// Pass monthly earnings data for chart
window.monthlyEarnings = <?php echo json_encode($data['monthly_earnings']); ?>;
</script>

<?php
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

include __DIR__ . '/../layouts/footer.php';
?>