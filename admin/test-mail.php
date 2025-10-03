<?php
// Enable error reporting for testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Test email details (replace with your actual test email)
$to = 'ganguli.sabyasachi2705@gmail.com';  // Change this to an email you control
$subject = 'PHP Mail Test from WisdomJ Admin';
$message = 'If you receive this, PHP mail() works! Sent on: ' . date('Y-m-d H:i:s');
$headers = 'From: it@wisdomj.in' . "\r\n" .
           'Reply-To: it@wisdomj.in' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

echo "<h2>Testing PHP mail()...</h2>";

// Check if mail() function exists
if (!function_exists('mail')) {
    die('Error: mail() function is disabled on this server.');
} else {
    echo '✓ mail() function is available.<br>';
}

// Attempt to send
$sent = mail($to, $subject, $message, $headers);
if ($sent) {
    echo '✓ mail() returned true (email queued/sent).<br>';
} else {
    echo '✗ mail() returned false (immediate failure).<br>';
}

// Log the attempt for debugging
error_log('Mail test attempt: To=' . $to . ', Sent=' . ($sent ? 'Yes' : 'No'));

echo '<p>Check your inbox/spam in 5-10 minutes. Also check server logs in /admin/logs/error.log for clues.</p>';
?>