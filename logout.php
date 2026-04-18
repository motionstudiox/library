<?php
require_once __DIR__ . '/includes/session.php';

SessionManager::destroySession();
header('Location: ' . SITE_URL . '/login.php');
exit;
