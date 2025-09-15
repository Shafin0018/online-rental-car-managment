<?php
// views/reviews/index.php - Reviews management view
$page_title = "Reviews & Ratings";
include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Reviews & Ratings</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline" onclick="filterReviews('needs_response')">
                    <i class="fas fa-reply"></i> Needs Response
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php include __DIR__ . '/../layouts/alerts.php'; ?>

        <!-- Reviews Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Total Reviews</h3>
                    <div class="stat-icon primary">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['total_reviews'] ?? 0; ?></div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Average Rating</h3>
                    <div class="stat-icon success">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo number_format($stats['average_rating'] ?? 0, 1); ?></div>
                <div class="stat-change">
                    <?php 
                    $totalReviews = $stats['total_reviews'] ?? 0;
                    if ($totalReviews > 0) {
                        for ($i = 1; $i <= 5; $i++) {
                            $color = $i <= round($stats['average_rating']) ? '#f59e0b' : '#d1d5db';
                            echo "<i class='fas fa-star' style='color: $color; font-size: 0.75rem;'></i> ";
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Response Rate</h3>
                    <div class="stat-icon warning">
                        <i class="fas fa-reply"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?php 
                    $responseRate = $stats['total_reviews'] > 0 ? 
                        round(($stats['responded_reviews'] / $stats['total_reviews']) * 100) : 0;
                    echo $responseRate . '%';
                    ?>
                </div>
                <div class="stat-change">
                    <?php echo $stats['responded_reviews'] ?? 0; ?> of <?php echo $stats['total_reviews'] ?? 0; ?> responded
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">5-Star Reviews</h3>
                    <div class="stat-icon info">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['five_star'] ?? 0; ?></div>
                <div class="stat-change">
                    <?php 
                    $fiveStarRate = $stats['total_reviews'] > 0 ? 
                        round(($stats['five_star'] / $stats['total_reviews']) * 100) : 0;
                    echo $fiveStarRate . '% of total';
                    ?>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Rating Distribution</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
                    <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                        <?php 
                        $count = $stats[$rating == 1 ? 'one_star' : ($rating == 2 ? 'two_star' : ($rating == 3 ? 'three_star' : ($rating == 4 ? 'four_star' : 'five_star')))] ?? 0;
                        $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                        ?>
                        <div class="rating-bar">
                            <div class="d-flex justify-between items-center mb-2">
                                <span><?php echo $rating; ?> Stars</span>
                                <span class="text-muted"><?php echo $count; ?></span>
                            </div>
                            <div style="background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="background: #f59e0b; height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customer Reviews</h3>
                <div class="d-flex gap-2">
                    <select class="form-control" id="rating-filter" style="width: 150px;">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select class="form-control" id="response-filter" style="width: 150px;">
                        <option value="">All Reviews</option>
                        <option value="responded">Responded</option>
                        <option value="needs_response">Needs Response</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($reviews)): ?>
                    <div class="text-center" style="padding: 3rem;">
                        <i class="fas fa-comment-alt" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--text-muted); margin-bottom: 1rem;">No reviews yet</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">Customer reviews will appear here after completed bookings.</p>
                    </div>
                <?php else: ?>
                    <div id="reviews-container">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card" data-rating="<?php echo $review['rating']; ?>" data-responded="<?php echo !empty($review['owner_response']) ? 'true' : 'false'; ?>">
                                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                                    <!-- Review Header -->
                                    <div class="d-flex justify-between items-start mb-3">
                                        <div>
                                            <div class="d-flex items-center gap-3 mb-2">
                                                <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong>
                                                <div class="rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#f59e0b' : '#d1d5db'; ?>; font-size: 0.875rem;"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.875rem;">
                                                <?php echo htmlspecialchars($review['make'] . ' ' . $review['model'] . ' (' . $review['year'] . ')'); ?> •
                                                <?php echo formatDate($review['created_at']); ?>
                                                <?php if (!empty($review['start_date']) && !empty($review['end_date'])): ?>
                                                    • Rental: <?php echo formatDate($review['start_date']); ?> - <?php echo formatDate($review['end_date']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Content -->
                                    <div class="review-content mb-3">
                                        <p style="margin: 0; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                    </div>

                                    <!-- Owner Response -->
                                    <div class="review-response">
                                        <?php if (!empty($review['owner_response'])): ?>
                                            <div style="background: var(--secondary-color); padding: 1rem; border-radius: 0.5rem; border-left: 4px solid var(--primary-color);">
                                                <div class="d-flex justify-between items-start mb-2">
                                                    <strong style="color: var(--primary-color);">Your Response</strong>
                                                    <small class="text-muted"><?php echo formatDate($review['response_date']); ?></small>
                                                </div>
                                                <p style="margin: 0; color: var(--text-color);"><?php echo nl2br(htmlspecialchars($review['owner_response'])); ?></p>
                                            </div>
                                        <?php else: ?>
                                            <form class="review-response-form" style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Respond to this review:</label>
                                                    <textarea name="response" class="form-control" rows="3" 
                                                              placeholder="Thank you for your feedback..." required></textarea>
                                                </div>
                                                <div class="d-flex justify-end">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-reply"></i> Send Response
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const ratingFilter = document.getElementById('rating-filter');
    const responseFilter = document.getElementById('response-filter');
    
    if (ratingFilter) {
        ratingFilter.addEventListener('change', filterReviews);
    }
    
    if (responseFilter) {
        responseFilter.addEventListener('change', filterReviews);
    }
});

function filterReviews(type = null) {
    const reviewCards = document.querySelectorAll('.review-card');
    const ratingFilter = document.getElementById('rating-filter');
    const responseFilter = document.getElementById('response-filter');
    
    // Set filter if called with specific type
    if (type === 'needs_response') {
        responseFilter.value = 'needs_response';
    }
    
    const selectedRating = ratingFilter ? ratingFilter.value : '';
    const selectedResponse = responseFilter ? responseFilter.value : '';
    
    reviewCards.forEach(card => {
        let showCard = true;
        
        // Rating filter
        if (selectedRating && card.getAttribute('data-rating') !== selectedRating) {
            showCard = false;
        }
        
        // Response filter
        if (selectedResponse) {
            const hasResponse = card.getAttribute('data-responded') === 'true';
            if ((selectedResponse === 'responded' && !hasResponse) || 
                (selectedResponse === 'needs_response' && hasResponse)) {
                showCard = false;
            }
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

// Auto-expand textareas
document.addEventListener('input', function(e) {
    if (e.target.matches('textarea')) {
        e.target.style.height = 'auto';
        e.target.style.height = e.target.scrollHeight + 'px';
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>