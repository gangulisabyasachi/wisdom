<!-- add-journal.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Admin Portal - Add Journal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-outfit antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-6">
            <h2 class="text-xl font-bold mb-4">MENU</h2>
            <ul>
                <li><a href="home.html" class="block py-2">Dashboard</a></li>
                <li><a href="add-journal.html" class="block py-2">Add Journal</a></li>
                <!-- Add more menu items if needed -->
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">Add New Journal</h1>
            <form id="journalForm" action="home.html"> <!-- Redirect to home on submit (static) -->
                <div class="grid gap-4">
                    <div>
                        <label for="topic" class="block text-sm mb-2">Topic*</label>
                        <input type="text" id="topic" name="topic" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                    </div>

                    <div>
                        <label for="authors" class="block text-sm mb-2">Author(s)*</label>
                        <input type="text" id="authors" name="authors" placeholder="John Doe, Jane Smith" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                    </div>

                    <div>
                        <label for="affiliations" class="block text-sm mb-2">Affiliations</label>
                        <input type="text" id="affiliations" name="affiliations" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm">
                    </div>

                    <div>
                        <label for="publishedDate" class="block text-sm mb-2">Published Date*</label>
                        <input type="date" id="publishedDate" name="publishedDate" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                    </div>

                    <div>
                        <label for="page" class="block text-sm mb-2">Page</label>
                        <input type="text" id="page" name="page" placeholder="1-10" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm">
                    </div>

                    <div>
                        <label for="volume" class="block text-sm mb-2">Volume</label>
                        <input type="text" id="volume" name="volume" placeholder="Vol. 1" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm">
                    </div>

                    <div>
                        <label for="abstract" class="block text-sm mb-2">Abstract</label>
                        <textarea id="abstract" name="abstract" rows="4" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm"></textarea>
                    </div>

                    <div>
                        <label for="body" class="block text-sm mb-2">Body</label>
                        <textarea id="body" name="body" rows="8" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm"></textarea>
                    </div>

                    <div>
                        <label for="pdf" class="block text-sm mb-2">PDF Upload</label>
                        <input type="file" id="pdf" name="pdf" accept=".pdf" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm mb-2">Auto-generated Citation</label>
                        <p id="citation" class="py-3 px-4 block w-full bg-gray-100 rounded-lg text-sm">Will be generated on submit</p>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">Save Journal</button>
                </div>
            </form>
        </main>
    </div>
    <script src="bundle.js"></script>
    <script>
        document.getElementById('journalForm').addEventListener('submit', function(e) {
            // e.preventDefault(); // Uncomment for real submit later

            // Auto-generate citation
            const authors = document.getElementById('authors').value;
            const topic = document.getElementById('topic').value;
            const date = document.getElementById('publishedDate').value;
            const volume = document.getElementById('volume').value;
            const page = document.getElementById('page').value;
            const citation = `${authors}. (${date}). ${topic}. Journal Name, ${volume}(${page}).`;
            document.getElementById('citation').textContent = citation;

            // Auto-generate unique PDF filename (simulated)
            const pdfInput = document.getElementById('pdf');
            if (pdfInput.files.length > 0) {
                const uniqueName = `journal_${Date.now()}.pdf`;
                console.log('Unique PDF name:', uniqueName); // For demo; in real, handle upload
            }
        });
    </script>
</body>
</html>