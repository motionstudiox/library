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
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Cannot renew a returned book.', 'error');
}

// Check if overdue
if ($lending['due_date'] < date('Y-m-d H:i:s')) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Cannot renew an overdue book. Please return it first.', 'error');
}

// Extend due date by 14 days
$newDueDate = date('Y-m-d H:i:s', strtotime($lending['due_date'] . ' +14 days'));

$stmt = $db->prepare("UPDATE lending SET due_date = ? WHERE id = ?");
$stmt->bind_param("si", $newDueDate, $lendingId);

if ($stmt->execute()) {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Book renewed! New due date: ' . formatDate($newDueDate), 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/my-lending.php', 'Failed to renew book. Please try again.', 'error');
}
