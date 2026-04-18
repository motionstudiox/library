<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'Reviews';
$userId = SessionManager::getCurrentUserId();

// Get user reviews
$userReviews = $db->query("
    SELECT r.id, r.rating, r.comment, r.review_date, b.title, b.id as book_id
    FROM review r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = $userId
    ORDER BY r.review_date DESC
");
?>

<h2 class="mb-4"><i class="fas fa-star"></i> My Reviews</h2>

<div class="row mb-4">
    <div class="col-md-12">
        <a href="<?php echo SITE_URL; ?>/pages/books.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Leave a Review
        </a>
    </div>
</div>

<div class="row">
    <?php if ($userReviews->num_rows > 0): ?>
        <?php while ($review = $userReviews->fetch_assoc()): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?php echo SITE_URL; ?>/pages/book-detail.php?id=<?php echo $review['book_id']; ?>">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </a>
                        </h5>
                        <div class="mb-2">
                            <span class="badge bg-info">
                                <?php echo formatStars($review['rating']); ?> <?php echo $review['rating']; ?>/5
                            </span>
                        </div>
                        <p class="card-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small class="text-muted">Reviewed on <?php echo formatDateTime($review['review_date']); ?></small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You haven't written any reviews yet.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
