<?php 
session_start();
require 'koneksi.php';

// Ambil Buku Terlaris (Limit 4)
$buku_terlaris = query("SELECT * FROM buku ORDER BY id DESC LIMIT 4");

// Ambil ATK (Limit 4)
$atk_terlaris = query("SELECT * FROM atk LIMIT 4");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Jembatan Ilmu Store</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Lato', 'sans-serif'],
                    },
                    colors: {
                        primary: '#003366', 
                        accent: '#F4A261',  
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen overflow-y-scroll">

    <header class="bg-[#FFFFFF] shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        
        <div class="flex items-center">
            <a href="beranda.php">
                <img src="Jembatan Ilmu.png" alt="Logo Jembatan Ilmu" class="h-12 w-auto object-contain">
            </a>
        </div>

        <nav class="hidden md:flex space-x-8 font-semibold text-gray-700">
            <a href="beranda.php" class="hover:text-primary transition duration-300 border-b-2 border-primary">Beranda</a>
            <a href="produk_buku.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Produk Buku</a>
            <a href="atk.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">ATK</a>
            <a href="diskon.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Diskon</a>
            <a href="berita.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Berita</a>
            <a href="tentang_kami.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Tentang Kami</a>
            
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                <a href="admin.php" class="text-red-600 font-bold hover:border-b-2 hover:border-red-600">Admin Panel</a>
            <?php endif; ?>
        </nav>

        <div class="flex items-center space-x-6 text-xl text-primary">
            <div class="relative hidden md:block text-base"> 
                <input type="text" placeholder="Search..." class="border border-gray-300 rounded-full py-1.5 pl-4 pr-10 text-sm w-40 shadow-sm focus:outline-none focus:border-primary">
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            
            <a href="checkout.php" class="relative hover:text-blue-800 transition">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php 
                    $jml_keranjang = isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0;
                    if($jml_keranjang > 0): 
                ?>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    <?php echo $jml_keranjang; ?>
                </span>
                <?php endif; ?>
            </a>

            <?php if(isset($_SESSION['user_login'])): ?>
                <a href="profile.php" class="hover:text-blue-800 transition" title="Profil Saya">
                    <i class="fa-solid fa-user text-black-600"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="hover:text-blue-800 transition" title="Login / Daftar">
                    <i class="fa-regular fa-user"></i>
                </a>
            <?php endif; ?>
            <!-- Burger Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl text-primary hover:text-blue-800 focus:outline-none ml-4">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 py-2 text-center">
    <nav class="flex flex-col space-y-4 items-center font-semibold text-gray-700">
        <a href="beranda.php" class="hover:text-primary transition duration-300">Beranda</a>
        <a href="produk_buku.php" class="hover:text-primary transition duration-300">Produk Buku</a>
        <a href="atk.php" class="hover:text-primary transition duration-300">ATK</a>
        <a href="diskon.php" class="hover:text-primary transition duration-300">Diskon</a>
        <a href="berita.php" class="hover:text-primary transition duration-300">Berita</a>
        <a href="tentang_kami.php" class="hover:text-primary transition duration-300">Tentang Kami</a>
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
            <a href="admin.php" class="text-red-600 font-bold">Admin Panel</a>
        <?php endif; ?>
    </nav>
</div>


    <section class="relative bg-gray-900 h-[500px] overflow-hidden group" id="hero-slider">
        <!-- Slide 1 -->
        <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-100 z-10">
            <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2690&auto=format&fit=crop" 
                 alt="Sampul Toko Buku 1" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40">
            
            <div class="relative container mx-auto px-4 h-full flex flex-col justify-center items-start text-white">
                <h1 class="text-5xl font-bold mb-4">Jembatan Ilmu Store</h1>
                <p class="text-xl mb-8 max-w-2xl">Temukan ribuan buku berkualitas dan perlengkapan kantor terlengkap untuk menunjang produktivitas dan wawasan Anda. Banyak diskon menanti untuk kalian karena toko ATK termurah ada di sini.</p>
                <a href="produk_buku.php" class="bg-primary hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-full transition duration-300 shadow-lg transform hover:scale-105">
                    Belanja Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0">
            <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2690&auto=format&fit=crop" 
                 alt="Koleksi Buku Lengkap" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40">
            
            <div class="relative container mx-auto px-4 h-full flex flex-col justify-center items-start text-white">
                <h1 class="text-5xl font-bold mb-4">Koleksi Buku Terlengkap</h1>
                <p class="text-xl mb-8 max-w-2xl">Dari novel best-seller hingga buku pelajaran dan referensi akademik. Semua tersedia untuk memenuhi rasa ingin tahu dan kebutuhan belajar Anda tanpa harus merogoh kocek dalam.</p>
                <a href="produk_buku.php" class="bg-accent hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 shadow-lg transform hover:scale-105">
                    Jelajahi Koleksi <i class="fa-solid fa-book-open ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0">
            <img src="https://images.unsplash.com/photo-1456735190827-d1262f71b8a3?q=80&w=2690&auto=format&fit=crop" 
                 alt="Alat Tulis Kantor" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40">
            
            <div class="relative container mx-auto px-4 h-full flex flex-col justify-center items-start text-white">
                <h1 class="text-5xl font-bold mb-4">Perlengkapan ATK Terbaik</h1>
                <p class="text-xl mb-8 max-w-2xl">Kami juga menyediakan berbagai alat tulis kantor dengan kualitas terbaik dan harga yang sangat bersahabat. Cocok untuk menunjang pekerjaan kantoran maupun tugas sekolah.</p>
                <a href="atk.php" class="bg-primary hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-full transition duration-300 shadow-lg transform hover:scale-105">
                    Lihat Produk ATK <i class="fa-solid fa-pen ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="absolute bottom-6 left-0 right-0 flex justify-center space-x-3 z-20">
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-100 transition-opacity" onclick="goToSlide(0)"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" onclick="goToSlide(1)"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" onclick="goToSlide(2)"></button>
        </div>
        
        <!-- Prev/Next Controls -->
        <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white p-3 rounded-full z-20 opacity-0 group-hover:opacity-100 transition-all duration-300" onclick="prevSlide()">
            <i class="fa-solid fa-chevron-left text-xl"></i>
        </button>
        <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white p-3 rounded-full z-20 opacity-0 group-hover:opacity-100 transition-all duration-300" onclick="nextSlide()">
            <i class="fa-solid fa-chevron-right text-xl"></i>
        </button>
    </section>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('#hero-slider .slide');
        const dots = document.querySelectorAll('#hero-slider .slider-dot');
        const totalSlides = slides.length;
        let slideInterval;

        function updateSlider() {
            slides.forEach((slide, index) => {
                if(index === currentSlide) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                    dots[index].classList.remove('opacity-50');
                    dots[index].classList.add('opacity-100');
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                    dots[index].classList.remove('opacity-100');
                    dots[index].classList.add('opacity-50');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
            resetInterval();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
            resetInterval();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
            resetInterval();
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 10000); // 10 seconds
        }

        // Init auto-slide
        slideInterval = setInterval(nextSlide, 10000);
    </script>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary">Mengapa Belanja di Sini?</h2>
                <div class="w-20 h-1 bg-accent mx-auto mt-2"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6 border rounded-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-accent mb-4"><i class="fa-solid fa-book-open"></i></div>
                    <h3 class="text-xl font-bold mb-2">Koleksi Terlengkap</h3>
                    <p class="text-gray-600">Dari buku pelajaran, novel best-seller, hingga buku impor langka tersedia di sini.</p>
                </div>
                <div class="p-6 border rounded-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-accent mb-4"><i class="fa-solid fa-truck-fast"></i></div>
                    <h3 class="text-xl font-bold mb-2">Pengiriman Cepat</h3>
                    <p class="text-gray-600">Kami bekerja sama dengan ekspedisi terpercaya agar buku sampai dengan aman.</p>
                </div>
                <div class="p-6 border rounded-lg hover:shadow-xl transition duration-300">
                    <div class="text-5xl text-accent mb-4"><i class="fa-solid fa-tags"></i></div>
                    <h3 class="text-xl font-bold mb-2">Harga Bersahabat</h3>
                    <p class="text-gray-600">Dapatkan harga terbaik dan diskon spesial setiap minggunya.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="produk-buku" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Buku Terlaris</h2>
                    <p class="text-gray-500 mt-1">Pilihan favorit pembaca bulan ini</p>
                </div>
                <a href="produk_buku.php" class="text-primary font-bold hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <?php if(empty($buku_terlaris)): ?>
                <div class="bg-white p-12 text-center rounded-lg border border-dashed border-gray-300">
                    <i class="fa-solid fa-book-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600">Buku tidak ditemukan</h3>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($buku_terlaris as $item): ?>
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
                            
                            <div class="relative h-60 bg-gray-100 overflow-hidden rounded-t-lg">
                                <img src="<?php echo $item['cover']; ?>" alt="<?php echo $item['judul']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur text-primary text-xs font-bold px-2 py-1 rounded shadow">
                                    <?php echo $item['kategori']; ?>
                                </div>
                            </div>

                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 line-clamp-2 leading-snug mb-1">
                                    <?php echo $item['judul']; ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-3"><?php echo $item['penulis']; ?></p>
                                
                                <div class="mt-auto pt-3 border-t border-gray-100">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-primary font-bold text-lg">
                                            Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                    
                                    <a href="detail_buku.php?id=<?php echo $item['id']; ?>" class="block w-full text-center bg-gray-100 hover:bg-primary text-primary hover:text-white font-bold py-2 rounded transition duration-300">
                                        Selengkapnya <i class="fa-solid fa-arrow-right ml-1 text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-12 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Promo Tengah Semester!</h2>
            <p class="text-lg mb-8">Dapatkan potongan harga hingga <strong>50%</strong> untuk buku pelajaran dan alat tulis pilihan.</p>
            <a href="diskon.php" class="bg-white text-primary font-bold py-3 px-10 rounded-full hover:bg-gray-200 transition duration-300 shadow-lg">
                Lihat Promo <i class="fa-solid fa-tag ml-1"></i>
            </a>
        </div>
    </section>

    <section id="atk" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Alat Tulis Kantor (ATK)</h2>
                    <p class="text-gray-500 mt-1">Lengkapi kebutuhan kerja dan belajarmu</p>
                </div>
                <a href="atk.php" class="text-primary font-bold hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <?php if(empty($atk_terlaris)): ?>
                <div class="bg-white p-12 text-center rounded-lg border border-dashed border-gray-300">
                    <i class="fa-solid fa-pen-ruler text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600">ATK tidak ditemukan</h3>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($atk_terlaris as $item): ?>
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
                            
                            <div class="relative h-60 bg-gray-100 overflow-hidden rounded-t-lg">
                                <img src="<?php echo $item['cover']; ?>" alt="<?php echo $item['nama_atk']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur text-primary text-xs font-bold px-2 py-1 rounded shadow">
                                    <?php echo $item['jenis_atk']; ?>
                                </div>
                            </div>

                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 line-clamp-2 leading-snug mb-1">
                                    <?php echo $item['nama_atk']; ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-3">Merk: <?php echo $item['merk']; ?></p>
                                
                                <div class="mt-auto pt-3 border-t border-gray-100">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-primary font-bold text-lg">
                                            Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                    
                                    <a href="detail_atk.php?id=<?php echo $item['id_atk']; ?>" class="block w-full text-center bg-gray-100 hover:bg-primary text-primary hover:text-white font-bold py-2 rounded transition duration-300">
                                        Selengkapnya <i class="fa-solid fa-arrow-right ml-1 text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Apa Kata Mereka?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md italic relative flex flex-col h-full">
                    <i class="fa-solid fa-quote-left text-4xl text-gray-200 absolute top-4 left-4"></i>
                    <p class="text-gray-600 mb-4 z-10 relative">"Buku yang saya cari susah banget dapetnya di toko lain, eh ternyata di Jembatan Ilmu ada. Pengirimannya juga cepet banget!"</p>
                    <div class="flex items-center mt-auto">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-gray-300">
                            <img src="https://i.pinimg.com/736x/b1/12/82/b11282ad1217bd3dc55b84a22ccaedfb.jpg" alt="Feri" class="w-full h-full object-cover">
                        </div>
                        <div class="ml-3">
                            <h5 class="font-bold text-sm">Feri Adi Prasetya</h5>
                            <p class="text-xs text-gray-500">Mahasiswa</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md italic relative flex flex-col h-full">
                    <i class="fa-solid fa-quote-left text-4xl text-gray-200 absolute top-4 left-4"></i>
                    <p class="text-gray-600 mb-4 z-10 relative">"Suka banget sama pelayanan adminnya yang ramah. Packaging bukunya aman, pake bubble wrap tebal. Recommended!"</p>
                    <div class="flex items-center mt-auto">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-gray-300">
                            <img src="https://i.pinimg.com/736x/56/94/a5/5694a5be1c828189658c63917ca5dd04.jpg" alt="Siti" class="w-full h-full object-cover">
                        </div>
                        <div class="ml-3">
                            <h5 class="font-bold text-sm">Siti Maulia Erlin Vettasia</h5>
                            <p class="text-xs text-gray-500">Ibu Rumah Tangga</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md italic relative flex flex-col h-full">
                    <i class="fa-solid fa-quote-left text-4xl text-gray-200 absolute top-4 left-4"></i>
                    <p class="text-gray-600 mb-4 z-10 relative">"Lengkap banget buat kebutuhan kantor. Sekali belanja langsung borong pulpen sama kertas rim. Diskonnya lumayan. Terimakasih Jembatan Ilmu Store udah kasih layanan terbaik buat para pelanggan."</p>
                    <div class="flex items-center mt-auto">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-gray-300">
                            <img src="https://i.pinimg.com/1200x/d2/09/04/d2090492292eccac661bc557855c4003.jpg" alt="Rhadix" class="w-full h-full object-cover">
                        </div>
                        <div class="ml-3">
                            <h5 class="font-bold text-sm">Melandri Rhadix Ardiansyah</h5>
                            <p class="text-xs text-gray-500">Karyawan Swasta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-[#003366] text-white pt-12 pb-6">
        <div class="container mx-auto px-4 text-center md:text-left">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 text-center md:text-left">
                
                <div class="col-span-1 md:col-span-1">
                    <div class="bg-white p-2 w-fit rounded mb-4 mx-auto md:mx-0">
                        <img src="Jembatan Ilmu.png" alt="Logo Jembatan Ilmu" class="h-10 w-auto">
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Jembatan Ilmu Store berkomitmen mencerdaskan bangsa dengan menyediakan buku berkualitas dan alat tulis terbaik dengan harga terjangkau.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Tautan</h3>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="#" class="hover:text-white hover:underline">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Konfirmasi Pembayaran</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Cek Resi</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Kategori Populer</h3>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="#" class="hover:text-white hover:underline">Buku Pelajaran</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Novel & Sastra</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Komik</a></li>
                        <li><a href="#" class="hover:text-white hover:underline">Perlengkapan Kantor</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Hubungi Kami</h3>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li class="flex items-center justify-center md:justify-start gap-2">
                            <i class="fa-solid fa-location-dot mt-1"></i>
                            <span>Jl. Pendidikan No. 123, Kota Pelajar, Indonesia</span>
                        </li>
                        <li class="flex items-center justify-center md:justify-start gap-2">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center justify-center md:justify-start gap-2">
                            <i class="fa-solid fa-envelope"></i>
                            <span>halo@jembatanilmu.com</span>
                        </li>
                    </ul>
                    <div class="flex justify-center md:justify-start space-x-4 mt-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-white text-[#003366] flex items-center justify-center hover:bg-gray-200"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white text-[#003366] flex items-center justify-center hover:bg-gray-200"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white text-[#003366] flex items-center justify-center hover:bg-gray-200"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <hr class="border-gray-600 mb-6">

            <div class="text-center text-sm text-gray-400">
                &copy; 2024 Jembatan Ilmu Store. All Rights Reserved.
            </div>
        </div>
    </footer>


<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        const menu = document.getElementById('mobile-menu');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    });
</script>
</body>

</html>