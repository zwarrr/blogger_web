<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register Author | Blogger</title>
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
<body class="bg-[#F5F7FB] flex items-center justify-center min-h-screen px-4 py-8">

  <div class="relative bg-white rounded-xl flex overflow-hidden w-full max-w-4xl max-h-[90vh] border border-gray-200">
    
    <!-- Left: Register Form -->
    <div class="w-full md:w-1/2 p-6 lg:p-8 flex flex-col justify-center overflow-y-auto">
      
      <!-- Logo -->
      <div class="flex items-center gap-2 mb-6">
        <div class="bg-orange-500 w-8 h-8 rounded-full flex items-center justify-center text-white">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a7.93 7.93 0 01-4.9-1.7l9.2-9.2A8 8 0 0112 20zm5.9-3.3l-9.2-9.2A8 8 0 0117.9 16.7z"/>
          </svg>
        </div>
        <span class="font-semibold text-gray-800 text-base">Blogger</span>
      </div>

      <!-- Title -->
      <h1 class="text-xl font-semibold text-gray-900 mb-1">Daftar sebagai Author</h1>
      <p class="text-gray-500 mb-4 text-sm">Bergabunglah dengan platform Blogger sebagai penulis artikel</p>

      <!-- Display Errors -->
      @if ($errors->any())
          <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              @foreach ($errors->all() as $error)
                  <p class="text-sm text-red-600">{{ $error }}</p>
              @endforeach
          </div>
      @endif

      <!-- Form -->
      <form class="space-y-3" method="POST" action="{{ route('auth.register.submit') }}">
        @csrf

        <!-- Grid 2 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-orange-700 mb-1">Email</label>
            <input id="email" name="email" type="email" required
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="example@example.com" value="{{ old('email') }}" />
            @error('email')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Nama Lengkap -->
          <div>
            <label for="name" class="block text-sm font-medium text-orange-700 mb-1">Nama Lengkap</label>
            <input id="name" name="name" type="text" required
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="Nama Lengkap" value="{{ old('name') }}" />
            @error('name')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- No Telepon -->
          <div>
            <label for="phone" class="block text-sm font-medium text-orange-700 mb-1">No Telepon</label>
            <input id="phone" name="phone" type="tel" required
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" />
            @error('phone')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Alamat -->
          <div>
            <label for="address" class="block text-sm font-medium text-orange-700 mb-1">Alamat</label>
            <input id="address" name="address" type="text" required
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="Alamat lengkap..." value="{{ old('address') }}" />
            @error('address')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-orange-700 mb-1">Password</label>
            <input id="password" name="password" type="password" required minlength="8"
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="********" />
            @error('password')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-orange-700 mb-1">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
              class="w-full px-3 py-1.5 rounded-lg border border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm"
              placeholder="********" />
          </div>
        </div>

        <!-- Hidden role field -->
        <input type="hidden" name="role" value="author">

        <!-- Terms Agreement -->
        <div class="flex items-start gap-2 text-xs mt-3">
          <input name="terms" type="checkbox" required class="mt-0.5 rounded text-orange-600 focus:ring-orange-500">
          <label class="text-gray-600">
            Saya menyetujui <a href="#" class="text-orange-600 hover:text-orange-700 underline">syarat dan ketentuan</a> 
            serta <a href="#" class="text-orange-600 hover:text-orange-700 underline">kebijakan privasi</a> platform Blogger
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full py-2 bg-orange-600 text-white rounded-lg font-medium text-base shadow-md hover:bg-orange-800 transition">
          Daftar sebagai Author
        </button>
      </form>

      <p class="mt-4 text-xs text-center text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('auth.login') }}" class="text-orange-700 font-semibold hover:underline">Login</a>
      </p>
      
    </div>

    <!-- Right: Orange Panel with Particles -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-orange-500 to-orange-600 items-center justify-center relative overflow-hidden">
      
      <!-- Particles -->
      <div class="particle w-6 h-6 top-10 left-20"></div>
      <div class="particle w-4 h-4 top-40 left-10" style="animation-delay: 2s;"></div>
      <div class="particle w-8 h-8 bottom-20 right-16" style="animation-delay: 4s;"></div>
      <div class="particle w-5 h-5 top-1/3 right-20" style="animation-delay: 6s;"></div>
      <div class="particle w-3 h-3 top-20 right-32" style="animation-delay: 8s;"></div>

      <!-- Content -->
      <div class="text-white text-center space-y-4 relative z-10 px-6">
        <div class="text-4xl mb-4">✍️</div>
        <h2 class="text-3xl font-bold">Jadilah Author</h2>
        <p class="max-w-sm text-orange-100 text-sm leading-relaxed">
          Bergabunglah dengan komunitas penulis terbaik. Bagikan ide, pengalaman, dan pengetahuan Anda melalui artikel yang menginspirasi.
        </p>
        <div class="mt-6 space-y-2 text-orange-100 text-xs">
          <div class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Dashboard Author yang Mudah
          </div>
          <div class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Manajemen Artikel Lengkap
          </div>
          <div class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Komunitas Penulis Aktif
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Password confirmation script -->
<script>
  (function(){
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const submit = form.querySelector('button[type="submit"]');

    // create error element
    const err = document.createElement('p');
    err.className = 'text-red-500 text-sm mt-1';
    err.style.display = 'none';
    confirm.parentNode.appendChild(err);

    function validate(){
      if (!password.value && !confirm.value){
        err.style.display = 'none';
        submit.disabled = false;
        submit.classList.remove('opacity-50','cursor-not-allowed');
        return;
      }
      if (password.value !== confirm.value){
        err.textContent = 'Password konfirmasi tidak sama.';
        err.style.display = 'block';
        submit.disabled = true;
        submit.classList.add('opacity-50','cursor-not-allowed');
      } else {
        err.style.display = 'none';
        submit.disabled = false;
        submit.classList.remove('opacity-50','cursor-not-allowed');
      }
    }

    password.addEventListener('input', validate);
    confirm.addEventListener('input', validate);

    // final check on submit to prevent bypassing
    form.addEventListener('submit', function(e){
      if (password.value !== confirm.value){
        e.preventDefault();
        validate();
      }
    });
  })();
</script>

</body>
</html>