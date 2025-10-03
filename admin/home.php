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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('Unauthorized access to home.php');
    header('Location: login.php');
    exit;
}

require_once 'connection.php';

// Fetch all journals
$sql = 'SELECT id, user_id, topic, authors, affiliations, published_date, volume, page, abstract, body, pdf_path, citation, created_at FROM journals';
$result = $conn->query($sql);
if ($result === false) {
    error_log('DB query failed: ' . $conn->error);
    die('Database error.');
}

$journals = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Admin Portal - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-outfit antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-6">
            <h2 class="text-xl font-bold mb-4">MENU</h2>
            <ul>
                <li><a href="home.php" class="block py-2 hover:bg-gray-700">Dashboard</a></li>
                <li><a href="add-journal.html" class="block py-2 hover:bg-gray-700">Add Journal</a></li>
                <li><a href="logout.php" class="block py-2 hover:bg-gray-700">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">Journals List</h1>
            <div class="mb-4 flex gap-4">
                <input type="text" id="searchInput" class="py-2 px-4 w-full max-w-md border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600" placeholder="Search by topic, authors, or abstract...">
                <a href="add-journal.html" class="py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Journal</a>
            </div>
            <div class="overflow-x-auto">
                <table id="journalsTable" class="w-full border-collapse border border-gray-200 dark:border-gray-600">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">ID</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">User ID</th>
                            <th class="border border-gray-200 dark:border-gnu-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Topic</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Authors</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Affiliations</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Published Date</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Volume</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Page</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Abstract</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Body</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">PDF Path</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Citation</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Created At</th>
                            <th class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-800 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($journals)): ?>
                            <tr>
                                <td colspan="14" class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-center text-sm text-gray-600 dark:text-gray-400">No journals found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($journals as $journal): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['id'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['user_id'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['topic'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['authors'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['affiliations'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['published_date'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['volume'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['page'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars(substr($journal['abstract'] ?? '', 0, 100)) . (strlen($journal['abstract'] ?? '') > 100 ? '...' : ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars(substr($journal['body'] ?? '', 0, 100)) . (strlen($journal['body'] ?? '') > 100 ? '...' : ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['pdf_path'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['citation'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($journal['created_at'] ?? ''); ?></td>
                                    <td class="border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm">
                                        <a href="add-journal.html?edit=1&id=<?php echo htmlspecialchars($journal['id']); ?>" class="text-blue-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#journalsTable tbody tr:not(.no-results)');

            rows.forEach(row => {
                const topic = row.cells[2].textContent.toLowerCase();
                const authors = row.cells[3].textContent.toLowerCase();
                const abstract = row.cells[8].textContent.toLowerCase();

                if (topic.includes(searchTerm) || authors.includes(searchTerm) || abstract.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const noResultsRow = document.querySelector('#journalsTable tbody tr.no-results');
            if (noResultsRow) {
                noResultsRow.style.display = rows.length > 0 && !Array.from(rows).some(row => row.style.display === '') ? '' : 'none';
            }
        });
    </script>
</body>
</html>