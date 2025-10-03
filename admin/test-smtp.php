<?php
ini_set('display_errors', 1);
ini_set('error_log', 'logs/error.log');

require_once 'simple-smtp.php';

$mailer = new SimpleSMTPMailer();
$to = 'ganguli.sabyasachi2705@gmail.com'; // Replace with your test email
$sent = $mailer->send($to, $to, 'Test Password Email', 'This is a test password email.', 'it@wisdomj.in');

echo $sent ? 'Email sent. Check inbox/spam.' : 'Failed to send email. Check logs.';
error_log('Test email to ' . $to . ': ' . ($sent ? 'Sent' : 'Failed'));
?>