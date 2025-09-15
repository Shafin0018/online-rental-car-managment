<?php
// views/layouts/alerts.php - Alert messages display

// Success messages
if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif;

// Error messages
if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif;

// Warning messages
if (isset($_SESSION['warning_message'])): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <?php echo htmlspecialchars($_SESSION['warning_message']); ?>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
    <?php unset($_SESSION['warning_message']); ?>
<?php endif;

// Info messages
if (isset($_SESSION['info_message'])): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <?php echo htmlspecialchars($_SESSION['info_message']); ?>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
    <?php unset($_SESSION['info_message']); ?>
<?php endif;

// Validation errors
if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<style>
.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
    position: relative;
}

.alert i {
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.alert ul {
    margin-bottom: 0;
}

.alert-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}
</style>