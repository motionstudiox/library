<?php
/**
 * Session Management
 */

require_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class SessionManager {
    
    public static function startSession($userId, $username, $fullName) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $fullName;
        $_SESSION['login_time'] = time();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getCurrentUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    public static function getCurrentUserFullName() {
        return $_SESSION['full_name'] ?? null;
    }
    
    public static function isSessionExpired() {
        if (self::isLoggedIn()) {
            $loginTime = $_SESSION['login_time'] ?? 0;
            $currentTime = time();
            
            if (($currentTime - $loginTime) > SESSION_TIMEOUT) {
                self::destroySession();
                return true;
            }
            
            // Reset session timeout
            $_SESSION['login_time'] = $currentTime;
            return false;
        }
        return true;
    }
    
    public static function destroySession() {
        session_unset();
        session_destroy();
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn() || self::isSessionExpired()) {
            header('Location: ' . SITE_URL . '/login.php');
            exit;
        }
    }
    
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn() && !self::isSessionExpired()) {
            header('Location: ' . SITE_URL . '/dashboard.php');
            exit;
        }
    }
}
