<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Jembatan Ilmu Store</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Lato', 'sans-serif'] },
                    colors: { primary: '#003366', accent: '#F4A261' }
                }
            }
        }
    </script>
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
            <a href="beranda.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Beranda</a>
            <a href="produk_buku.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Produk Buku</a>
            <a href="atk.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">ATK</a>
            <a href="diskon.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Diskon</a>
            <a href="berita.php" class="hover:text-primary transition duration-300 hover:border-b-2 hover:border-primary">Berita</a>
            <a href="tentang_kami.php" class="hover:text-primary transition duration-300 border-b-2 border-primary">Tentang Kami</a>
            
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
    <nav class="flex flex-col space-y-4 font-semibold text-gray-700 text-center">
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


    <div class="relative bg-gray-900 h-80 flex items-center justify-center text-center px-4">
        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=2670&auto=format&fit=crop" 
             alt="Interior Toko Buku" 
             class="absolute inset-0 w-full h-full object-cover opacity-30">
        
        <div class="relative z-10 text-white max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Membangun Jembatan Ilmu</h1>
            <p class="text-lg md:text-xl font-light">Lebih dari sekadar toko buku, kami adalah teman perjalanan intelektual Anda.</p>
        </div>
    </div>

    <section class="container mx-auto px-4 py-16">
        <div class="flex flex-col md:flex-row items-center gap-12">
            
            <div class="w-full md:w-1/2">
                <div class="relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-accent/20 rounded-full z-0"></div>
                    <img src="https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1000&auto=format&fit=crop" 
                         alt="Suasana Membaca" 
                         class="relative z-10 rounded-lg shadow-xl w-full object-cover h-[400px]">
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-primary/10 rounded-full z-0"></div>
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <h4 class="text-primary font-bold uppercase tracking-widest text-sm mb-2">Sejarah Kami</h4>
                <h2 class="text-3xl font-bold mb-6 text-gray-900">Berawal dari Kecintaan pada Literasi</h2>
                <p class="text-gray-600 mb-4 leading-relaxed text-justify">
                    Jembatan Ilmu Store didirikan pada tahun 2024 dengan satu tujuan sederhana: membuat buku berkualitas mudah diakses oleh semua kalangan. Kami percaya bahwa setiap lembar halaman memiliki kekuatan untuk mengubah pemikiran dan membuka wawasan baru.
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed text-justify">
                    Dimulai dari sebuah garasi kecil dengan koleksi terbatas, kini kami telah melayani ribuan pembaca di seluruh Indonesia. Kami berkomitmen untuk terus menyediakan kurasi buku terbaik, mulai dari literatur klasik, buku pelajaran, hingga karya penulis lokal yang inspiratif.
                </p>
                
                <div class="grid grid-cols-3 gap-4 text-center border-t pt-6">
                    <div>
                        <span class="block text-2xl font-bold text-primary">5000+</span>
                        <span class="text-xs text-gray-500">Judul Buku</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-bold text-primary">10k+</span>
                        <span class="text-xs text-gray-500">Pelanggan Puas</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-bold text-primary">24/7</span>
                        <span class="text-xs text-gray-500">Layanan Online</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-primary text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                <div class="bg-white/10 p-8 rounded-lg backdrop-blur-sm border border-white/20 hover:bg-white/20 transition">
                    <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center text-white text-xl mb-4">
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Visi Kami</h3>
                    <p class="text-gray-200 leading-relaxed">
                        Menjadi toko buku terdepan yang tidak hanya menjual produk, tetapi juga menjadi pusat ekosistem literasi yang mencerdaskan kehidupan bangsa.
                    </p>
                </div>

                <div class="bg-white/10 p-8 rounded-lg backdrop-blur-sm border border-white/20 hover:bg-white/20 transition">
                    <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center text-white text-xl mb-4">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Misi Kami</h3>
                    <ul class="list-disc list-inside text-gray-200 space-y-2">
                        <li>Menyediakan buku original dengan harga terjangkau.</li>
                        <li>Mendukung penulis lokal untuk berkarya.</li>
                        <li>Memberikan pelayanan yang ramah dan solutif.</li>
                        <li>Mengadakan event literasi untuk komunitas.</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <?php
    // Array Data Tim (Contoh penggunaan Array PHP untuk konten repetitif)
    $tim = [
        ["nama" => "Imam", "posisi" => "Founder & CEO", "foto" => "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=400"],
        ["nama" => "Siti Aminah", "posisi" => "Manager Operasional", "foto" => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=400"],
        ["nama" => "Budi Santoso", "posisi" => "Head of Content", "foto" => "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=400"],
        ["nama" => "Rina Wijaya", "posisi" => "Customer Success", "foto" => "https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=400"],
    ];
    ?>

    <section class="container mx-auto px-4 py-16 text-center">
        <h2 class="text-3xl font-bold mb-12 text-gray-900">Di Balik Layar</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach ($tim as $orang): ?>
                <div class="group">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-gray-100 shadow-lg mb-4 group-hover:border-accent transition duration-300">
                        <img src="<?php echo $orang['foto']; ?>" alt="<?php echo $orang['nama']; ?>" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-bold text-lg text-primary"><?php echo $orang['nama']; ?></h4>
                    <p class="text-sm text-gray-500"><?php echo $orang['posisi']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-8 bg-white p-6 rounded-xl shadow-lg">
                
                <div class="w-full md:w-1/3 flex flex-col justify-center p-6">
                    <h3 class="text-2xl font-bold text-primary mb-6">Kunjungi Toko Kami</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h5 class="font-bold">Alamat</h5>
                                <p class="text-gray-600 text-sm">Jl. Pendidikan No. 123, Komplek Pelajar, Kota Magelang, Jawa Tengah 56123</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="font-bold">Jam Operasional</h5>
                                <p class="text-gray-600 text-sm">Senin - Jumat: 08.00 - 20.00 WIB</p>
                                <p class="text-gray-600 text-sm">Sabtu - Minggu: 09.00 - 18.00 WIB</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h5 class="font-bold">Kontak</h5>
                                <p class="text-gray-600 text-sm">Email: halo@jembatanilmu.com</p>
                                <p class="text-gray-600 text-sm">WA: 0812-3456-7890</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-2/3 h-80 rounded-lg overflow-hidden border border-gray-200">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.270634689363!2d110.21776997405102!3d-7.468494673248866!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a85573755979f%3A0xf6398f6d35366432!2sAlun-Alun%20Kota%20Magelang!5e0!3m2!1sid!2sid!4v1705123456789!5m2!1sid!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
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