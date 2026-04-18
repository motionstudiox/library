<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'Book Details';
$userId = SessionManager::getCurrentUserId();

$bookId = (int)($_GET['id'] ?? 0);
if (!$bookId) {
    header('Location: ' . SITE_URL . '/pages/books.php');
    exit;
}

// Get book details
$bookResult = $db->query("SELECT * FROM books WHERE id = $bookId");
if ($bookResult->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Book not found.', 'error');
}
$book = $bookResult->fetch_assoc();

// Get reviews
$reviewsResult = $db->query("
    SELECT r.id, r.rating, r.comment, r.review_date, u.username, u.full_name
    FROM review r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = $bookId
    ORDER BY r.review_date DESC
");

// Get average rating
$ratingResult = $db->query("
    SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
    FROM review WHERE book_id = $bookId
");
$ratings = $ratingResult->fetch_assoc();
$avgRating = $ratings['avg_rating'] ? round($ratings['avg_rating'], 1) : 0;
$reviewCount = $ratings['review_count'] ?? 0;

// Check if user has borrowed this book
$userBorrowed = $db->query("
    SELECT id FROM lending 
    WHERE user_id = $userId AND book_id = $bookId 
    LIMIT 1
");

// Check user's existing review
$userReview = $db->query("
    SELECT * FROM review 
    WHERE user_id = $userId AND book_id = $bookId
");
?>

<!-- Book Header -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 80px; margin: 20px 0;">
                    <i class="fas fa-book"></i>
                </div>
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($book['author']); ?></p>
                
                <?php if ($book['genre']): ?>
                    <span class="badge bg-info mb-2"><?php echo htmlspecialchars($book['genre']); ?></span>
                <?php endif; ?>
                
                <div class="mt-3 mb-3">
                    <div class="mb-2">
                        <?php if ($avgRating > 0): ?>
                            <span class="badge bg-warning text-dark">
                                <?php echo formatStars($avgRating); ?> <?php echo $avgRating; ?>/5
                            </span>
                            <br>
                            <small class="text-muted"><?php echo $reviewCount; ?> review<?php echo $reviewCount !== 1 ? 's' : ''; ?></small>
                        <?php else: ?>
                            <small class="text-muted">No ratings yet</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="alert alert-info mb-3">
                    <strong><?php echo $book['available_copies']; ?> of <?php echo $book['total_copies']; ?></strong> available
                </div>

                <?php if ($userBorrowed->num_rows > 0): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/my-lending.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-check"></i> You Have This Book
                    </a>
                <?php elseif ($book['available_copies'] > 0): ?>
                    <a href="<?php echo SITE_URL; ?>/api/borrow.php?book_id=<?php echo $book['id']; ?>" class="btn btn-primary w-100">
                        <i class="fas fa-download"></i> Borrow This Book
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/api/reserve.php?book_id=<?php echo $book['id']; ?>" class="btn btn-warning w-100">
                        <i class="fas fa-bookmark"></i> Reserve
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Book Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Book Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>ISBN:</strong><br><?php echo htmlspecialchars($book['isbn']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Publication Year:</strong><br><?php echo $book['publication_year'] ?? 'N/A'; ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Publisher:</strong><br><?php echo htmlspecialchars($book['publisher'] ?? 'Unknown'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Copies:</strong><br><?php echo $book['total_copies']; ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Description:</strong></p>
                        <p><?php echo htmlspecialchars($book['description'] ?? 'No description available.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-star"></i> Reviews & Ratings (<?php echo $reviewCount; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if ($userBorrowed->num_rows > 0): ?>
                    <form method="POST" action="<?php echo SITE_URL; ?>/api/add-review.php" class="mb-4 pb-4 border-bottom">
                        <h6>Leave a Review</h6>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select id="rating" name="rating" class="form-select" required>
                                <option value="">Select rating...</option>
                                <option value="1">⭐ 1 - Poor</option>
                                <option value="2">⭐⭐ 2 - Fair</option>
                                <option value="3">⭐⭐⭐ 3 - Good</option>
                                <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment (optional)</label>
                            <textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Share your thoughts..."></textarea>
                        </div>
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Review
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i> Borrow this book to leave a review.
                    </div>
                <?php endif; ?>

                <!-- Existing Reviews -->
                <?php if ($reviewsResult->num_rows > 0): ?>
                    <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                    <br>
                                    <span class="badge bg-warning text-dark">
                                        <?php echo formatStars($review['rating']); ?> <?php echo $review['rating']; ?>/5
                                    </span>
                                </div>
                                <small class="text-muted"><?php echo formatDateTime($review['review_date']); ?></small>
                            </div>
                            <p class="mt-2"><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted"><i class="fas fa-comment-slash"></i> No reviews yet. Be the first to review!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
