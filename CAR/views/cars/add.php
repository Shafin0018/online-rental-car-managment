<?php
// views/cars/add.php - Add new car view
$page_title = "Add New Car";
include __DIR__ . '/../layouts/header.php';

// Get form data if validation failed
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Add New Car</h1>
            <a href="index.php?page=cars" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Cars
            </a>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Car Information</h3>
            </div>
            <div class="card-body">
                <form action="index.php?page=add_car" method="POST" enctype="multipart/form-data" class="validate-form">
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
                                    <option value="petrol" <?php echo ($form_data['fuel_type'] ?? '') === 'petrol' ? 'selected' : ''; ?>>Petrol/Gasoline</option>
                                    <option value="diesel" <?php echo ($form_data['fuel_type'] ?? '') === 'diesel' ? 'selected' : ''; ?>>Diesel</option>
                                    <option value="electric" <?php echo ($form_data['fuel_type'] ?? '') === 'electric' ? 'selected' : ''; ?>>Electric</option>
                                    <option value="hybrid" <?php echo ($form_data['fuel_type'] ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="transmission">Transmission</label>
                                <select id="transmission" name="transmission" class="form-control form-select">
                                    <option value="manual" <?php echo ($form_data['transmission'] ?? '') === 'manual' ? 'selected' : ''; ?>>Manual</option>
                                    <option value="automatic" <?php echo ($form_data['transmission'] ?? '') === 'automatic' ? 'selected' : ''; ?>>Automatic</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="seats">Number of Seats</label>
                                <select id="seats" name="seats" class="form-control form-select">
                                    <?php for ($i = 2; $i <= 8; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($form_data['seats'] ?? 5) == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> seats
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="car_image">Car Image</label>
                                <input type="file" id="car_image" name="car_image" class="form-control" 
                                       accept="image/*" data-preview="image-preview">
                                <small class="text-muted">Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                                <img id="image-preview" style="display: none; margin-top: 1rem; max-width: 200px; height: auto; border-radius: 0.5rem;">
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
                            <i class="fas fa-save"></i> Add Car
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i> Tips for Adding Your Car
                </h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-camera"></i> Great Photos
                        </h4>
                        <p class="text-muted">Upload clear, well-lit photos of your car from multiple angles. Good photos can increase bookings by up to 40%.</p>
                    </div>
                    
                    <div>
                        <h4 style="color: var(--success-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-dollar-sign"></i> Competitive Pricing
                        </h4>
                        <p class="text-muted">Research similar cars in your area to set competitive daily rates. Consider your car's age, condition, and features.</p>
                    </div>
                    
                    <div>
                        <h4 style="color: var(--info-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-edit"></i> Detailed Description
                        </h4>
                        <p class="text-muted">Include important details like fuel efficiency, special features, pickup instructions, and house rules.</p>
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
        
        // Validate file size
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Car...';
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

// Auto-save draft functionality (optional)
function saveFormDraft() {
    const formData = new FormData(document.querySelector('.validate-form'));
    const draftData = {};
    
    for (let [key, value] of formData.entries()) {
        if (key !== 'car_image') { // Don't save file inputs
            draftData[key] = value;
        }
    }
    
    localStorage.setItem('car_form_draft', JSON.stringify(draftData));
}

function loadFormDraft() {
    try {
        const draftData = JSON.parse(localStorage.getItem('car_form_draft') || '{}');
        
        Object.keys(draftData).forEach(key => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field && !field.value) {
                field.value = draftData[key];
            }
        });
    } catch (e) {
        console.log('No valid draft found');
    }
}

// Load draft on page load (if no server-side form data)
document.addEventListener('DOMContentLoaded', function() {
    const hasServerData = <?php echo !empty($form_data) ? 'true' : 'false'; ?>;
    if (!hasServerData) {
        loadFormDraft();
    }
    
    // Save draft every 30 seconds
    setInterval(saveFormDraft, 30000);
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>