<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Car Rental Owner</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <!-- Meta tags for SEO and social sharing -->
    <meta name="description" content="Car Rental Owner Dashboard - Manage your rental cars, bookings, and earnings">
    <meta name="keywords" content="car rental, dashboard, car owner, booking management">
    <meta name="author" content="Car Rental System">
    
    <!-- Open Graph meta tags -->
    <meta property="og:title" content="<?php echo $page_title ?? 'Dashboard'; ?> - Car Rental Owner">
    <meta property="og:description" content="Manage your car rental business with our comprehensive dashboard">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Twitter Card meta tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo $page_title ?? 'Dashboard'; ?> - Car Rental Owner">
    <meta name="twitter:description" content="Manage your car rental business with our comprehensive dashboard">
    
    <!-- Additional CSS for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom inline styles if needed -->
    <?php if (isset($inline_styles)): ?>
        <style><?php echo $inline_styles; ?></style>
    <?php endif; ?>
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <!-- Loading overlay -->
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; align-items: center; justify-content: center;">
        <div class="loading-spinner"></div>
    </div>