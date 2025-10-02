<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Realistis Blogger Loading</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Spin rotasi */
    @keyframes rotate {
      100% {
        transform: rotate(360deg);
      }
    }

    /* Dash animasi stroke */
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

    .spinner-circle {
      animation:
        dash 1.5s ease-in-out infinite,
        rotate 2s linear infinite;
      transform-origin: center;
    }

    #loadingOverlay {
      transition: opacity 0.5s ease;
    }
    #loadingOverlay.hidden {
      opacity: 0;
      pointer-events: none;
    }
  </style>
</head>

  <!-- Loading Fullscreen -->
  <div id="loadingOverlay" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black bg-opacity-80">
    <svg class="w-24 h-24 text-[#FF5722]" viewBox="0 0 50 50">
      <circle
        class="spinner-circle"
        cx="25"
        cy="25"
        r="20"
        fill="none"
        stroke="currentColor"
        stroke-width="4"
        stroke-linecap="round"
      />
    </svg>
    <p class="mt-5 text-[#FFCCBC] text-lg font-semibold tracking-wide animate-pulse select-none">loading......</p>
  </div>

  <script>
    // Loading selesai simulasi 3 detik
    window.addEventListener('load', () => {
      setTimeout(() => {
        document.getElementById('loadingOverlay').classList.add('hidden');
        document.getElementById('mainContent').classList.remove('hidden');
      }, 3000);
    });
  </script>

</body>
</html>
