<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Loading System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Test Loading System</h1>
        
        <div class="space-y-4">
            <p class="text-gray-600">Klik link di bawah untuk menguji loading system:</p>
            
            <div class="space-y-2">
                <a href="{{ route('user.views') }}" class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                    🏠 Kembali ke Beranda (Loading: "Kembali ke beranda...")
                </a>
                
                <a href="{{ route('user.views') }}/posts/POST005" class="block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                    📖 Baca Artikel (Loading: "Memuat artikel...")
                </a>
                
                <a href="/test-loading" class="block bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition-colors">
                    🔄 Reload Halaman Ini (Loading: "Memuat halaman...")
                </a>
            </div>
        </div>
        
        <div class="mt-8 p-4 bg-yellow-50 rounded-lg">
            <h3 class="font-semibold text-yellow-800 mb-2">🎯 Cara Test Loading:</h3>
            <ol class="list-decimal list-inside space-y-1 text-sm text-yellow-700">
                <li>Klik salah satu link di atas</li>
                <li>Loading overlay akan muncul selama ~1 detik</li>
                <li>Halaman akan berpindah setelah loading selesai</li>
                <li>Setiap link memiliki pesan loading yang berbeda</li>
            </ol>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="page-transition" id="pageTransition" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%); z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out; pointer-events: none;">
        <svg class="w-24 h-24 text-orange-500" viewBox="0 0 50 50">
            <circle
                class="spinner-circle"
                cx="25"
                cy="25"
                r="20"
                fill="none"
                stroke="currentColor"
                stroke-width="4"
                stroke-linecap="round"
                style="animation: dash 1.5s ease-in-out infinite, rotate 2s linear infinite; transform-origin: center;"
            />
        </svg>
        <p class="loading-text" style="color: #FFCCBC; font-size: 1.125rem; font-weight: 600; letter-spacing: 0.05em; animation: pulse 2s ease-in-out infinite; user-select: none; margin-top: 1.25rem;">Loading...</p>
    </div>

    <style>
        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 1, 150;
                stroke-dashoffset: 0;
            }
            50% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -35;
            }
            100% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -124;
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        .page-transition.active {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: all !important;
        }
    </style>

    @vite(['resources/js/animations.js'])
</body>
</html>