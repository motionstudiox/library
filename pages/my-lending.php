<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'My Loans';
$userId = SessionManager::getCurrentUserId();

// Get active loans
$activeLending = $db->query("
    SELECT l.id, l.borrow_date, l.due_date, b.title, b.id as book_id,
           CASE WHEN l.due_date < NOW() THEN DATEDIFF(NOW(), l.due_date) ELSE 0 END as days_overdue,
           CASE WHEN l.due_date < NOW() THEN ROUND((DATEDIFF(NOW(), l.due_date) * 0.50), 2) ELSE 0 END as fine_amount
    FROM lending l
    JOIN books b ON l.book_id = b.id
    WHERE l.user_id = $userId AND l.is_returned = FALSE
    ORDER BY l.due_date ASC
");

// Get returned loans
$returnedLending = $db->query("
    SELECT l.id, l.borrow_date, l.due_date, l.return_date, b.title, l.fine_amount
    FROM lending l
    JOIN books b ON l.book_id = b.id
    WHERE l.user_id = $userId AND l.is_returned = TRUE
    ORDER BY l.return_date DESC
    LIMIT 20
");
?>

<h2 class="mb-4"><i class="fas fa-hand-holding-heart"></i> My Loans</h2>

<!-- Active Loans -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-book"></i> Active Loans</h5>
    </div>
    <div class="card-body">
        <?php if ($activeLending->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Book</th>
                            <th>Borrowed</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Fine</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($loan = $activeLending->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($loan['title']); ?></td>
                                <td><?php echo formatDate($loan['borrow_date']); ?></td>
                                <td><?php echo formatDate($loan['due_date']); ?></td>
                                <td>
                                    <?php 
                                    $days_overdue = $loan['days_overdue'];
                                    if ($days_overdue > 0) {
                                        echo "<span class='badge bg-danger'>Overdue ({$days_overdue} days)</span>";
                                    } else {
                                        $days_until = getDaysUntilDue($loan['due_date']);
                                        if ($days_until <= 3) {
                                            echo "<span class='badge bg-warning'>Due soon ({$days_until} days)</span>";
                                        } else {
                                            echo "<span class='badge bg-success'>On Track</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo formatCurrency($loan['fine_amount']); ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/api/return-book.php?lending_id=<?php echo $loan['id']; ?>" 
                                       class="btn btn-sm btn-success"
                                       onclick="return confirm('Return this book?');">
                                       <i class="fas fa-undo"></i> Return
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/api/renew.php?lending_id=<?php echo $loan['id']; ?>" 
                                       class="btn btn-sm btn-info"
                                       onclick="return confirm('Renew this book?');">
                                       <i class="fas fa-sync"></i> Renew
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted"><i class="fas fa-info-circle"></i> You don't have any active loans.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Returned Loans -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-history"></i> Returned Loans</h5>
    </div>
    <div class="card-body">
        <?php if ($returnedLending->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Book</th>
                            <th>Borrowed</th>
                            <th>Returned</th>
                            <th>Fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($loan = $returnedLending->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($loan['title']); ?></td>
                                <td><?php echo formatDate($loan['borrow_date']); ?></td>
                                <td><?php echo formatDate($loan['return_date']); ?></td>
                                <td><?php echo formatCurrency($loan['fine_amount']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted"><i class="fas fa-info-circle"></i> No returned loans yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
