document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');
    initializeDashboard();
});

function initializeDashboard() {
    initTooltips();
    initModals();
    initFormValidations();
    initAjaxHandlers();
    if (typeof Chart !== 'undefined') {
        initCharts();
    }
    if (document.getElementById('availability-calendar')) {
        initAvailabilityCalendar();
    }
    autoHideAlerts();
}

function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const text = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    document.body.appendChild(tooltip);
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function initModals() {
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-target]')) {
            const modalId = e.target.getAttribute('data-modal-target');
            showModal(modalId);
        }
        if (e.target.matches('[data-modal-close]') || e.target.matches('.modal-close')) {
            hideModal();
        }
        if (e.target.matches('.modal')) {
            hideModal();
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideModal();
        }
    });
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function hideModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
    document.body.style.overflow = 'auto';
}

function initFormValidations() {
    const forms = document.querySelectorAll('.validate-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });
    const numberFields = form.querySelectorAll('input[type="number"]');
    numberFields.forEach(field => {
        if (field.value && isNaN(field.value)) {
            showFieldError(field, 'Please enter a valid number');
            isValid = false;
        }
    });
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-danger';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function initAjaxHandlers() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.update-booking-status')) {
            const button = e.target.closest('.update-booking-status');
            const bookingId = button.getAttribute('data-booking-id');
            const status = button.getAttribute('data-status');
            if (bookingId && status) {
                updateBookingStatus(bookingId, status, button);
            }
        }
        if (e.target.matches('.toggle-availability')) {
            toggleCarAvailability(e.target);
        }
        if (e.target.matches('.delete-car-btn')) {
            deleteCar(e.target);
        }
        if (e.target.matches('.view-booking-btn')) {
            viewBookingDetails(e.target);
        }
    });
    document.addEventListener('submit', function(e) {
        if (e.target.matches('.review-response-form')) {
            e.preventDefault();
            submitReviewResponse(e.target);
        }
    });
}

function updateBookingStatus(bookingId, status, buttonElement) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    if (!confirm('Are you sure you want to ' + statusText.toLowerCase() + ' this booking?')) {
        return;
    }
    buttonElement.disabled = true;
    const originalContent = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_booking_status&booking_id=' + bookingId + '&status=' + status
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            const row = buttonElement.closest('tr');
            const statusCell = row.querySelector('.booking-status');
            const badge = statusCell.querySelector('.badge');
            badge.className = 'badge badge-' + getBookingStatusColor(data.new_status);
            badge.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
            const actionsCell = row.querySelector('.booking-actions');
            actionsCell.innerHTML = generateBookingActions(bookingId, data.new_status);
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalContent;
        }
    })
    .catch(function(error) {
        showAlert('error', 'An error occurred. Please try again.');
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalContent;
    });
}

function toggleCarAvailability(button) {
    const carId = button.getAttribute('data-car-id');
    const row = button.closest('tr');
    button.disabled = true;
    button.textContent = 'Loading...';
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=toggle_car_availability&car_id=' + carId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusCell = row.querySelector('.car-status');
            const badge = statusCell.querySelector('.badge');
            if (data.is_available) {
                badge.className = 'badge badge-success';
                badge.textContent = 'Available';
                button.textContent = 'Make Unavailable';
                button.className = 'btn btn-sm btn-warning toggle-availability';
            } else {
                badge.className = 'badge badge-danger';
                badge.textContent = 'Unavailable';
                button.textContent = 'Make Available';
                button.className = 'btn btn-sm btn-success toggle-availability';
            }
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
            button.textContent = data.is_available ? 'Make Unavailable' : 'Make Available';
        }
    })
    .catch(() => showAlert('error', 'An error occurred. Please try again.'))
    .finally(() => button.disabled = false);
}

function deleteCar(button) {
    const carId = button.getAttribute('data-car-id');
    const carName = button.getAttribute('data-car-name');
    if (!confirm('Are you sure you want to delete ' + carName + '? This action cannot be undone.')) {
        return;
    }
    button.disabled = true;
    button.textContent = 'Deleting...';
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete_car&car_id=' + carId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = button.closest('tr');
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
            button.textContent = 'Delete';
        }
    })
    .catch(() => showAlert('error', 'An error occurred. Please try again.'))
    .finally(() => button.disabled = false);
}

function viewBookingDetails(bookingId) {
    fetch('controllers/AjaxController.php?action=get_booking_details&booking_id=' + bookingId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateBookingModal(data.booking);
            showModal('booking-details-modal');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(() => showAlert('error', 'An error occurred while fetching booking details.'));
}

function loadCarDetails(carId) {
    fetch('controllers/AjaxController.php?action=get_car_details&car_id=' + carId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateCarDetailsModal(data.car);
            showModal('car-details-modal');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(() => showAlert('error', 'Failed to load car details'));
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

function populateBookingModal(booking) {
    const modal = document.getElementById('booking-details-modal');
    if (!modal) return;
    const fields = [
        'car', 'license_plate', 'customer_name', 'customer_email', 'customer_phone',
        'start_date', 'end_date', 'total_days', 'daily_rate', 'total_amount',
        'status', 'special_requests', 'booking_date'
    ];
    fields.forEach(function(field) {
        const element = modal.querySelector('[data-booking-' + field + ']');
        if (element) {
            element.textContent = booking[field] || 'N/A';
        }
    });
}

function submitReviewResponse(form) {
    const formData = new FormData(form);
    formData.append('action', 'respond_to_review');
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';
    fetch('controllers/AjaxController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const reviewCard = form.closest('.review-card');
            const responseSection = reviewCard.querySelector('.review-response');
            if (responseSection) {
                responseSection.innerHTML = 
                    '<div class="response-content">' +
                        '<strong>Your Response:</strong>' +
                        '<p>' + data.response + '</p>' +
                        '<small class="text-muted">Responded on ' + data.response_date + '</small>' +
                    '</div>';
            }
            form.style.display = 'none';
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(() => showAlert('error', 'An error occurred. Please try again.'))
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Submit Response';
    });
}

function initCharts() {
    const earningsCanvas = document.getElementById('earnings-chart');
    if (earningsCanvas) initEarningsChart(earningsCanvas);
    const bookingsCanvas = document.getElementById('bookings-chart');
    if (bookingsCanvas) initBookingsChart(bookingsCanvas);
}

function initEarningsChart(canvas) {
    const period = new URLSearchParams(window.location.search).get('period') || 'month';
    fetch('controllers/AjaxController.php?action=get_earnings_chart&period=' + period)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Earnings',
                        data: data.values,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: value => '$' + value.toLocaleString() } } }
                }
            });
        }
    })
    .catch(error => console.error('Error loading earnings chart:', error));
}

function initBookingsChart(canvas) {
    const bookingStats = window.bookingStats || {};
    new Chart(canvas, {
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
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

function initAvailabilityCalendar() {
    const calendar = document.getElementById('availability-calendar');
    const carSelect = document.getElementById('car-select');
