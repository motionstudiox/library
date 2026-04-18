<?php
require_once __DIR__ . '/includes/header.php';

$userId = SessionManager::getCurrentUserId();
$pageTitle = 'Dashboard';

// Get statistics
$userBooks = $db->query("SELECT COUNT(*) as count FROM lending WHERE user_id = $userId AND is_returned = FALSE");
$userBooks = $userBooks->fetch_assoc()['count'];

$userReservations = $db->query("SELECT COUNT(*) as count FROM reservation WHERE user_id = $userId AND is_active = TRUE");
$userReservations = $userReservations->fetch_assoc()['count'];

$overdueBooks = $db->query("SELECT COUNT(*) as count FROM lending WHERE user_id = $userId AND is_returned = FALSE AND due_date < NOW()");
$overdueBooks = $overdueBooks->fetch_assoc()['count'];

$totalBooks = $db->query("SELECT COUNT(*) as count FROM books");
$totalBooks = $totalBooks->fetch_assoc()['count'];

// Recent activity
$recentLending = $db->query("
    SELECT l.id, b.title, l.due_date, l.is_returned 
    FROM lending l 
    JOIN books b ON l.book_id = b.id 
    WHERE l.user_id = $userId 
    ORDER BY l.borrow_date DESC 
    LIMIT 5
");
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-line"></i> Welcome, <?php echo htmlspecialchars($currentUser); ?>!</h2>
        <p class="text-muted">Here's your library activity overview</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted">Books Borrowed</h6>
                        <h3><?php echo $userBooks; ?></h3>
                    </div>
                    <div class="text-primary" style="font-size: 40px;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-left-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted">Reservations</h6>
                        <h3><?php echo $userReservations; ?></h3>
                    </div>
                    <div class="text-warning" style="font-size: 40px;">
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-left-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted">Overdue</h6>
                        <h3 style="color: <?php echo $overdueBooks > 0 ? '#dc3545' : '#28a745'; ?>;">
                            <?php echo $overdueBooks; ?>
                        </h3>
                    </div>
                    <div style="font-size: 40px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-left-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted">Catalog</h6>
                        <h3><?php echo $totalBooks; ?></h3>
                    </div>
                    <div class="text-info" style="font-size: 40px;">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Borrowings</h5>
            </div>
            <div class="card-body">
                <?php if ($recentLending->num_rows > 0): ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $recentLending->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo formatDate($item['due_date']); ?></td>
                                    <td><?php echo getStatusBadge($item['is_returned'] ? 'returned' : 'active'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No borrowing history yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-random"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo SITE_URL; ?>/pages/books.php" class="btn btn-outline-primary btn-block w-100 mb-2">
                    <i class="fas fa-search"></i> Browse Books
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/my-lending.php" class="btn btn-outline-info btn-block w-100 mb-2">
                    <i class="fas fa-hand-holding-heart"></i> My Loans
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/reservations.php" class="btn btn-outline-warning btn-block w-100 mb-2">
                    <i class="fas fa-bookmark"></i> Reservations
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/profile.php" class="btn btn-outline-secondary btn-block w-100">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
    margin-top: 10px;
}

.card .card-body {
    padding: 1.5rem;
}

.border-left-primary {
    border-left: 0.25rem solid #007bff!important;
}

.border-left-warning {
    border-left: 0.25rem solid #ffc107!important;
}

.border-left-danger {
    border-left: 0.25rem solid #dc3545!important;
}

.border-left-info {
    border-left: 0.25rem solid #17a2b8!important;
}

.btn-block {
    display: block;
    width: 100%;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
