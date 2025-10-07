<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Blogger</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    /* Partikel animasi */
    .particle {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      animation: float 10s infinite ease-in-out;
    }

    @keyframes float {
      0% { transform: translateY(0) translateX(0); opacity: 0.7; }
      50% { transform: translateY(-40px) translateX(20px); opacity: 1; }
      100% { transform: translateY(0) translateX(0); opacity: 0.7; }
    }
  </style>
</head>
<body class="bg-[#F5F7FB] flex items-center justify-center min-h-screen px-4">

  <div class="relative bg-white rounded-xl flex overflow-hidden w-full max-w-4xl border border-gray-200">
    
    <!-- Left: Login Form -->
    <div class="w-full md:w-1/2 p-8 lg:p-10 flex flex-col justify-center">
      
      <!-- Logo -->
      <div class="flex items-center gap-2 mb-8">
        <div class="bg-orange-500 w-10 h-10 rounded-full flex items-center justify-center text-white">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="currentColor">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a7.93 7.93 0 01-4.9-1.7l9.2-9.2A8 8 0 0112 20zm5.9-3.3l-9.2-9.2A8 8 0 0117.9 16.7z"/>
          </svg>
        </div>
        <span class="font-semibold text-gray-800 text-lg">Blogger</span>
      </div>

      <!-- Title -->
      <h1 class="text-2xl font-semibold text-gray-900 mb-2">Login</h1>
      <p class="text-gray-500 mb-6">Masuk ke dashboard Blogger</p>

      <!-- Display Errors -->
      @if ($errors->any())
          <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              @foreach ($errors->all() as $error)
                  <p class="text-sm text-red-600">{{ $error }}</p>
              @endforeach
          </div>
      @endif

      <!-- Form -->
      <form class="space-y-4" method="POST" action="{{ route('auth.login.submit') }}">
        @csrf

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-orange-700 mb-1">Email</label>
          <input id="email" name="email" type="email" required
            class="w-full px-4 py-2 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
            placeholder="example@example.com" value="{{ old('email') }}" />
          @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-orange-700 mb-1">Password</label>
          <input id="password" name="password" type="password" required
            class="w-full px-4 py-2 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
            placeholder="********" />
          @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Options -->
        <div class="flex items-center justify-start text-sm mt-4">
          <label class="flex items-center gap-2 text-gray-600">
            <input name="remember" type="checkbox" class="rounded text-orange-600 focus:ring-orange-500">
            Remember me
          </label>
        </div>

        <!-- Login Button -->
        <button type="submit"
          class="w-full py-2 bg-orange-600 text-white rounded-lg font-semibold text-lg shadow-md hover:bg-orange-800 transition">
          Login
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-gray-600">
        Belum memiliki akun?
        <a href="{{ route('auth.register') }}" class="text-orange-700 font-semibold hover:underline">Daftar sebagai Author</a>
      </p>
      
    </div>

    <!-- Right: Orange Panel with Particles -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-orange-500 to-orange-600 items-center justify-center relative overflow-hidden">
      
      <!-- Particles -->
      <div class="particle w-6 h-6 top-10 left-20"></div>
      <div class="particle w-4 h-4 top-40 left-10" style="animation-delay: 2s;"></div>
      <div class="particle w-8 h-8 bottom-20 right-16" style="animation-delay: 4s;"></div>
      <div class="particle w-5 h-5 top-1/3 right-20" style="animation-delay: 6s;"></div>

      <!-- Content -->
      <div class="text-white text-center space-y-3 relative z-10">
        <h2 class="text-3xl font-bold">Blogger</h2>
        <p class="max-w-sm text-orange-100 text-sm">Your trusted platform to manage, create, and grow your projects effortlessly.</p>
      </div>
    </div>
  </div>

</body>
</html>
