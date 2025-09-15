<?php
// views/bookings/index.php - Bookings management view
$page_title = "Bookings";
include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Bookings Management</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline export-csv" data-table="bookings-table" data-filename="bookings.csv">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <!-- Filter Options -->
        <div class="card mb-4">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 200px 200px; gap: 1rem; align-items: end;">
                    <div class="form-group mb-0">
                        <label class="form-label">Search Bookings</label>
                        <input type="text" class="form-control search-input" 
                               data-table="bookings-table" 
                               placeholder="Search by customer name, car, dates...">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Filter by Status</label>
                        <select class="form-control filter-select" data-table="bookings-table" data-column="4" id="status-filter">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Sort by</label>
                        <select class="form-control" id="sort-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="amount_high">Amount High-Low</option>
                            <option value="amount_low">Amount Low-High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Bookings (<?php echo count($bookings); ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($bookings)): ?>
                    <div class="text-center" style="padding: 3rem;">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--text-muted); margin-bottom: 1rem;">No bookings yet</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">Your car bookings will appear here once customers start renting your cars.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table" id="bookings-table">
                            <thead>
                                <tr>
                                    <th>Booking Details</th>
                                    <th>Customer</th>
                                    <th>Car</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>#<?php echo $booking['id']; ?></strong><br>
                                                <small class="text-muted">
                                                    Booked: <?php echo formatDate($booking['created_at']); ?>
                                                </small><br>
                                                <small class="text-muted">
                                                    <?php echo $booking['total_days']; ?> day(s)
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($booking['customer_email']); ?></small><br>
                                                <?php if (!empty($booking['customer_phone'])): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars($booking['customer_phone']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($booking['year'] . ' • ' . $booking['license_plate']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo formatDate($booking['start_date']); ?></strong><br>
                                                <small class="text-muted">to</small><br>
                                                <strong><?php echo formatDate($booking['end_date']); ?></strong>
                                            </div>
                                        </td>
                                        <td class="booking-status">
                                            <span class="badge badge-<?php echo getBookingStatusColor($booking['status']); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo formatCurrency($booking['total_amount']); ?></strong><br>
                                            <small class="text-muted"><?php echo formatCurrency($booking['daily_rate']); ?>/day</small>
                                        </td>
                                        <td class="booking-actions">
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-info view-booking-btn" 
                                                        data-booking-id="<?php echo $booking['id']; ?>"
                                                        data-tooltip="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                    <button class="btn btn-sm btn-success update-booking-status" 
                                                            data-booking-id="<?php echo $booking['id']; ?>"
                                                            data-status="confirmed"
                                                            data-tooltip="Confirm Booking">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger update-booking-status" 
                                                            data-booking-id="<?php echo $booking['id']; ?>"
                                                            data-status="cancelled"
                                                            data-tooltip="Cancel Booking">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php elseif ($booking['status'] === 'confirmed'): ?>
                                                    <button class="btn btn-sm btn-success update-booking-status" 
                                                            data-booking-id="<?php echo $booking['id']; ?>"
                                                            data-status="completed"
                                                            data-tooltip="Mark as Completed">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger update-booking-status" 
                                                            data-booking-id="<?php echo $booking['id']; ?>"
                                                            data-status="cancelled"
                                                            data-tooltip="Cancel Booking">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 0.75rem;">No actions</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <?php if (!empty($bookings)): ?>
            <div class="stats-grid mt-4">
                <?php 
                $statusCounts = array_count_values(array_column($bookings, 'status'));
                $totalRevenue = array_sum(array_map(function($booking) {
                    return ($booking['status'] === 'completed' || $booking['status'] === 'confirmed') ? $booking['total_amount'] : 0;
                }, $bookings));
                ?>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Total Bookings</h3>
                        <div class="stat-icon primary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo count($bookings); ?></div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Pending</h3>
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo isset($statusCounts['pending']) ? $statusCounts['pending'] : 0; ?></div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Completed</h3>
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo isset($statusCounts['completed']) ? $statusCounts['completed'] : 0; ?></div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-header">
                        <h3 class="stat-title">Revenue</h3>
                        <div class="stat-icon info">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo formatCurrency($totalRevenue); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Booking Details Modal -->
<div id="booking-details-modal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Booking Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="booking-details-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-modal-close>Close</button>
        </div>
    </div>
</div>

<script>
// Status filter change handler
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            if (this.value) {
                currentUrl.searchParams.set('status', this.value);
            } else {
                currentUrl.searchParams.delete('status');
            }
            window.location.href = currentUrl.toString();
        });
    }
});

