<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Library Management System';

// Check if user is already logged in
if (SessionManager::isLoggedIn()) {
    // Redirect to dashboard if logged in
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to Our Library</h1>
                <p class="lead mb-4">
                    Discover thousands of books, borrow your favorites, and join our community of readers.
                    Access our digital catalog, manage your loans, and leave reviews for other readers.
                </p>
                <div class="d-flex gap-3">
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus"></i> Join Now
                    </a>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-icon">
                    <i class="fas fa-book-open" style="font-size: 120px; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="display-5 fw-bold text-primary mb-3">What We Offer</h2>
            <p class="lead text-muted">Everything you need for your reading journey</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Feature 1: Book Catalog -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-search text-primary" style="font-size: 48px;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Extensive Catalog</h5>
                    <p class="card-text text-muted">
                        Browse our comprehensive collection of books across all genres.
                        Search by title, author, or ISBN to find exactly what you're looking for.
                    </p>
                </div>
            </div>
        </div>

        <!-- Feature 2: Easy Borrowing -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-hand-holding-heart text-success" style="font-size: 48px;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Easy Borrowing</h5>
                    <p class="card-text text-muted">
                        Borrow books with just a few clicks. Track due dates, renew loans,
                        and manage your reading list all in one place.
                    </p>
                </div>
            </div>
        </div>

        <!-- Feature 3: Reviews & Community -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-star text-warning" style="font-size: 48px;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Reviews & Community</h5>
                    <p class="card-text text-muted">
                        Share your thoughts with ratings and reviews. Discover new books
                        through community recommendations and feedback.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-number display-4 fw-bold text-primary">5,000+</div>
                <div class="stat-label h6 text-muted">Books Available</div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-number display-4 fw-bold text-success">1,200+</div>
                <div class="stat-label h6 text-muted">Active Members</div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-number display-4 fw-bold text-info">15,000+</div>
                <div class="stat-label h6 text-muted">Books Borrowed</div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-number display-4 fw-bold text-warning">4.8</div>
                <div class="stat-label h6 text-muted">Average Rating</div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h3 class="mb-4">Ready to Start Your Reading Journey?</h3>
            <p class="lead mb-4 text-muted">
                Join thousands of readers who trust our library for their literary needs.
                Create your account today and get instant access to our collection.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-rocket"></i> Get Started Free
                </a>
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-primary btn-lg px-4">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Demo Credentials Alert -->
<div class="container pb-4">
    <div class="alert alert-info border-0 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-3 text-info" style="font-size: 24px;"></i>
            <div>
                <strong>Demo Account Available:</strong> Username: <code>john_doe</code> | Password: <code>password123</code>
                <br>
                <small class="text-muted">Use this to explore the system before creating your own account.</small>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.feature-icon {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-number {
    font-size: 2.5rem;
    line-height: 1;
}

@media (max-width: 768px) {
    .hero-section {
        text-align: center;
        padding: 3rem 0;
    }

    .display-4 {
        font-size: 2.5rem;
    }

    .stat-number {
        font-size: 2rem;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
