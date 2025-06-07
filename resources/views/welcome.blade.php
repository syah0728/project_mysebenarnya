<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MySebenarnya System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('image/favicon.ico') }}" type="image/x-icon" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-hover {
            transition: transform 0.3s ease-in-out;
        }

        .card-hover:hover {
            transform: translateY(-10px);
        }

        .main-content {
            min-height: 100vh;
            padding-top: 4rem;
            position: relative;
            overflow: hidden;
        }

        #orb-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .backdrop-blur-md {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .bg-gradient-animate {
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes bounceSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-bounce-slow {
            animation: bounceSlow 4s infinite;
        }

        @keyframes pulseSlow {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.4; }
        }

        .animate-pulse-slow {
            animation: pulseSlow 6s infinite;
        }

        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body class="antialiased bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <img src="{{ asset('image/MCMC.png.webp') }}" class="h-20 w-20 object-contain" />
                        <span class="ml-2 text-xl font-bold gradient-text">MySebenarnya</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg hover:scale-105 hover:rotate-1 transition duration-300">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg hover:scale-105 hover:rotate-1 transition duration-300">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-gradient-to-r from-purple-500 to-pink-600 text-white px-6 py-2 rounded-full hover:shadow-lg hover:scale-105 hover:rotate-1 transition duration-300">
                                    <i class="fas fa-user-plus mr-2"></i>Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 py-20 overflow-hidden">
            <canvas id="orb-canvas"></canvas>

            <!-- Floating Icons -->
            <i class="fas fa-shield-alt text-white text-6xl absolute top-20 left-20 animate-bounce-slow opacity-20"></i>
            <i class="fas fa-bullhorn text-white text-5xl absolute bottom-16 right-24 animate-pulse-slow opacity-20"></i>

            <div class="relative z-10 text-center px-4 w-full max-w-6xl mx-auto" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white drop-shadow-[0_2px_15px_rgba(255,255,255,0.5)]">Welcome MySebenarnya System</h1>
                <p class="text-xl mb-8 text-white/90">Verifying the truth behind every headline.</p>
                <a href="#about" class="inline-block mt-4 px-8 py-3 bg-white text-purple-600 font-semibold rounded-full shadow-lg hover:bg-gray-100 transition duration-300">
                    Learn More
                </a>
            </div>
        </div>

        <!-- About Section -->
        <section id="about" class="py-20 bg-gray-100 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4" data-aos="fade-up">What is MySebenarnya?</h2>
            <p class="max-w-2xl mx-auto text-gray-600" data-aos="fade-up" data-aos-delay="200">
                MySebenarnya is your trusted platform to verify the authenticity of news and information, ensuring Malaysians stay informed with facts.
            </p>
        </section>

        <!-- Parallax Section -->
        <section class="parallax h-96 flex items-center justify-center bg-[url('/image/parallax-bg.jpg')]">
            <h2 class="text-black text-4xl font-bold drop-shadow-xl" data-aos="zoom-in">Stay Informed. Stay Smart.</h2>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
            <p class="mb-2">&copy; 2025 MySebenarnya. All rights reserved.</p>
            
        </div>
    </footer>

    <!-- Orb Canvas Animation Script -->
    <script>
        (() => {
            const canvas = document.getElementById('orb-canvas');
            const ctx = canvas.getContext('2d');
            let width, height;

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }

            window.addEventListener('resize', resize);
            resize();

            class Orb {
                constructor() {
                    this.reset();
                }
                reset() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.radius = 10 + Math.random() * 20;
                    this.color = `hsl(${Math.floor(Math.random() * 360)}, 80%, 70%)`;
                    this.speedX = (Math.random() - 0.5) * 0.4;
                    this.speedY = (Math.random() - 0.5) * 0.4;
                    this.opacity = 0.15 + Math.random() * 0.3;
                }
                draw() {
                    const gradient = ctx.createRadialGradient(this.x, this.y, this.radius * 0.1, this.x, this.y, this.radius);
                    gradient.addColorStop(0, `rgba(255, 255, 255, ${this.opacity})`);
                    gradient.addColorStop(1, `rgba(255, 255, 255, 0)`);

                    ctx.beginPath();
                    ctx.fillStyle = this.color;
                    ctx.shadowColor = this.color;
                    ctx.shadowBlur = 20;
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.beginPath();
                    ctx.fillStyle = gradient;
                    ctx.arc(this.x, this.y, this.radius * 1.5, 0, Math.PI * 2);
                    ctx.fill();
                }
                update(mouse) {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x < this.radius || this.x > width - this.radius) this.speedX *= -1;
                    if (this.y < this.radius || this.y > height - this.radius) this.speedY *= -1;

                    if (mouse.x && mouse.y) {
                        const dx = this.x - mouse.x;
                        const dy = this.y - mouse.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 100) {
                            const angle = Math.atan2(dy, dx);
                            const repelForce = (100 - dist) / 100 * 2;
                            this.speedX += Math.cos(angle) * repelForce;
                            this.speedY += Math.sin(angle) * repelForce;
                        }
                    }

                    this.speedX *= 0.95;
                    this.speedY *= 0.95;
                }
            }

            const orbs = [];
            const orbCount = 30;
            for (let i = 0; i < orbCount; i++) {
                orbs.push(new Orb());
            }

            const mouse = { x: null, y: null };
            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            window.addEventListener('mouseout', () => {
                mouse.x = null;
                mouse.y = null;
            });

            function animate() {
                ctx.clearRect(0, 0, width, height);
                orbs.forEach(orb => {
                    orb.update(mouse);
                    orb.draw();
                });
                requestAnimationFrame(animate);
            }

            animate();
        })();
    </script>

    <!-- AOS Init -->
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>
