<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>403 Forbidden</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700,800&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      overflow: hidden;
      background-color: #fff;
      color: #dc2626; /* merah untuk forbidden */
    }
    @keyframes fadeInDown {
      0% { opacity: 0; transform: translateY(-50px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
      animation: fadeInDown 1s ease-out forwards;
    }
    .glow {
      color: #dc2626;
      text-shadow:
        0 0 10px rgba(220, 38, 38, 0.7),
        0 0 20px rgba(220, 38, 38, 0.6),
        0 0 30px rgba(220, 38, 38, 0.5);
    }
    canvas#particle-canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      pointer-events: none;
      z-index: 0;
    }
    main.content {
      position: relative;
      z-index: 10;
      text-shadow: 0 0 3px rgba(220, 38, 38, 0.4);
    }
    a {
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    }
  </style>
</head>
<body class="flex items-center justify-center h-screen w-screen">

  <!-- Canvas untuk partikel -->
  <canvas id="particle-canvas"></canvas>

  <main class="content text-center px-4 animate-fade-in-down">
    <h1 id="glitch-text" class="text-[140px] md:text-[180px] font-extrabold glow mb-4">403</h1>
    <p id="glitch-para" class="text-2xl md:text-3xl font-light mb-8 text-gray-700">Access Denied! Kamu tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="/" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-8 py-3 rounded-full shadow-xl transition-all duration-300 transform hover:scale-105">
      Back to Home
    </a>
  </main>

  <script>
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas.getContext('2d');

    let W = canvas.width = window.innerWidth;
    let H = canvas.height = window.innerHeight;

    const particles = [];
    const particleCount = 80;

    function initParticles() {
      for (let i = 0; i < particleCount; i++) {
        particles.push(createParticle());
      }
    }

    function createParticle() {
      return {
        x: Math.random() * W,
        y: Math.random() * H,
        size: Math.random() * 4 + 1,
        speedX: (Math.random() - 0.5) * 1.5,
        speedY: (Math.random() - 0.5) * 1.5,
        alpha: Math.random() * 0.5 + 0.3,
        color: `rgba(220, 38, 38, ${Math.random() * 0.5 + 0.3})` // warna merah transparan
      };
    }

    function updateParticles() {
      for (let p of particles) {
        p.x += p.speedX;
        p.y += p.speedY;

        if (p.x < 0 || p.x > W || p.y < 0 || p.y > H) {
          p.x = Math.random() * W;
          p.y = Math.random() * H;
          p.speedX = (Math.random() - 0.5) * 1.5;
          p.speedY = (Math.random() - 0.5) * 1.5;
        }
      }
    }

    function drawParticles() {
      ctx.clearRect(0, 0, W, H);
      for (let p of particles) {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.fill();
      }
    }
    
    function loop() {
      updateParticles();
      drawParticles();
      requestAnimationFrame(loop);
    }

    window.addEventListener('resize', () => {
      W = canvas.width = window.innerWidth;
      H = canvas.height = window.innerHeight;
    });

    initParticles();
    loop();
  </script>
</body>
</html>