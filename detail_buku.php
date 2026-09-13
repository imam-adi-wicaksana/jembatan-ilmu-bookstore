<?php
include 'koneksi.php'; 
session_start();

if (!isset($_SESSION['user_login'])) {
    echo "<script>
            alert('Akses ditolak! Silakan login terlebih dahulu untuk melihat detail.');
            window.location='login.php';
          </script>";
    exit;
}

$id_requested = isset($_GET['id']) ? $_GET['id'] : null;
$buku_detail = null;

if ($id_requested) {
    // Amankan ID dari SQL Injection
    $id_safe = mysqli_real_escape_string($conn, $id_requested);
    
    // QUERY DATABASE (Bukan Array Manual Lagi)
    $query = mysqli_query($conn, "SELECT * FROM buku WHERE id = '$id_safe'");
    
    if (mysqli_num_rows($query) > 0) {
        $buku_detail = mysqli_fetch_assoc($query);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $buku_detail ? $buku_detail['judul'] : 'Buku Tidak Ditemukan'; ?> - Jembatan Ilmu</title>
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

        <?php if (!$buku_detail): ?>
            <div class="text-center py-20">
                <i class="fa-solid fa-circle-exclamation text-6xl text-red-400 mb-4"></i>
                <h2 class="text-2xl font-bold">Buku Tidak Ditemukan</h2>
                <p class="mb-6">Maaf, buku yang Anda cari tidak tersedia.</p>
                <a href="produk_buku.php" class="bg-primary text-white px-6 py-2 rounded hover:bg-blue-800">Kembali ke Katalog</a>
            </div>
        <?php else: ?>

            <nav class="text-sm text-gray-500 mb-6">
                <a href="beranda.php" class="hover:text-primary">Beranda</a> 
                <span class="mx-2">/</span> 
                <a href="produk_buku.php" class="hover:text-primary">Produk Buku</a> 
                <span class="mx-2">/</span> 
                <span class="text-primary font-bold"><?php echo $buku_detail['judul']; ?></span>
            </nav>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden p-6 md:p-8">
                <div class="flex flex-col md:flex-row gap-10">
                    
                    <div class="w-full md:w-1/3 flex-shrink-0">
                        <div class="sticky top-24">
                            <img src="<?php echo $buku_detail['cover']; ?>" alt="<?php echo $buku_detail['judul']; ?>" class="w-full rounded-lg shadow-md border border-gray-200 object-cover">
                        </div>
                    </div>

                    <div class="w-full md:w-2/3">
                        <span class="bg-blue-100 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            <?php echo $buku_detail['kategori']; ?>
                        </span>
                        
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mt-4 mb-2">
                            <?php echo $buku_detail['judul']; ?>
                        </h1>
                        <p class="text-lg text-gray-600 mb-6">
                            Oleh: <span class="font-semibold text-primary"><?php echo $buku_detail['penulis']; ?></span>
                        </p>

                        <div class="flex items-end mb-8 border-b border-gray-100 pb-6">
                            <span class="text-4xl font-bold text-primary">Rp <?php echo number_format($buku_detail['harga'], 0, ',', '.'); ?></span>
                            <span class="text-sm text-green-600 font-bold ml-4 mb-2"><i class="fa-solid fa-check"></i> Stok Tersedia</span>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 border-l-4 border-accent pl-3">Sinopsis Buku</h3>
                            <p class="text-gray-600 leading-relaxed text-justify">
                                <?php echo $buku_detail['sinopsis']; ?>
                            </p>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 border-l-4 border-accent pl-3">Detail Fisik</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-3 bg-gray-50 rounded">
                                    <span class="block text-gray-400 text-xs">Penerbit</span>
                                    <span class="font-semibold"><?php echo $buku_detail['penerbit']; ?></span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded">
                                    <span class="block text-gray-400 text-xs">ISBN</span>
                                    <span class="font-semibold"><?php echo $buku_detail['isbn']; ?></span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded">
                                    <span class="block text-gray-400 text-xs">Jumlah Halaman</span>
                                    <span class="font-semibold"><?php echo $buku_detail['halaman']; ?> Halaman</span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded">
                                    <span class="block text-gray-400 text-xs">Berat</span>
                                    <span class="font-semibold"><?php echo $buku_detail['berat']; ?></span>
                                </div>
                            </div>
                        </div>

                        <?php 
                            $item_data = isset($buku_detail) ? $buku_detail : (isset($atk_detail) ? $atk_detail : null);
                            $nama_item = isset($item_data['judul']) ? $item_data['judul'] : $item_data['nama_atk'];
                        ?>

                        <form method="POST">
                            <input type="hidden" name="nama_barang" value="<?php echo $nama_item; ?>">
                            <input type="hidden" name="harga_satuan" value="<?php echo $item_data['harga']; ?>">
                            <input type="hidden" name="gambar" value="<?php echo $item_data['cover']; ?>">

                            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                                <div class="flex items-center border border-gray-300 rounded-lg w-fit">
                                    <button type="button" onclick="updateQty(-1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input type="number" id="qtyDisplay" name="qty" value="1" min="1" class="w-12 text-center border-none focus:ring-0 appearance-none" readonly>
                                    <button type="button" onclick="updateQty(1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>

                                <button type="submit" formaction="tambah_keranjang.php" class="flex-1 bg-white border-2 border-primary text-primary font-bold py-3 px-6 rounded-lg hover:bg-blue-50 transition">
                                    <i class="fa-solid fa-cart-plus mr-2"></i> Tambah Keranjang
                                </button>
                                
                                <button type="submit" formaction="checkout.php" name="beli_sekarang" class="flex-1 bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-800 shadow-lg transition transform hover:-translate-y-1">
                                    Beli Sekarang
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        <section id="produk-buku" class="mt-1 bg-gray-50 py-12 rounded-xl">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800">Buku Lainnya</h2>
                            <p class="text-gray-500 mt-1">Mungkin Anda juga tertarik dengan ini</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php 
                        // QUERY BUKU LAIN (Random 4 biji, kecuali yg sedang dibuka)
                        $id_safe = mysqli_real_escape_string($conn, $id_requested);
                        $query_lain = mysqli_query($conn, "SELECT * FROM buku WHERE id != '$id_safe' ORDER BY RAND() LIMIT 4");

                        if(mysqli_num_rows($query_lain) > 0):
                            while ($item = mysqli_fetch_assoc($query_lain)): 
                    ?>
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
                            <div class="relative h-60 bg-gray-100 overflow-hidden rounded-t-lg">
                                <img src="<?php echo $item['cover']; ?>" 
                                     alt="<?php echo $item['judul']; ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                     onerror="this.src='https://via.placeholder.com/300x450?text=No+Cover'">
                                     
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
                    <?php 
                            endwhile;
                        else:
                            echo "<p class='col-span-4 text-center text-gray-400'>Belum ada buku lain.</p>";
                        endif;
                    ?>
                    </div>
                </div>
            </section>

        <?php endif; ?>
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
        function updateQty(change) {
            let input = document.getElementById('qtyDisplay');
            let newVal = parseInt(input.value) + change;
            if(newVal < 1) newVal = 1;
            input.value = newVal;
            }
    </script>

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