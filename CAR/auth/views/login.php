<?php
$pageTitle = "Login - CarRental Pro";
$formData = $_SESSION['login_form_data'] ?? [];
unset($_SESSION['login_form_data']);
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
    <meta name="description" content="Login to your CarRental Pro account">
    <meta name="keywords" content="car rental, login, authentication">
</head>
<body>
    <div class="auth-container fade-in">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-car"></i>
                </div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to your CarRental Pro account</p>
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

            <form action="login.php" method="POST" class="auth-form" id="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="identifier">Username or Email</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            id="identifier" 
                            name="identifier" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($formData['identifier'] ?? ''); ?>"
                            required 
                            autocomplete="username"
                            placeholder="Enter your username or email"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            required 
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="remember_me" 
                        name="remember_me" 
                        class="checkbox-input"
                        <?php echo !empty($formData['remember_me']) ? 'checked' : ''; ?>
                    >
                    <label for="remember_me" class="checkbox-label">Remember me for 30 days</label>
                </div>

                <button type="submit" class="btn btn-primary" id="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="auth-links">
                <p><a href="forgot-password.php">Forgot your password?</a></p>
                
                <div class="auth-divider">
                    <span>New to CarRental Pro?</span>
                </div>
                
                <p><a href="register.php">Create an account</a></p>
            </div>
        </div>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; background: rgba(255,255,255,0.95); padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 300px; font-size: 0.8rem;">
        <h4 style="margin: 0 0 10px 0; color: #4f46e5;">Demo Accounts</h4>
        <div style="margin-bottom: 8px;">
            <strong>Car Owner:</strong><br>
            Email: john@example.com<br>
            Password: password123
        </div>
        <div style="margin-bottom: 8px;">
            <strong>Customer:</strong><br>
            Email: mike@example.com<br>
            Password: password123
        </div>
        <div>
            <strong>Admin:</strong><br>
            Email: admin@carrental.com<br>
            Password: password123
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

        document.getElementById('login-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('login-btn');
            const identifier = document.getElementById('identifier').value.trim();
            const password = document.getElementById('password').value;

            if (!identifier || !password) {
                e.preventDefault();
                showAlert('error', 'Please fill in all required fields.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Signing In...';
        });

        function fillDemoAccount(email) {
            document.getElementById('identifier').value = email;
            document.getElementById('password').value = 'password123';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const demoInfo = document.querySelector('[style*="position: fixed"]');
            if (demoInfo) {
                const emails = ['john@example.com', 'mike@example.com', 'admin@carrental.com'];
                const divs = demoInfo.querySelectorAll('div');
                
                divs.forEach((div, index) => {
                    if (emails[index]) {
                        div.style.cursor = 'pointer';
                        div.style.padding = '5px';
                        div.style.borderRadius = '4px';
                        div.style.transition = 'background-color 0.2s';
                        
                        div.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
                        });
                        
                        div.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = 'transparent';
                        });
                        
                        div.addEventListener('click', function() {
                            fillDemoAccount(emails[index]);
                        });
                    }
                });
            }
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

        document.getElementById('identifier').addEventListener('input', function() {
            this.classList.remove('error');
        });

        document.getElementById('password').addEventListener('input', function() {
            this.classList.remove('error');
        });

        window.addEventListener('pageshow', function() {
            const submitBtn = document.getElementById('login-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
        });

        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key >= '1' && e.key <= '3') {
                const emails = ['john@example.com', 'mike@example.com', 'admin@carrental.com'];
                const index = parseInt(e.key) - 1;
                if (emails[index]) {
                    fillDemoAccount(emails[index]);
                    e.preventDefault();
                }
            }
        });

        document.getElementById('password').addEventListener('keypress', function(e) {
            const capsLockOn = e.getModifierState && e.getModifierState('CapsLock');
            const warning = document.getElementById('caps-warning');
            
            if (capsLockOn && !warning) {
                const warningDiv = document.createElement('div');
                warningDiv.id = 'caps-warning';
                warningDiv.style.cssText = `
                    color: #f59e0b;
                    font-size: 0.75rem;
                    margin-top: 0.25rem;
                    display: flex;
                    align-items: center;
                    gap: 0.25rem;
                `;
                warningDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Caps Lock is on';
                this.parentNode.parentNode.appendChild(warningDiv);
            } else if (!capsLockOn && warning) {
                warning.remove();
            }
        });
    </script>
</body>
</html>
