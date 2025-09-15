<!-- JavaScript Files -->
    <script src="../assets/js/dashboard.js"></script>
    
    <!-- Additional JavaScript for specific pages -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom inline scripts if needed -->
    <?php if (isset($inline_scripts)): ?>
        <script><?php echo $inline_scripts; ?></script>
    <?php endif; ?>
    
    <!-- Service Worker Registration (if needed for PWA features) -->
    <script>
        // Check if service worker is supported
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('../sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    
    <!-- Global error handler -->
    <script>
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            // You can send error reports to your logging service here
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            // You can send error reports to your logging service here
        });
    </script>
    
    <!-- Performance monitoring (optional) -->
    <script>
        // Basic performance monitoring
        window.addEventListener('load', function() {
            setTimeout(function() {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart + 'ms');
                }
            }, 0);
        });
    </script>
    
</body>
</html>

<?php
// Clean up any remaining session data that should only be shown once
$cleanup_keys = ['form_data', 'temp_data', 'flash_data'];
foreach ($cleanup_keys as $key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}
?>