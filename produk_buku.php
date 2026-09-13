<?php
session_start();
require 'koneksi.php';

$where = "WHERE 1=1";

if (isset($_GET['kategori'])) {
    $filter_kategori = $_GET['kategori'];
    
    if (!empty($filter_kategori)) {
        $kategori_sql = implode("','", $filter_kategori);
        $where .= " AND kategori IN ('$kategori_sql')";
    }
} else {
    $filter_kategori = [];
}

$min = isset($_GET['min_harga']) && $_GET['min_harga'] != '' ? $_GET['min_harga'] : 0;
$max = isset($_GET['max_harga']) && $_GET['max_harga'] != '' ? $_GET['max_harga'] : 999999999;

$where .= " AND harga BETWEEN $min AND $max";

$buku_tampil = query("SELECT * FROM buku $where");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Jembatan Ilmu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="produk_buku.php" class="hover:text-primary transition duration-300 border-b-2 border-primary">Produk Buku</a>
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

<main class="container mx-auto px-4 py-8 flex-grow">
    
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-primary text-center">Katalog Buku</h1>
        <p class="text-gray-500 mt-1 text-center">Temukan buku favoritmu di sini</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        
        <aside class="w-full md:w-1/4 h-fit">
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 sticky top-24">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg"><i class="fa-solid fa-filter mr-2"></i> Filter</h3>
        </div>
        <form action="" method="GET">
    <div class="mb-6">
        <h4 class="font-bold text-sm text-gray-600 mb-3 uppercase tracking-wider">Kategori</h4>
        <div class="space-y-2 text-sm">
            
            <label class="flex items-center space-x-2 cursor-pointer hover:text-primary transition">
                <input type="checkbox" name="kategori[]" value="Novel" class="rounded text-primary focus:ring-primary" 
                <?php echo (in_array('Novel', $filter_kategori)) ? 'checked' : ''; ?>>
                <span>Novel</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer hover:text-primary transition">
                <input type="checkbox" name="kategori[]" value="Self Improvement" class="rounded text-primary focus:ring-primary" 
                <?php echo (in_array('Self Improvement', $filter_kategori)) ? 'checked' : ''; ?>>
                <span>Self Improvement</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer hover:text-primary transition">
                <input type="checkbox" name="kategori[]" value="Pelajaran" class="rounded text-primary focus:ring-primary" 
                <?php echo (in_array('Pelajaran', $filter_kategori)) ? 'checked' : ''; ?>>
                <span>Buku Pelajaran</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer hover:text-primary transition">
                <input type="checkbox" name="kategori[]" value="Sastra" class="rounded text-primary focus:ring-primary" 
                <?php echo (in_array('Sastra', $filter_kategori)) ? 'checked' : ''; ?>>
                <span>Sastra Klasik</span>
            </label>
        </div>
    </div>

    <hr class="border-gray-200 mb-6">

    <div class="mb-6">
        <h4 class="font-bold text-sm text-gray-600 mb-3 uppercase tracking-wider">Rentang Harga</h4>
        
        <div class="grid grid-cols-2 gap-2">
            <div class="relative">
                <span class="absolute left-2 top-2 text-gray-400 text-xs">Rp</span>
                <input type="number" name="min_harga" placeholder="Min" 
                       value="<?php echo isset($_GET['min_harga']) ? $_GET['min_harga'] : ''; ?>"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 pl-7 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="relative">
                <span class="absolute left-2 top-2 text-gray-400 text-xs">Rp</span>
                <input type="number" name="max_harga" placeholder="Max" 
                       value="<?php echo isset($_GET['max_harga']) ? $_GET['max_harga'] : ''; ?>"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 pl-7 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <button type="submit" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-blue-800 transition duration-300 shadow-md text-sm">
            Terapkan Filter
        </button>

        <a href="produk_buku.php" class="w-full bg-gray-200 text-gray-700 font-bold py-2 rounded hover:bg-gray-300 transition duration-300 text-center text-sm">
            Reset Filter
        </a>
    </div>

</form>
    </div>
</aside>

        <section class="w-full md:w-3/4">  
            <?php if(empty($buku_tampil)): ?>
                <div class="bg-white p-12 text-center rounded-lg border border-dashed border-gray-300">
                    <i class="fa-solid fa-book-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600">Buku tidak ditemukan</h3>
                    <p class="text-gray-400 mt-2">Coba ganti filter kategori atau rentang harga.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($buku_tampil as $item): ?>
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
        </section>

    </div>
</main>

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