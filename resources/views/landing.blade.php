<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - Sistem Inventaris</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes highlight {

            0%,
            100% {
                background-color: #fde047;
                color: #262626;
            }

            50% {
                background-color: #eab308;
                color: #fff;
            }
        }

        .highlight-animate {
            animation: highlight 1.5s infinite;
            transition: color .3s, background .3s;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-900 text-gray-100 font-sans">

    <!-- HEADER -->
    <header class="bg-gray-800 shadow-md py-4 px-6 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-2xl font-extrabold text-green-400 tracking-wide animate-pulse">SIMPA</h1>
    </header>

    <!-- HERO SECTION -->
    <section id="hero"
        class="flex flex-col md:flex-row items-center justify-between px-8 py-20 bg-gradient-to-l from-gray-800 to-gray-900">
        <div class="md:w-1/2 text-center md:text-left space-y-6" data-aos="fade-right" data-aos-duration="1000">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-100 leading-tight">
                Kelola Inventaris.<br>
                Pinjam Mudah.<br>
                <span
  class="inline-block px-4 py-1 highlight-animate font-extrabold rounded leading-tight shadow"
  style="box-decoration-break: clone;"
>
  Efisien
</span> untuk SMK 3 Yogyakarta.
            </h2>
            <p class="text-gray-300 text-lg mt-4">Sistem Informasi Manajemen Peminjaman Alat & Persediaan <b>SIMPA</b> –
                digital, cepat, akurat.</p>
            <a href="/admin"
                class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition transform hover:scale-105 duration-300 shadow-lg mt-6">Masuk
                Admin</a>
        </div>

        <div class="md:w-1/2 mt-10 md:mt-0 flex justify-center" data-aos="fade-left" data-aos-duration="1000">
            <img src="{{ asset('img/hero.png') }}" alt="Inventaris"
                class="w-full max-w-md mx-auto drop-shadow-2xl transform hover:scale-105 transition duration-500">
            <!-- Ganti src dengan hero kamu: svg/animasi/Lottie/gif -->
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="mt-auto bg-gray-800 text-center text-sm text-gray-400 py-4 border-t border-gray-700">
        &copy; {{ date('Y') }} SIMPA - SMK Negeri 3. All rights reserved.
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
