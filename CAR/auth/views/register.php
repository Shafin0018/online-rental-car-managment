<?php
$pageTitle = "Register - CarRental Pro";
$formData = $_SESSION['register_form_data'] ?? [];
unset($_SESSION['register_form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="description" content="Create your CarRental Pro account">
    <meta name="keywords" content="car rental, register, sign up, create account">
</head>
<body>
    <div class="auth-container fade-in">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join CarRental Pro and start your journey</p>
            </div>

            <?php 
            $flashMessages = getAllFlashMessages();
            foreach ($flashMessages as $type => $message): 
            ?>
                <div class="alert alert-<?php echo $type; ?>">
                    <i class="fas fa-<?php echo $type === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endforeach; ?>

            <form action="register.php" method="POST" class="auth-form" id="register-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label">I want to:</label>
                    <div class="role-selection">
                        <div class="role-option" data-role="customer">
                            <input type="radio" name="role" value="customer" id="role-customer" 
                                   <?php echo ($formData['role'] ?? 'customer') === 'customer' ? 'checked' : ''; ?>>
                            <div class="role-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="role-title">Rent Cars</div>
                            <div class="role-description">Browse and book rental cars</div>
                        </div>
                        <div class="role-option" data-role="car_owner">
                            <input type="radio" name="role" value="car_owner" id="role-owner" 
                                   <?php echo ($formData['role'] ?? '') === 'car_owner' ? 'checked' : ''; ?>>
                            <div class="role-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="role-title">Rent Out Cars</div>
                            <div class="role-description">List your cars for rent</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Full Name *</label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input 
                                type="text" 
                                id="full_name" 
                                name="full_name" 
                                class="form-control" 
                                value="<?php echo htmlspecialchars($formData['full_name'] ?? ''); ?>"
                                required 
                                autocomplete="name"
                                placeholder="John Doe"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <div class="input-group">
                            <i class="fas fa-phone input-icon"></i>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                class="form-control" 
                                value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                                autocomplete="tel"
                                placeholder="+1 (555) 123-4567"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username *</label>
                    <div class="input-group">
                        <i class="fas fa-at input-icon"></i>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                            required 
                            autocomplete="username"
                            placeholder="johndoe123"
                            pattern="[a-zA-Z0-9_]+"
                            title="Username can only contain letters, numbers, and underscores"
                        >
                        <div class="username-feedback"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                            required 
                            autocomplete="email"
                            placeholder="john@example.com"
                        >
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="password">Password *</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                required 
                                autocomplete="new-password"
                                placeholder="Enter password"
                                minlength="8"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password *</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                class="form-control" 
                                required 
                                autocomplete="new-password"
                                placeholder="Confirm password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="confirm_password-toggle-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="password-requirements" id="password-requirements" style="font-size: 0.75rem; color: #6b7280; margin-bottom: 1rem;">
                    <p>Password must contain:</p>
                    <ul style="margin: 0.5rem 0 0 1rem; padding: 0;">
                        <li id="req-length">At least 8 characters</li>
                        <li id="req-upper">One uppercase letter</li>
                        <li id="req-lower">One lowercase letter</li>
                        <li id="req-number">One number</li>
                    </ul>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" class="checkbox-input" required>
                    <label for="terms" class="checkbox-label">
                        I agree to the <a href="#" target="_blank">Terms of Service</a> and 
                        <a href="#" target="_blank">Privacy Policy</a> *
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" id="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const roleOptions = document.querySelectorAll('.role-option');
            const roleInputs = document.querySelectorAll('input[name="role"]');

            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const role = this.getAttribute('data-role');
                    const radio = document.getElementById('role-' + (role === 'car_owner' ? 'owner' : 'customer'));
                    roleOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    radio.checked = true;
                });
            });

            const checkedInput = document.querySelector('input[name="role"]:checked');
            if (checkedInput) {
                const role = checkedInput.value;
                const option = document.querySelector(`[data-role="${role}"]`);
                if (option) option.classList.add('selected');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            validatePasswordMatch();
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            validatePasswordMatch();
        });

        function checkPasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /\d/.test(password)
            };
            Object.keys(requirements).forEach(req => {
                const element = document.getElementById('req-' + req);
                if (element) {
                    element.style.color = requirements[req] ? '#10b981' : '#6b7280';
                    element.innerHTML = (requirements[req] ? '✓ ' : '• ') + element.innerHTML.replace(/^[✓•]\s/, '');
                }
            });
            const score = Object.values(requirements).filter(Boolean).length;
            const strengthElement = document.getElementById('password-strength');
            const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const strengthColors = ['#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];

            if (password.length > 0) {
                strengthElement.innerHTML = `
                    <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                        <span style="color: ${strengthColors[score - 1] || '#ef4444'};">
                            ${strengthTexts[score - 1] || 'Very Weak'}
                        </span>
                        <div style="width: 100%; height: 2px; background: #e5e7eb; border-radius: 1px; margin-top: 2px;">
                            <div style="width: ${score * 25}%; height: 100%; background: ${strengthColors[score - 1] || '#ef4444'}; border-radius: 1px; transition: all 0.3s;"></div>
                        </div>
                    </div>
                `;
            } else {
                strengthElement.innerHTML = '';
            }
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmField = document.getElementById('confirm_password');

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    confirmField.classList.remove('error');
                    confirmField.style.borderColor = '#10b981';
                } else {
                    confirmField.classList.add('error');
                }
            } else {
                confirmField.classList.remove('error');
                confirmField.style.borderColor = '';
            }
        }

        let usernameTimeout;
        document.getElementById('username').addEventListener('input', function() {
            clearTimeout(usernameTimeout);
            const username = this.value.trim();
            const feedback = document.querySelector('.username-feedback');
            
            if (username.length >= 3) {
                usernameTimeout = setTimeout(() => {
                    const unavailableUsernames = ['admin', 'test', 'user', 'john', 'jane'];
                    const isAvailable = !unavailableUsernames.includes(username.toLowerCase());
                    
                    feedback.innerHTML = `
                        <div style="font-size: 0.75rem; margin-top: 0.25rem; color: ${isAvailable ? '#10b981' : '#ef4444'};">
                            <i class="fas fa-${isAvailable ? 'check' : 'times'}"></i>
                            ${isAvailable ? 'Username is available' : 'Username is taken'}
                        </div>
                    `;
                    
                    this.style.borderColor = isAvailable ? '#10b981' : '#ef4444';
                }, 500);
            } else {
                feedback.innerHTML = '';
                this.style.borderColor = '';
            }
        });

        document.getElementById('register-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('register-btn');
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;

            if (password !== confirmPassword) {
                e.preventDefault();
                showAlert('error', 'Passwords do not match.');
                return;
            }

            if (!terms) {
                e.preventDefault();
                showAlert('error', 'Please agree to the Terms of Service and Privacy Policy.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Creating Account...';
        });

        function showAlert(type, message) {
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                ${message}
            `;

            const header = document.querySelector('.auth-header');
            header.insertAdjacentElement('afterend', alert);

            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        document.querySelectorAll('.form-control').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });

        window.addEventListener('pageshow', function() {
            const submitBtn = document.getElementById('register-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
        });
    </script>
</body>
</html>
