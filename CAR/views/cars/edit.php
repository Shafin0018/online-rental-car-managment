<?php
// views/cars/edit.php - Edit car view
$page_title = "Edit Car";
include __DIR__ . '/../layouts/header.php';

// Get form data if validation failed
$form_data = $_SESSION['form_data'] ?? array();
unset($_SESSION['form_data']);

// Use existing car data if no form data
if (empty($form_data) && isset($car)) {
    $form_data = $car;
}
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Edit Car</h1>
            <a href="index.php?page=cars" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Cars
            </a>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Car Information</h3>
            </div>
            <div class="card-body">
                <form action="index.php?page=edit_car" method="POST" enctype="multipart/form-data" class="validate-form">
                    <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($car['id'] ?? ''); ?>">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <!-- Left Column -->
                        <div>
                            <div class="form-group">
                                <label class="form-label" for="make">Make *</label>
                                <input type="text" id="make" name="make" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['make'] ?? ''); ?>" 
                                       required placeholder="e.g., Toyota, Honda, BMW">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="model">Model *</label>
                                <input type="text" id="model" name="model" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['model'] ?? ''); ?>" 
                                       required placeholder="e.g., Camry, Accord, X5">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="year">Year *</label>
                                <input type="number" id="year" name="year" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['year'] ?? ''); ?>" 
                                       required min="1900" max="<?php echo date('Y') + 1; ?>" 
                                       placeholder="<?php echo date('Y'); ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="color">Color</label>
                                <input type="text" id="color" name="color" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['color'] ?? ''); ?>" 
                                       placeholder="e.g., White, Black, Silver">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="license_plate">License Plate *</label>
                                <input type="text" id="license_plate" name="license_plate" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['license_plate'] ?? ''); ?>" 
                                       required style="text-transform: uppercase;" 
                                       placeholder="ABC-1234">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="daily_rate">Daily Rate ($) *</label>
                                <input type="number" id="daily_rate" name="daily_rate" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['daily_rate'] ?? ''); ?>" 
                                       required min="1" step="0.01" placeholder="50.00">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div>
                            <div class="form-group">
                                <label class="form-label" for="location">Location *</label>
                                <input type="text" id="location" name="location" class="form-control" 
                                       value="<?php echo htmlspecialchars($form_data['location'] ?? ''); ?>" 
                                       required placeholder="City, State or Address">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="fuel_type">Fuel Type</label>
                                <select id="fuel_type" name="fuel_type" class="form-control form-select">
                                    <option value="petrol" <?php echo (isset($form_data['fuel_type']) && $form_data['fuel_type'] === 'petrol') ? 'selected' : ''; ?>>Petrol/Gasoline</option>
                                    <option value="diesel" <?php echo (isset($form_data['fuel_type']) && $form_data['fuel_type'] === 'diesel') ? 'selected' : ''; ?>>Diesel</option>
                                    <option value="electric" <?php echo (isset($form_data['fuel_type']) && $form_data['fuel_type'] === 'electric') ? 'selected' : ''; ?>>Electric</option>
                                    <option value="hybrid" <?php echo (isset($form_data['fuel_type']) && $form_data['fuel_type'] === 'hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="transmission">Transmission</label>
                                <select id="transmission" name="transmission" class="form-control form-select">
                                    <option value="manual" <?php echo (isset($form_data['transmission']) && $form_data['transmission'] === 'manual') ? 'selected' : ''; ?>>Manual</option>
                                    <option value="automatic" <?php echo (isset($form_data['transmission']) && $form_data['transmission'] === 'automatic') ? 'selected' : ''; ?>>Automatic</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="seats">Number of Seats</label>
                                <select id="seats" name="seats" class="form-control form-select">
                                    <?php for ($i = 2; $i <= 8; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($form_data['seats']) && $form_data['seats'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> seats
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="car_image">Car Image</label>
                                
                                <!-- Current Image Display -->
                                <?php if (!empty($form_data['car_image'])): ?>
                                    <div class="current-image mb-3">
                                        <label class="form-label" style="font-size: 0.875rem; color: var(--text-muted);">Current Image:</label>
                                        <div style="position: relative; display: inline-block;">
                                            <img src="assets/uploads/cars/<?php echo htmlspecialchars($form_data['car_image']); ?>" 
                                                 alt="Current car image" 
                                                 style="max-width: 200px; height: auto; border-radius: 0.5rem; border: 2px solid var(--border-color);">
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    style="position: absolute; top: 5px; right: 5px; padding: 0.25rem 0.5rem;"
                                                    onclick="removeCurrentImage()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" id="car_image" name="car_image" class="form-control" 
                                       accept="image/*" data-preview="image-preview">
                                <small class="text-muted">Upload new image to replace current one. Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                                <img id="image-preview" style="display: none; margin-top: 1rem; max-width: 200px; height: auto; border-radius: 0.5rem; border: 2px solid var(--primary-color);">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control textarea" 
                                  rows="4" placeholder="Describe your car's features, condition, and any special notes..."><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-end gap-3">
                        <a href="index.php?page=cars" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Car
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Car Information Summary -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Car Information Summary
                </h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-calendar-alt"></i> Car History
                        </h4>
                        <ul class="text-muted" style="margin: 0; padding-left: 1.2rem;">
                            <li>Added: <?php echo formatDate($car['created_at'] ?? date('Y-m-d')); ?></li>
                            <?php if (isset($car['updated_at']) && $car['updated_at'] != $car['created_at']): ?>
                                <li>Last updated: <?php echo formatDate($car['updated_at']); ?></li>
                            <?php endif; ?>
                            <li>Status: 
                                <span class="badge badge-<?php echo (isset($car['is_available']) && $car['is_available']) ? 'success' : 'danger'; ?>">
                                    <?php echo (isset($car['is_available']) && $car['is_available']) ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 style="color: var(--success-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-chart-line"></i> Performance Tips
                        </h4>
                        <ul class="text-muted" style="margin: 0; padding-left: 1.2rem;">
                            <li>Keep your car information up to date</li>
                            <li>Add high-quality photos to attract customers</li>
                            <li>Set competitive pricing for your area</li>
                            <li>Respond to customer reviews promptly</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 style="color: var(--info-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-shield-alt"></i> Safety Reminders
                        </h4>
                        <ul class="text-muted" style="margin: 0; padding-left: 1.2rem;">
                            <li>Verify customer licenses before handover</li>
                            <li>Document car condition before/after rental</li>
                            <li>Keep insurance information updated</li>
                            <li>Set clear rental terms and conditions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Form validation and enhancements
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.validate-form');
    const licensePlateInput = document.getElementById('license_plate');
    
    // Auto-uppercase license plate
    if (licensePlateInput) {
        licensePlateInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Real-time validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
    
    // Year validation
    const yearField = document.getElementById('year');
    if (yearField) {
        yearField.addEventListener('input', function() {
            const year = parseInt(this.value);
            const currentYear = new Date().getFullYear();
            
            if (year < 1900) {
                showFieldError(this, 'Year must be 1900 or later');
            } else if (year > currentYear + 1) {
                showFieldError(this, `Year cannot be more than ${currentYear + 1}`);
            } else {
                clearFieldError(this);
            }
        });
    }
    
    // Daily rate validation
    const rateField = document.getElementById('daily_rate');
    if (rateField) {
        rateField.addEventListener('input', function() {
            const rate = parseFloat(this.value);
            
            if (rate <= 0) {
                showFieldError(this, 'Daily rate must be greater than 0');
            } else if (rate > 10000) {
                showFieldError(this, 'Daily rate seems too high. Please check the amount.');
            } else {
                clearFieldError(this);
            }
        });
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        
        // Validate all required fields
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                hasErrors = true;
            }
        });
        
        // Validate file size if new image is uploaded
        const fileInput = document.getElementById('car_image');
        if (fileInput && fileInput.files[0]) {
            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (file.size > maxSize) {
                showFieldError(fileInput, 'File size must be less than 5MB');
                hasErrors = true;
            }
        }
        
        if (hasErrors) {
            e.preventDefault();
            showAlert('error', 'Please fix the errors in the form before submitting.');
        } else {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Car...';
        }
    });
});

function validateField(field) {
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'number' && value) {
        const num = parseFloat(value);
        const min = parseFloat(field.min);
        const max = parseFloat(field.max);
        
        if (min && num < min) {
            showFieldError(field, `Value must be at least ${min}`);
            return false;
        }
        
        if (max && num > max) {
            showFieldError(field, `Value must be at most ${max}`);
            return false;
        }
    }
    
    clearFieldError(field);
    return true;
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

function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        const currentImageDiv = document.querySelector('.current-image');
        if (currentImageDiv) {
            currentImageDiv.style.display = 'none';
            // You might want to add a hidden input to indicate image removal
            const removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.name = 'remove_current_image';
            removeImageInput.value = '1';
            document.querySelector('form').appendChild(removeImageInput);
        }
    }
}

// Image preview functionality
document.getElementById('car_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            
            // Hide current image when new one is selected
            const currentImage = document.querySelector('.current-image');
            if (currentImage) {
                currentImage.style.opacity = '0.5';
            }
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        
        // Show current image again
        const currentImage = document.querySelector('.current-image');
        if (currentImage) {
            currentImage.style.opacity = '1';
        }
    }
});

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

// Handle form errors on page load
window.addEventListener('pageshow', function() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Car';
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>