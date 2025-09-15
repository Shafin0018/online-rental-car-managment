<?php
// views/cars/index.php - Cars management view
$page_title = "My Cars";
include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">My Cars</h1>
            <div class="d-flex gap-2">
                <a href="index.php?page=add_car" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Car
                </a>
                <button class="btn btn-outline export-csv" data-table="cars-table" data-filename="my-cars.csv">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 200px 200px; gap: 1rem; align-items: end;">
                    <div class="form-group mb-0">
                        <label class="form-label">Search Cars</label>
                        <input type="text" class="form-control search-input" 
                               data-table="cars-table" 
                               placeholder="Search by make, model, license plate...">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Filter by Status</label>
                        <select class="form-control filter-select" data-table="cars-table" data-column="4">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Sort by</label>
                        <select class="form-control" id="sort-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="make">Make A-Z</option>
                            <option value="price_high">Price High-Low</option>
                            <option value="price_low">Price Low-High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cars List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Cars (<?php echo count($cars); ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($cars)): ?>
                    <div class="text-center" style="padding: 3rem;">
                        <i class="fas fa-car" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--text-muted); margin-bottom: 1rem;">No cars added yet</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">Start by adding your first car to begin earning from rentals.</p>
                        <a href="index.php?page=add_car" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Add Your First Car
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table" id="cars-table">
                            <thead>
                                <tr>
                                    <th>Car Details</th>
                                    <th>License Plate</th>
                                    <th>Daily Rate</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cars as $car): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex items-center gap-3">
                                                <?php if ($car['car_image']): ?>
                                                    <img src="../assets/uploads/cars/<?php echo htmlspecialchars($car['car_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>"
                                                         style="width: 60px; height: 45px; object-fit: cover; border-radius: 0.375rem;">
                                                <?php else: ?>
                                                    <div style="width: 60px; height: 45px; background: var(--secondary-color); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-car text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($car['year']); ?>
                                                        <?php if ($car['color']): ?>
                                                            • <?php echo htmlspecialchars($car['color']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($car['license_plate']); ?></strong>
                                        </td>
                                        <td>
                                            <strong><?php echo formatCurrency($car['daily_rate']); ?></strong>
                                            <small class="text-muted">/day</small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($car['location']); ?>
                                        </td>
                                        <td class="car-status">
                                            <span class="badge badge-<?php echo $car['is_available'] ? 'success' : 'danger'; ?>">
                                                <?php echo $car['is_available'] ? 'Available' : 'Unavailable'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo formatDate($car['created_at']); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="index.php?page=edit_car&id=<?php echo $car['id']; ?>" 
                                                   class="btn btn-sm btn-outline" 
                                                   data-tooltip="Edit Car">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button class="btn btn-sm btn-<?php echo $car['is_available'] ? 'warning' : 'success'; ?> toggle-availability" 
                                                        data-car-id="<?php echo $car['id']; ?>"
                                                        data-tooltip="<?php echo $car['is_available'] ? 'Make Unavailable' : 'Make Available'; ?>">
                                                    <i class="fas fa-<?php echo $car['is_available'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-info view-car-details" 
                                                        data-car-id="<?php echo $car['id']; ?>"
                                                        data-tooltip="View Details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-danger delete-car-btn" 
                                                        data-car-id="<?php echo $car['id']; ?>"
                                                        data-car-name="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>"
                                                        data-tooltip="Delete Car">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
        <?php if (!empty($cars)): ?>
            <div class="stats-grid mt-4">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Total Cars</h3>
                        <div class="stat-icon primary">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo count($cars); ?></div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Available Cars</h3>
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php echo count(array_filter($cars, function($car) { return $car['is_available']; })); ?>
                    </div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-header">
                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Average Rate</h3>
                        <div class="stat-icon warning">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php 
                        $totalRate = array_sum(array_column($cars, 'daily_rate'));
                        $avgRate = count($cars) > 0 ? $totalRate / count($cars) : 0;
                        echo formatCurrency($avgRate); 
                        ?>
                    </div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-header">
                        <h3 class="stat-title">Highest Rate</h3>
                        <div class="stat-icon info">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php 
                        $maxRate = count($cars) > 0 ? max(array_column($cars, 'daily_rate')) : 0;
                        echo formatCurrency($maxRate); 
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Car Details Modal -->
<div id="car-details-modal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Car Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="car-details-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-modal-close>Close</button>
        </div>
    </div>
</div>

<script>
// Handle view car details
document.addEventListener('click', function(e) {
    if (e.target.closest('.view-car-details')) {
        const button = e.target.closest('.view-car-details');
        const carId = button.getAttribute('data-car-id');
        loadCarDetails(carId);
    }
});

function loadCarDetails(carId) {
    fetch(`controllers/AjaxController.php?action=get_car_details&car_id=${carId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateCarDetailsModal(data.car);
            showModal('car-details-modal');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to load car details');
    });
}

function loadCarDetails(carId) {
    fetch('controllers/AjaxController.php?action=get_car_details&car_id=' + carId)
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            populateCarDetailsModal(data.car);
            showModal('car-details-modal');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to load car details');
    });
}

function populateCarDetailsModal(car) {
    const content = document.getElementById('car-details-content');
    const imageHtml = car.car_image ? 
        '<img src="assets/uploads/cars/' + car.car_image + '" alt="' + car.make + ' ' + car.model + '" style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1rem;">' :
        '<div style="width: 100%; height: 200px; background: var(--secondary-color); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;"><i class="fas fa-car" style="font-size: 3rem; color: var(--text-muted);"></i></div>';
    
    content.innerHTML = 
        '<div>' + imageHtml + '</div>' +
        '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">' +
            '<div><strong>Make:</strong> ' + car.make + '</div>' +
            '<div><strong>Model:</strong> ' + car.model + '</div>' +
            '<div><strong>Year:</strong> ' + car.year + '</div>' +
            '<div><strong>Color:</strong> ' + (car.color || 'Not specified') + '</div>' +
            '<div><strong>License Plate:</strong> ' + car.license_plate + '</div>' +
            '<div><strong>Daily Rate:</strong> ' + car.daily_rate + '</div>' +
            '<div><strong>Location:</strong> ' + car.location + '</div>' +
            '<div><strong>Fuel Type:</strong> ' + car.fuel_type + '</div>' +
            '<div><strong>Transmission:</strong> ' + car.transmission + '</div>' +
            '<div><strong>Seats:</strong> ' + car.seats + '</div>' +
            '<div><strong>Status:</strong> <span class="badge badge-' + (car.is_available ? 'success' : 'danger') + '">' + (car.is_available ? 'Available' : 'Unavailable') + '</span></div>' +
            '<div><strong>Added:</strong> ' + car.created_at + '</div>' +
        '</div>' +
        
        '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: var(--secondary-color); border-radius: 0.5rem;">' +
            '<div class="text-center">' +
                '<div style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color);">' + car.total_bookings + '</div>' +
                '<div style="font-size: 0.875rem; color: var(--text-muted);">Total Bookings</div>' +
            '</div>' +
            '<div class="text-center">' +
                '<div style="font-size: 1.5rem; font-weight: bold; color: var(--success-color);">' + car.total_earnings + '</div>' +
                '<div style="font-size: 0.875rem; color: var(--text-muted);">Total Earnings</div>' +
            '</div>' +
            '<div class="text-center">' +
                '<div style="font-size: 1.5rem; font-weight: bold; color: var(--warning-color);">' + car.average_rating + '</div>' +
                '<div style="font-size: 0.875rem; color: var(--text-muted);">Avg Rating (' + car.total_reviews + ' reviews)</div>' +
            '</div>' +
        '</div>' +
        
        (car.description ? 
            '<div style="margin-bottom: 1rem;">' +
                '<strong>Description:</strong>' +
                '<p style="margin-top: 0.5rem; color: var(--text-muted); line-height: 1.6;">' + car.description + '</p>' +
            '</div>' : '');
}

// Sorting functionality
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortTable(this.value);
        });
    }
});

function sortTable(sortBy) {
    const table = document.getElementById('cars-table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aVal, bVal;
        
        switch (sortBy) {
            case 'newest':
                aVal = new Date(a.cells[5].textContent);
                bVal = new Date(b.cells[5].textContent);
                return bVal - aVal;
                
            case 'oldest':
                aVal = new Date(a.cells[5].textContent);
                bVal = new Date(b.cells[5].textContent);
                return aVal - bVal;
                
            case 'make':
                aVal = a.cells[0].textContent.trim().toLowerCase();
                bVal = b.cells[0].textContent.trim().toLowerCase();
                return aVal.localeCompare(bVal);
                
            case 'price_high':
                aVal = parseFloat(a.cells[2].textContent.replace(/[^0-9.]/g, ''));
                bVal = parseFloat(b.cells[2].textContent.replace(/[^0-9.]/g, ''));
                return bVal - aVal;
                
            case 'price_low':
                aVal = parseFloat(a.cells[2].textContent.replace(/[^0-9.]/g, ''));
                bVal = parseFloat(b.cells[2].textContent.replace(/[^0-9.]/g, ''));
                return aVal - bVal;
                
            default:
                return 0;
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

// Format currency for JavaScript
function formatCurrency(amount) {
    return ' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Format date for JavaScript
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Batch actions (if needed)
function selectAllCars() {
    const checkboxes = document.querySelectorAll('.car-checkbox');
    const selectAll = document.getElementById('select-all-cars');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    updateBatchActions();
}

function updateBatchActions() {
    const checkedBoxes = document.querySelectorAll('.car-checkbox:checked');
    const batchActions = document.getElementById('batch-actions');
    if (batchActions) {
        batchActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
}

// Print functionality
function printCarsTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('cars-table').cloneNode(true);
    
    // Remove action columns
    const actionHeaders = table.querySelectorAll('th:last-child');
    const actionCells = table.querySelectorAll('td:last-child');
    actionHeaders.forEach(header => header.remove());
    actionCells.forEach(cell => cell.remove());
    
    printWindow.document.write(`
        <html>
        <head>
            <title>My Cars - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .badge { padding: 2px 6px; border-radius: 3px; font-size: 0.8em; }
                .badge-success { background-color: #d4edda; color: #155724; }
                .badge-danger { background-color: #f8d7da; color: #721c24; }
                @media print {
                    body { margin: 0; }
                    table { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <h1>My Cars</h1>
            <p>Generated on: ${new Date().toLocaleDateString()}</p>
            ${table.outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>