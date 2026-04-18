<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

SessionManager::requireLogin();

$userId = SessionManager::getCurrentUserId();
$lendingId = (int)($_GET['lending_id'] ?? 0);

if (!$lendingId) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Invalid lending ID.', 'error');
}

// Get lending record
$lendingResult = $db->query("SELECT * FROM lending WHERE id = $lendingId AND user_id = $userId");
if ($lendingResult->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Lending record not found.', 'error');
}
$lending = $lendingResult->fetch_assoc();

if ($lending['is_returned']) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'This book has already been returned.', 'error');
}

// Calculate fine if overdue
$fineAmount = calculateFine($lending['due_date']);
$returnDate = date('Y-m-d H:i:s');

// Update lending record
$stmt = $db->prepare("UPDATE lending SET is_returned = TRUE, return_date = ?, fine_amount = ? WHERE id = ?");
$stmt->bind_param("sdi", $returnDate, $fineAmount, $lendingId);

if ($stmt->execute()) {
    // Increase book availability
    $db->query("UPDATE books SET available_copies = available_copies + 1 WHERE id = " . $lending['book_id']);
    
    $message = 'Book returned successfully!';
    if ($fineAmount > 0) {
        $message .= ' Fine amount: ' . formatCurrency($fineAmount);
    }
    
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', $message, 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Failed to return book. Please try again.', 'error');
}
