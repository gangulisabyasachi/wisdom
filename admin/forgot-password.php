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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    error_log('Forgot password attempt for: ' . $email);

    if (empty($email)) {
        $error = 'Email is required.';
        error_log('Empty email submitted.');
    } else {
        // Check if email exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        if ($stmt === false) {
            $error = 'Database error.';
            error_log('DB prepare failed: ' . $conn->error);
        } else {
            $stmt->bind_param('s', $email);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // Generate OTP
                    $otp = sprintf("%06d", mt_rand(0, 999999));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                    // Store OTP
                    $stmt = $conn->prepare('INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, ?)');
                    if ($stmt === false) {
                        $error = 'Database error.';
                        error_log('OTP insert prepare failed: ' . $conn->error);
                    } else {
                        $stmt->bind_param('sss', $email, $otp, $expires_at);
                        if ($stmt->execute()) {
                            // Send OTP email
                            if ($mailer->send($email, $email, 'Journal Admin Portal - Password Reset OTP', "Your OTP is: $otp\nValid for 15 minutes.", 'it@wisdomj.in')) {
                                $_SESSION['reset_email'] = $email;
                                $success = 'OTP sent. Check inbox/spam.';
                                error_log('OTP email sent to: ' . $email);
                                header('Location: verify-otp.php');
                                $stmt->close();
                                $conn->close();
                                exit;
                            } else {
                                $error = 'Failed to send OTP email.';
                                error_log('OTP email failed for: ' . $email);
                            }
                        } else {
                            $error = 'Database error.';
                            error_log('OTP insert failed: ' . $stmt->error);
                        }
                        $stmt->close();
                    }
                } else {
                    $error = 'Email not found.';
                    error_log('Email not in users table: ' . $email);
                }
            } else {
                $error = 'Database error.';
                error_log('DB query failed: ' . $stmt->error);
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
    <title>Journal Admin Portal - Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-outfit antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <div class="w-full max-w-md mx-auto p-6">
            <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 sm:p-7">
                    <div class="text-center">
                        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Forgot Password</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Enter your email to receive an OTP.
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
                                    <label for="email" class="block text-sm mb-2 dark:text-white">Email*</label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600" required>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">Send OTP</button>
                            </div>
                        </form>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
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