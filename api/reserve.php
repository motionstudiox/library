<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

SessionManager::requireLogin();

$userId = SessionManager::getCurrentUserId();
$bookId = (int)($_GET['book_id'] ?? 0);

if (!$bookId) {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Invalid book ID.', 'error');
}

// Get book
$bookResult = $db->query("SELECT * FROM books WHERE id = $bookId");
if ($bookResult->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Book not found.', 'error');
}

// Check if user already has active reservation
$existingRes = $db->query("SELECT id FROM reservation WHERE user_id = $userId AND book_id = $bookId AND is_active = TRUE");
if ($existingRes->num_rows > 0) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'You already have an active reservation for this book.', 'error');
}

// Create reservation
$stmt = $db->prepare("INSERT INTO reservation (user_id, book_id) VALUES (?, ?)");
$stmt->bind_param("ii", $userId, $bookId);

if ($stmt->execute()) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'Book reserved successfully!', 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Failed to reserve book. Please try again.', 'error');
}
