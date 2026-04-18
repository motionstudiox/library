<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'My Reservations';
$userId = SessionManager::getCurrentUserId();

// Get active reservations
$reservations = $db->query("
    SELECT r.id, r.reservation_date, r.is_fulfilled, b.title, b.id as book_id, b.available_copies
    FROM reservation r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = $userId AND r.is_active = TRUE
    ORDER BY r.reservation_date ASC
");
?>

<h2 class="mb-4"><i class="fas fa-bookmark"></i> My Reservations</h2>

<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-bookmark"></i> Active Reservations</h5>
    </div>
    <div class="card-body">
        <?php if ($reservations->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Book</th>
                            <th>Reserved</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res['title']); ?></td>
                                <td><?php echo formatDate($res['reservation_date']); ?></td>
                                <td><?php echo $res['available_copies'] > 0 ? 'Available' : 'Unavailable'; ?></td>
                                <td><?php echo $res['is_fulfilled'] ? '<span class="badge bg-success">Fulfilled</span>' : '<span class="badge bg-warning">Pending</span>'; ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/api/cancel-reservation.php?reservation_id=<?php echo $res['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Cancel this reservation?');">
                                       <i class="fas fa-times"></i> Cancel
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted"><i class="fas fa-info-circle"></i> You don't have any active reservations.</p>
            <a href="<?php echo SITE_URL; ?>/pages/books.php" class="btn btn-primary">
                <i class="fas fa-search"></i> Browse Books
            </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
