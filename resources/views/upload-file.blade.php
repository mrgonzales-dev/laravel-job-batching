<!DOCTYPE html>
<html lang="en" x-data="{ fileChosen: false, fileName: '' }" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV File Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Upload CSV File</h1>

        <form action="/upload" method="POST" enctype="multipart/form-data" @submit.prevent="if (!fileChosen) alert('Please select a CSV file!'); else $el.submit()">
            @csrf

            <label class="block mb-4">
                <span class="text-gray-700">Choose CSV file</span>
                <input
                    type="file"
                    name="csvfile"
                    accept=".csv"
                    class="mt-2 block w-full text-sm text-gray-600
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:text-sm file:font-semibold
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100"
                    @change="fileChosen = $event.target.files.length > 0; fileName = $event.target.files[0]?.name || ''"
                >
            </label>

            <template x-if="!fileChosen">
                <p class="text-red-600 text-sm mb-4">No file selected.</p>
            </template>

            <template x-if="fileChosen">
                <p class="text-green-600 text-sm mb-4">Selected: <span x-text="fileName"></span></p>
            </template>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg text-lg font-medium hover:bg-blue-700 transition duration-200"
            >
                Upload
            </button>
        </form>
    </div>

</body>
</html>
