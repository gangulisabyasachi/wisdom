<?php
// Start session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('display_errors', 1);
ini_set('error_log', 'logs/error.log');

// Create logs directory
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

if (!is_writable(session_save_path())) {
    error_log('Session path not writable.');
    die('Session path error.');
}

if (!session_start()) {
    error_log('Session start failed.');
    die('Session start error.');
}

require_once 'connection.php';
require_once 'simple-smtp.php';

$mailer = new SimpleSMTPMailer();
$error = '';
$success = '';

if (!isset($_SESSION['reset_email'])) {
    $error = 'Session expired. Please try again.';
    error_log('Session expired in verify-otp.php');
    header('Location: forgot-password.php');
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'] ?? '';
    error_log('OTP verification attempt for: ' . $email . ', OTP: ' . $otp);

    if (empty($otp)) {
        $error = 'OTP is required.';
        error_log('Empty OTP submitted.');
    } else {
        // Verify OTP
        $stmt = $conn->prepare('SELECT otp FROM password_resets WHERE email = ? AND otp = ? AND expires_at > NOW()');
        if ($stmt === false) {
            $error = 'Database error.';
            error_log('OTP verify prepare failed: ' . $conn->error);
        } else {
            $stmt->bind_param('ss', $email, $otp);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // OTP valid, get password
                    $stmt = $conn->prepare('SELECT password FROM users WHERE email = ?');
                    if ($stmt === false) {
                        $error = 'Database error.';
                        error_log('Password fetch prepare failed: ' . $conn->error);
                    } else {
                        $stmt->bind_param('s', $email);
                        if ($stmt->execute()) {
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            if ($user && !empty($user['password'])) {
                                $password = $user['password'];

                                // Send password email
                                if ($mailer->send($email, $email, 'Journal Admin Portal - Your Password', "Your password is: $password\nPlease log in and change it.", 'it@wisdomj.in')) {
                                    $success = 'Password sent. Check inbox/spam.';
                                    error_log('Password email sent to: ' . $email);

                                    // Delete OTP
                                    $stmt = $conn->prepare('DELETE FROM password_resets WHERE email = ?');
                                    $stmt->bind_param('s', $email);
                                    $stmt->execute();
                                    $stmt->close();

                                    // Clear session
                                    unset($_SESSION['reset_email']);
                                    $_SESSION['success'] = $success;
                                    header('Location: login.php');
                                    $conn->close();
                                    exit;
                                } else {
                                    $error = 'Failed to send password email.';
                                    error_log('Password email failed for: ' . $email);
                                }
                            } else {
                                $error = 'No password found.';
                                error_log('No password for: ' . $email);
                            }
                        } else {
                            $error = 'Database error.';
                            error_log('Password fetch failed: ' . $stmt->error);
                        }
                        $stmt->close();
                    }
                } else {
                    $error = 'Invalid or expired OTP.';
                    error_log('Invalid OTP for: ' . $email);
                }
            } else {
                $error = 'Database error.';
                error_log('OTP verify query failed: ' . $stmt->error);
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Admin Portal - Verify OTP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-outfit antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <div class="w-full max-w-md mx-auto p-6">
            <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 sm:p-7">
                    <div class="text-center">
                        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Verify OTP</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Enter the OTP sent to <?php echo htmlspecialchars($email); ?>.
                        </p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            MAIL CAN TAKE UP TO 1 HOUR TO COME. PLEASE BEAR WITH US. PLEASE CHECK YOUR INBOX AND SPAM FOLDER.
                        </p>
                        <?php if ($error): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <p class="mt-2 text-sm text-green-600"><?php echo htmlspecialchars($success); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5">
                        <form method="POST">
                            <div class="grid gap-y-4">
                                <div>
                                    <label for="otp" class="block text-sm mb-2 dark:text-white">OTP*</label>
                                    <div class="relative">
                                        <input type="text" id="otp" name="otp" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600" required>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">Verify OTP</button>
                            </div>
                        </form>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <a href="forgot-password.php" class="text-blue-600 decoration-2 hover:underline">Resend OTP</a> | 
                            <a href="login.php" class="text-blue-600 decoration-2 hover:underline">Back to Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bundle.js"></script>
</body>
</html>