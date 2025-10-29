<?php
// Harden session before starting
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    // PHP ≥7.3 supports this directly; safe on lower versions too
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Prevent cached admin pages from showing after logout/back
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Gate: require login
if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    // Optional: remember intended URL
    $redirect = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index.php';
    header('Location: login.php?redirect=' . urlencode($redirect));
    exit;
}

// Convenience var for templates
$currentUser = $_SESSION['user'];
