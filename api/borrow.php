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
$book = $bookResult->fetch_assoc();

// Check if user already has this book borrowed
$existingLending = $db->query("SELECT id FROM lending WHERE user_id = $userId AND book_id = $bookId AND is_returned = FALSE");
if ($existingLending->num_rows > 0) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'You already have this book borrowed.', 'error');
}

// Check availability
if ($book['available_copies'] <= 0) {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'This book is not available. Please reserve it instead.', 'error');
}

// Create lending record
$dueDate = date('Y-m-d H:i:s', strtotime('+14 days'));
$stmt = $db->prepare("INSERT INTO lending (user_id, book_id, due_date) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $userId, $bookId, $dueDate);

if ($stmt->execute()) {
    // Update book availability
    $newAvailable = $book['available_copies'] - 1;
    $db->query("UPDATE books SET available_copies = $newAvailable WHERE id = $bookId");
    
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Book borrowed successfully! Due date: ' . formatDate($dueDate), 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Failed to borrow book. Please try again.', 'error');
}
