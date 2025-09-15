<?php
// views/layouts/sidebar.php - Navigation sidebar
$current_page = $_GET['page'] ?? 'dashboard';
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <h2><i class="fas fa-car"></i> CarRental Pro</h2>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="index.php?page=dashboard" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=cars" class="nav-link <?php echo in_array($current_page, ['cars', 'add_car', 'edit_car']) ? 'active' : ''; ?>">
                <i class="fas fa-car"></i>
                My Cars
                <?php
                // Get pending items count for badge
                $carModel = new Car();
                $carStats = $carModel->getCarStats($_SESSION['user_id']);
                $totalCars = $carStats['total_cars'] ?? 0;
                if ($totalCars > 0):
                ?>
                <span class="badge badge-secondary" style="margin-left: auto; font-size: 0.75rem;">
                    <?php echo $totalCars; ?>
                </span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=bookings" class="nav-link <?php echo in_array($current_page, ['bookings', 'view_booking']) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                Bookings
                <?php
                // Get pending bookings count
                $bookingModel = new Booking();
                $bookingStats = $bookingModel->getBookingStats($_SESSION['user_id']);
                $pendingBookings = $bookingStats['pending_bookings'] ?? 0;
                if ($pendingBookings > 0):
                ?>
                <span class="badge badge-warning" style="margin-left: auto; font-size: 0.75rem;">
                    <?php echo $pendingBookings; ?>
                </span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=earnings" class="nav-link <?php echo $current_page === 'earnings' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                Earnings
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=reviews" class="nav-link <?php echo $current_page === 'reviews' ? 'active' : ''; ?>">
                <i class="fas fa-star"></i>
                Reviews
                <?php
                // Get reviews needing response count
                $reviewModel = new Review();
                $pendingReviews = $reviewModel->getReviewsNeedingResponse($_SESSION['user_id'], 999);
                $pendingCount = count($pendingReviews);
                if ($pendingCount > 0):
                ?>
                <span class="badge badge-info" style="margin-left: auto; font-size: 0.75rem;">
                    <?php echo $pendingCount; ?>
                </span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=availability" class="nav-link <?php echo $current_page === 'availability' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                Availability
            </a>
        </div>
        
        <div class="nav-item" style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <a href="#" class="nav-link" onclick="showProfileModal()">
                <i class="fas fa-user-cog"></i>
                Profile Settings
            </a>
        </div>
        
        <div class="nav-item">
            <a href="index.php?page=logout" class="nav-link" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </nav>
    
    <!-- User Info at Bottom -->
    <div style="position: absolute; bottom: 1rem; left: 1rem; right: 1rem; padding: 1rem; background: var(--secondary-color); border-radius: 0.5rem;">
        <div class="d-flex items-center gap-2">
            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.875rem;">
                <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div>
                <div style="font-weight: 500; font-size: 0.875rem; color: var(--dark-color);">
                    <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">
                    Car Owner
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile menu button -->
<button id="mobile-menu-btn" class="btn btn-primary" style="display: none; position: fixed; top: 1rem; left: 1rem; z-index: 1000;">
    <i class="fas fa-bars"></i>
</button>

<!-- Profile Settings Modal -->
<div id="profile-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Profile Settings</h3>
            <button class="modal-close" onclick="hideModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="profile-form" class="validate-form">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" 
                           value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" 
                           value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" name="new_password" 
                           placeholder="Enter new password">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password" 
                           placeholder="Confirm new password">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="hideModal()">Cancel</button>
            <button type="submit" form="profile-form" class="btn btn-primary">Update Profile</button>
        </div>
    </div>
</div>

<script>
function showProfileModal() {
    showModal('profile-modal');
}

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    // Show mobile menu button on small screens
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            mobileMenuBtn.style.display = 'block';
            sidebar.classList.add('mobile-hidden');
        } else {
            mobileMenuBtn.style.display = 'none';
            sidebar.classList.remove('mobile-hidden', 'open');
        }
    }
    
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    
    // Mobile menu toggle
    mobileMenuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !mobileMenuBtn.contains(e.target) &&
            sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        }
    });
});

// Profile form submission
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(profileForm);
            formData.append('action', 'update_profile');
            
            // Validate passwords match if provided
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');
            
            if (newPassword && newPassword !== confirmPassword) {
                showAlert('error', 'New passwords do not match');
                return;
            }
            
            const submitBtn = profileForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            
            fetch('controllers/AjaxController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Profile updated successfully');
                    hideModal();
                    // Update session data display if needed
                    location.reload();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while updating profile');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Profile';
            });
        });
    }
});
</script>

<style>
@media (max-width: 768px) {
    .sidebar.mobile-hidden {
        transform: translateX(-100%);
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
    }
}

.nav-link {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.badge {
    font-size: 0.6rem;
    padding: 0.2rem 0.4rem;
    border-radius: 9999px;
    min-width: 1.2rem;
    text-align: center;
}
</style>