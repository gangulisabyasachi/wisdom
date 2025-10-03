<?php
// Start session with error handling
ini_set('session.cookie_httponly', 1); // Enhance session security
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('error_log', 'logs/error.log'); // Ensure errors are logged

// Check session save path
$session_path = session_save_path();
if (!$session_path || !is_writable($session_path)) {
    error_log('Session save path not writable: ' . $session_path, 3, 'logs/error.log');
    die('Session save path is not configured or not writable. Please check php.ini.');
}

if (!session_start()) {
    error_log('Session start failed: ' . session_status(), 3, 'logs/error.log');
    die('Session initialization failed. Please check server configuration.');
}

// Log session ID for debugging
error_log('Logout attempt for session ID: ' . session_id(), 3, 'logs/error.log');

// Store success message for display on login.php
$_SESSION['success'] = 'You have been logged out successfully.';

// Destroy session
session_unset();
session_destroy();

// Log successful logout
error_log('Logout successful for session ID: ' . session_id(), 3, 'logs/error.log');

// Redirect to login.php
header('Location: login.php');
exit;
?>