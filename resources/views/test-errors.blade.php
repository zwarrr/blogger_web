<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Error Pages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Test Error Pages</h1>
                <p class="text-gray-600 mb-8 text-center">Click on any error code below to test the custom error pages</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($errors as $code => $description)
                        <a href="{{ route('test.error', $code) }}" 
                           class="block p-6 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border border-blue-200 rounded-lg transition-all duration-300 transform hover:scale-105">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-blue-800 mb-2">{{ $code }}</h3>
                                <p class="text-blue-600 text-sm font-medium">{{ $description }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                        <svg class="inline w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Development Only
                    </h3>
                    <p class="text-yellow-700">
                        This testing page is only available in local and testing environments. 
                        In production, these routes will not be accessible for security reasons.
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-300">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>