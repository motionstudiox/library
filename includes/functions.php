<?php
/**
 * Utility Functions
 */

/**
 * Password hashing and verification
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validation functions
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPhone($phone) {
    return preg_match('/^\d{3}-\d{3}-\d{4}$|^\d{10}$/', $phone);
}

function isValidISBN($isbn) {
    $isbn = str_replace('-', '', $isbn);
    return (strlen($isbn) == 10 || strlen($isbn) == 13) && ctype_digit($isbn);
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Input sanitization
 */
function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

function sanitizeSQL($input) {
    global $db;
    return $db->escape($input);
}

/**
 * Formatting functions
 */
function formatDate($date) {
    return date('Y-m-d', strtotime($date));
}

function formatDateTime($datetime) {
    return date('Y-m-d H:i', strtotime($datetime));
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatStars($rating) {
    $stars = '';
    for ($i = 0; $i < intval($rating); $i++) {
        $stars .= '⭐';
    }
    return $stars;
}

/**
 * Error and success messages
 */
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

function getErrorMessage() {
    $message = $_SESSION['error_message'] ?? null;
    unset($_SESSION['error_message']);
    return $message;
}

function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

function getSuccessMessage() {
    $message = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);
    return $message;
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'error') {
    if ($type === 'error') {
        setErrorMessage($message);
    } else {
        setSuccessMessage($message);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Calculate fine for overdue books
 */
function calculateFine($dueDate, $dailyRate = 0.50) {
    $currentDate = time();
    $dueDateTimestamp = strtotime($dueDate);
    
    if ($currentDate <= $dueDateTimestamp) {
        return 0;
    }
    
    $daysOverdue = floor(($currentDate - $dueDateTimestamp) / 86400);
    return $daysOverdue * $dailyRate;
}

/**
 * Get days until due
 */
function getDaysUntilDue($dueDate) {
    $currentDate = time();
    $dueDateTimestamp = strtotime($dueDate);
    $daysLeft = floor(($dueDateTimestamp - $currentDate) / 86400);
    
    return $daysLeft;
}

/**
 * Get status badge
 */
function getStatusBadge($status) {
    $badges = [
        'active' => '<span class="badge bg-success">Active</span>',
        'inactive' => '<span class="badge bg-secondary">Inactive</span>',
        'returned' => '<span class="badge bg-info">Returned</span>',
        'overdue' => '<span class="badge bg-danger">Overdue</span>',
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'completed' => '<span class="badge bg-success">Completed</span>',
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Pagination helper
 */
function getPaginationInfo($currentPage, $totalRecords, $recordsPerPage = 10) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'limit' => $recordsPerPage,
        'total_records' => $totalRecords
    ];
}
