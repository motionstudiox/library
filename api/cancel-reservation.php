<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

SessionManager::requireLogin();

$userId = SessionManager::getCurrentUserId();
$reservationId = (int)($_GET['reservation_id'] ?? 0);

if (!$reservationId) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'Invalid reservation ID.', 'error');
}

// Get reservation
$resResult = $db->query("SELECT * FROM reservation WHERE id = $reservationId AND user_id = $userId");
if ($resResult->num_rows === 0) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'Reservation not found.', 'error');
}
$reservation = $resResult->fetch_assoc();

if (!$reservation['is_active']) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'This reservation is already inactive.', 'error');
}

// Cancel reservation
$stmt = $db->prepare("UPDATE reservation SET is_active = FALSE WHERE id = ?");
$stmt->bind_param("i", $reservationId);

if ($stmt->execute()) {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'Reservation cancelled successfully!', 'success');
} else {
    redirectWithMessage(SITE_URL . '/pages/reservations.php', 'Failed to cancel reservation. Please try again.', 'error');
}
