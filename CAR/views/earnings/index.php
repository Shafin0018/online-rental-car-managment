<?php
// views/earnings/index.php - Earnings dashboard view
$page_title = "Earnings Dashboard";
include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Earnings Dashboard</h1>
            <div class="d-flex gap-2">
                <select class="form-control" id="period-select" style="width: 150px;">
                    <option value="week" <?php echo (isset($_GET['period']) && $_GET['period'] === 'week') ? 'selected' : ''; ?>>Weekly</option>
                    <option value="month" <?php echo (!isset($_GET['period']) || $_GET['period'] === 'month') ? 'selected' : ''; ?>>Monthly</option>
                    <option value="year" <?php echo (isset($_GET['period']) && $_GET['period'] === 'year') ? 'selected' : ''; ?>>Yearly</option>
                </select>
                <button class="btn btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <!-- Earnings Overview -->
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Total Earnings</h3>
                    <div class="stat-icon success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo formatCurrency($data['total_earnings']['total_earnings'] ?? 0); ?></div>
                <div class="stat-change">
                    From <?php echo $data['total_earnings']['total_bookings'] ?? 0; ?> completed bookings
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">This Month</h3>
                    <div class="stat-icon info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?php 
                    $currentMonth = date('n');
                    $thisMonthEarnings = 0;
                    foreach ($data['monthly_earnings'] as $month) {
                        if ($month['month'] == $currentMonth) {
                            $thisMonthEarnings = $month['earnings'];
                            break;
                        }
                    }
                    echo formatCurrency($thisMonthEarnings);
                    ?>
                </div>
                <div class="stat-change">
                    <?php echo date('F Y'); ?>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Average per Booking</h3>
                    <div class="stat-icon warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?php 
                    $totalBookings = $data['total_earnings']['total_bookings'] ?? 0;
                    $avgPerBooking = $totalBookings > 0 ? ($data['total_earnings']['total_earnings'] ?? 0) / $totalBookings : 0;
                    echo formatCurrency($avgPerBooking);
                    ?>
                </div>
                <div class="stat-change">
                    Per completed rental
                </div>
            </div>

            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Active Bookings</h3>
                    <div class="stat-icon primary">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo ($data['booking_stats']['confirmed_bookings'] ?? 0) + ($data['booking_stats']['pending_bookings'] ?? 0); ?></div>
                <div class="stat-change">
                    <?php echo $data['booking_stats']['pending_bookings'] ?? 0; ?> pending approval
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Earnings Trend Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Earnings Trend</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earnings-trend-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Booking Status Distribution -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Status</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="booking-status-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Earnings Breakdown (<?php echo date('Y'); ?>)</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Earnings</th>
                                <th>Bookings</th>
                                <th>Average per Booking</th>
                                <th>Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $monthNames = array(
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            );
                            
                            $monthlyData = array();
                            foreach ($data['monthly_earnings'] as $month) {
                                $monthlyData[$month['month']] = $month;
                            }
                            
                            $previousEarnings = 0;
                            for ($i = 1; $i <= 12; $i++):
                                $monthData = isset($monthlyData[$i]) ? $monthlyData[$i] : null;
                                $earnings = $monthData ? $monthData['earnings'] : 0;
                                $growth = ($previousEarnings > 0 && $earnings > 0) ? (($earnings - $previousEarnings) / $previousEarnings) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?php echo $monthNames[$i]; ?></td>
                                    <td><strong><?php echo formatCurrency($earnings); ?></strong></td>
                                    <td><?php echo $monthData ? '~' : '0'; ?></td>
                                    <td><?php echo $monthData ? formatCurrency($earnings) : formatCurrency(0); ?></td>
                                    <td>
                                        <?php if ($growth != 0): ?>
                                            <span class="<?php echo $growth > 0 ? 'text-success' : 'text-danger'; ?>">
                                                <i class="fas fa-arrow-<?php echo $growth > 0 ? 'up' : 'down'; ?>"></i>
                                                <?php echo number_format(abs($growth), 1); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                if ($earnings > 0) $previousEarnings = $earnings;
                            endfor; 
                            ?>
                        </tbody>
                    </table>
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
                    <a href="index.php?page=bookings&status=pending" class="btn btn-warning">
                        <i class="fas fa-clock"></i> Review Pending Bookings (<?php echo $data['booking_stats']['pending_bookings'] ?? 0; ?>)
                    </a>
                    <a href="index.php?page=cars" class="btn btn-primary">
                        <i class="fas fa-car"></i> Manage Cars
                    </a>
                    <a href="index.php?page=add_car" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Car
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Period selector
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period-select');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('period', this.value);
            window.location.href = currentUrl.toString();
        });
    }
    
    // Initialize charts
    initEarningsCharts();
});

function initEarningsCharts() {
    // Earnings Trend Chart
    const earningsCanvas = document.getElementById('earnings-trend-chart');
    if (earningsCanvas) {
        const monthlyEarnings = <?php echo json_encode($data['monthly_earnings']); ?>;
        const labels = monthlyEarnings.map(item => {
            const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                              'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return monthNames[parseInt(item.month)];
        });
        const values = monthlyEarnings.map(item => parseFloat(item.earnings));
        
        new Chart(earningsCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Earnings',
                    data: values,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    }
    
    // Booking Status Chart
    const statusCanvas = document.getElementById('booking-status-chart');
    if (statusCanvas) {
        const bookingStats = <?php echo json_encode($data['booking_stats']); ?>;
        
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        bookingStats.pending_bookings || 0,
                        bookingStats.confirmed_bookings || 0,
                        bookingStats.completed_bookings || 0,
                        bookingStats.cancelled_bookings || 0
                    ],
                    backgroundColor: [
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>