// Update booking status function
function updateBookingStatus(bookingId, status, buttonElement) {
    // Confirm action
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    if (!confirm(`Are you sure you want to ${statusText.toLowerCase()} this booking?`)) {
        return;
    }
    
    // Disable button and show loading
    buttonElement.disabled = true;
    const originalContent = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Send AJAX request
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_booking_status&booking_id=${bookingId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Find the row
            const row = buttonElement.closest('tr');
            
            // Update status badge
            const statusCell = row.querySelector('.booking-status');
            const badge = statusCell.querySelector('.badge');
            badge.className = `badge badge-${getBookingStatusColor(data.new_status)}`;
            badge.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
            
            // Update action buttons
            const actionsCell = row.querySelector('.booking-actions');
            actionsCell.innerHTML = generateBookingActions(bookingId, data.new_status);
            
            // Show success message
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
            // Restore button
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred. Please try again.');
        // Restore button
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalContent;
    });
}

// Generate booking action buttons based on status
function generateBookingActions(bookingId, status) {
    let actions = '<div class="d-flex gap-1">';
    
    // View button (always available)
    actions += `
        <button class="btn btn-sm btn-info view-booking-btn" 
                data-booking-id="${bookingId}"
                data-tooltip="View Details">
            <i class="fas fa-eye"></i>
        </button>
    `;
    
    // Status-specific actions
    switch (status) {
        case 'pending':
            actions += `
                <button class="btn btn-sm btn-success update-booking-status" 
                        data-booking-id="${bookingId}"
                        data-status="confirmed"
                        data-tooltip="Confirm Booking"
                        onclick="updateBookingStatus(${bookingId}, 'confirmed', this)">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-danger update-booking-status" 
                        data-booking-id="${bookingId}"
                        data-status="cancelled"
                        data-tooltip="Cancel Booking"
                        onclick="updateBookingStatus(${bookingId}, 'cancelled', this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            break;
        case 'confirmed':
            actions += `
                <button class="btn btn-sm btn-success update-booking-status" 
                        data-booking-id="${bookingId}"
                        data-status="completed"
                        data-tooltip="Mark as Completed"
                        onclick="updateBookingStatus(${bookingId}, 'completed', this)">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="btn btn-sm btn-danger update-booking-status" 
                        data-booking-id="${bookingId}"
                        data-status="cancelled"
                        data-tooltip="Cancel Booking"
                        onclick="updateBookingStatus(${bookingId}, 'cancelled', this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            break;
        default:
            actions += '<span class="text-muted" style="font-size: 0.75rem;">No actions</span>';
    }
    
    actions += '</div>';
    return actions;
}

// Get status color for badges
function getBookingStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
        ${message}
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    `;

    // Insert after header
    const header = document.querySelector('.header');
    header.insertAdjacentElement('afterend', alert);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Sorting functionality
function sortBookingsTable(sortBy) {
    const table = document.getElementById('bookings-table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aVal, bVal;
        
        switch (sortBy) {
            case 'newest':
                aVal = new Date(a.cells[0].querySelector('small').textContent.replace('Booked: ', ''));
                bVal = new Date(b.cells[0].querySelector('small').textContent.replace('Booked: ', ''));
                return bVal - aVal;
                
            case 'oldest':
                aVal = new Date(a.cells[0].querySelector('small').textContent.replace('Booked: ', ''));
                bVal = new Date(b.cells[0].querySelector('small').textContent.replace('Booked: ', ''));
                return aVal - bVal;
                
            case 'amount_high':
                aVal = parseFloat(a.cells[5].textContent.replace(/[^0-9.]/g, ''));
                bVal = parseFloat(b.cells[5].textContent.replace(/[^0-9.]/g, ''));
                return bVal - aVal;
                
            case 'amount_low':
                aVal = parseFloat(a.cells[5].textContent.replace(/[^0-9.]/g, ''));
                bVal = parseFloat(b.cells[5].textContent.replace(/[^0-9.]/g, ''));
                return aVal - bVal;
                
            default:
                return 0;
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortBookingsTable(this.value);
        });
    }
});
</script>

<?php
function getBookingStatusColor($status) {
    $colors = array(
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    );
    return isset($colors[$status]) ? $colors[$status] : 'secondary';
}

include __DIR__ . '/../layouts/footer.php';
?>