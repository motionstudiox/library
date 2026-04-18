<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

SessionManager::requireLogin();

$userId = SessionManager::getCurrentUserId();
$bookId = (int)($_POST['book_id'] ?? 0);
$rating = (float)($_POST['rating'] ?? 0);
$comment = sanitizeInput($_POST['comment'] ?? '');

if (!$bookId || $rating < 1 || $rating > 5) {
    redirectWithMessage(SITE_URL . '/pages/book-detail.php?id=' . $bookId, 'Invalid review data.', 'error');
}

// Check if book exists
$bookCheck = $db->query("SELECT id FROM books WHERE id = $bookId LIMIT 1");
if ($bookCheck->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/books.php', 'Book not found.', 'error');
}

// Check if user has borrowed this book
$borrowCheck = $db->query("
    SELECT id FROM lending 
    WHERE user_id = $userId AND book_id = $bookId 
    LIMIT 1
");
if ($borrowCheck->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/book-detail.php?id=' . $bookId, 'You must borrow this book to review it.', 'error');
}

// Check if review already exists
$existingReview = $db->query("
    SELECT id FROM review 
    WHERE user_id = $userId AND book_id = $bookId 
    LIMIT 1
");

if ($existingReview->num_rows > 0) {
    // Update existing review
    $stmt = $db->prepare("UPDATE review SET rating = ?, comment = ?, review_date = NOW() WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("dsii", $rating, $comment, $userId, $bookId);
    $message = 'Review updated successfully!';
} else {
    // Create new review
    $stmt = $db->prepare("INSERT INTO review (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $userId, $bookId, $rating, $comment);
    $message = 'Review added successfully!';
}

if ($stmt->execute()) {
    redirectWithMessage(SITE_URL . '/pages/book-detail.php?id=' . $bookId, $message, 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/book-detail.php?id=' . $bookId, 'Failed to save review.', 'error');
}
