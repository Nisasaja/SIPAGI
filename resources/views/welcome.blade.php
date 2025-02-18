<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SIPAGI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="icon" href="{{ asset('asset/image/LOGO.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('asset/css/welcome.css') }}">
</head>
<body>
    <!-- Navbar -->
    <nav class="fixed w-full bg-white shadow-md z-10">
        <div class="max-w-6xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo Section -->
            <div class="flex items-center space-x-4">
                <img src="{{ asset('asset/image/LOGO.png') }}" alt="Logo SIPAGI" class="h-12">
                <img src="{{ asset('asset/image/PT PKN.png') }}" alt="Logo PKN" class="h-12">
                <img src="{{ asset('asset/image/PENS.png') }}" alt="Logo PENS" class="h-12">
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-4">
                {{--  <a href="#stats" class="px-4 py-2 font-semibold text-gray-700 hover:text-blue-500">Statistik</a>
                <a href="#features" class="px-4 py-2 font-semibold text-gray-700 hover:text-blue-500">Fitur</a>  --}}
                <a href="/login" class="btn-primary">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="carousel-container relative">
        <div class="carousel-slide bg-cover bg-center active" style="background-image: url('asset/image/4.png');"></div>
        <div class="carousel-slide bg-cover bg-center" style="background-image: url('asset/image/posyandu.jpeg');"></div>
        <div class="carousel-slide bg-cover bg-center" style="background-image: url('asset/image/Foto Bersama.png');"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="hero-text">
                <h1>Selamat Datang di SIPAGI</h1>
                <p class="text-lg text-gray-600 mb-6">Aplikasi Pemantauan Status Gizi Balita Terpercaya.</p>
                <a href="/login" class="btn-primary">Masuk Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16">
        <div class="container mx-auto text-center">
            <h3 class="text-2xl font-bold">Data Balita Saat Ini</h3>
            <br><br>
            <p class="text-gray-600 mb-6">Untuk melihat data lebih lanjut, silakan <a href="/login" class="text-blue-500 font-semibold">login</a> terlebih dahulu.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="stats-card">
                    <div class="text-center text-blue-500">
                        <i class="fas fa-child text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Jumlah Balita</h3>
                    <p class="text-xl font-bold">{{ $data['totalToddlers'] }}</p>
                </div>
                <div class="stats-card">
                    <div class="text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Balita Teridentifikasi Stunting</h3>
                    <p class="text-xl font-bold">{{ $data['stuntingCount'] }}</p>
                </div>
                <div class="stats-card">
                    <div class="text-center text-green-500">
                        <i class="fas fa-smile-beam text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Balita TB Normal</h3>
                    <p class="text-xl font-bold">{{ $data['healthyCount'] }}</p>
                </div>
                <div class="stats-card">
                    <div class="text-center text-yellow-500">
                        <i class="fas fa-frown text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Balita Gizi Buruk</h3>
                    <p class="text-xl font-bold">{{ $data['malNutrition2'] }}</p>
                </div>
                <div class="stats-card">
                    <div class="text-center text-yellow-500">
                        <i class="fas fa-chart-line text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Balita Gizi Kurang</h3>
                    <p class="text-xl font-bold">{{ $data['malNutrition'] }}</p>
                </div>
                <div class="stats-card">
                    <div class="text-center text-red-500">
                        <i class="fas fa-heart text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold">Balita Gizi Normal</h3>
                    <p class="text-xl font-bold">{{ $data['goodNutrition'] }}</p>
                </div>
            </div>
        </div>
    </section>
       

    <!-- Features Section -->
    <section id="features" class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-10 text-gray-700">Fitur Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="stats-card">
                    <h3 class="text-xl font-semibold text-blue-600">Pemantauan Mudah</h3>
                    <p>Kemudahan akses data gizi balita dalam satu platform.</p>
                </div>
                <div class="stats-card">
                    <h3 class="text-xl font-semibold text-blue-600">Laporan Terstruktur</h3>
                    <p>Data dan laporan yang lengkap untuk analisis.</p>
                </div>
                <div class="stats-card">
                    <h3 class="text-xl font-semibold text-blue-600">Akses Fleksibel</h3>
                    <p>Bisa diakses kapan saja dan di mana saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-6">
        <p class="text-sm">&copy; 2025 SIPAGI. All rights reserved.</p>
    </footer>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) slide.classList.add('active');
            });
        }
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 4000);
    </script>
</body>
</html>
