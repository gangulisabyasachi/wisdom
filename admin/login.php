<?php
// Start session with error handling
ini_set('session.cookie_httponly', 1); // Enhance session security
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('display_errors', 1); // Enable error display for debugging
ini_set('error_log', 'logs/error.log'); // Set default error log path

// Check and create logs directory
$log_dir = __DIR__ . '/logs';
$log_file = $log_dir . '/error.log';
if (!is_dir($log_dir)) {
    if (!mkdir($log_dir, 0755, true) && !is_dir($log_dir)) {
        ini_set('error_log', null); // Use php.ini default
        error_log('Failed to create logs directory: ' . $log_dir);
    }
}

// Check session save path
$session_path = session_save_path();
if (!$session_path || !is_writable($session_path)) {
    error_log('Session save path not writable: ' . $session_path);
    die('Session save path is not configured or not writable. Please check php.ini.');
}

if (!session_start()) {
    error_log('Session start failed: ' . session_status());
    die('Session initialization failed. Please check server configuration.');
}

// Log session ID for debugging
error_log('Session ID: ' . session_id());

// Include database connection
require_once 'connection.php';

// Initialize variables
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']); // Clear messages

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Log form submission
    error_log('Form submitted with email: ' . $email);

    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
        error_log('Empty email or password submitted.');
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare('SELECT id, email, password FROM users WHERE email = ?');
        if ($stmt === false) {
            $error = 'Database error: Unable to prepare statement.';
            error_log('Prepare statement failed: ' . $conn->error);
        } else {
            $stmt->bind_param('s', $email);
            if (!$stmt->execute()) {
                $error = 'Database error: Query execution failed.';
                error_log('Query execution failed: ' . $stmt->error);
            } else {
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // Log user fetch result
                error_log('User fetch result: ' . ($user ? 'User found' : 'No user found'));

                // Verify user and password (plaintext comparison)
                if ($user && $password === $user['password']) {
                    // Successful login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $success = 'Login successful!';
                    error_log('Login successful for email: ' . $email);
                    session_regenerate_id(true); // Prevent session fixation
                    $stmt->close();
                    $conn->close();
                    header('Location: home.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                    error_log('Invalid login attempt for email: ' . $email);
                }
                $stmt->close();
            }
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
    <title>Journal Admin Portal - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-outfit antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <div class="w-full max-w-md mx-auto p-6">
            <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 sm:p-7">
                    <div class="text-center">
                        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Sign In</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Enter your email and password to sign in!
                        </p>
                        <!-- Display error or success messages -->
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

                                <div>
                                    <div class="flex justify-between items-center">
                                        <label for="password" class="block text-sm mb-2 dark:text-white">Password*</label>
                                        <a class="text-sm text-blue-600 decoration-2 hover:underline font-medium dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" href="forgot-password.php">Forgot password?</a>
                                    </div>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600" required>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <div class="flex">
                                        <input id="remember-me" name="remember-me" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 pointer-events-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                    </div>
                                    <div class="ms-3">
                                        <label for="remember-me" class="text-sm dark:text-white">Keep me logged in</label>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bundle.js"></script>
</body>
</html>