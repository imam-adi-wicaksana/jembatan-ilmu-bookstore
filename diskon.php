<?php 
session_start();
include 'koneksi.php'; // Sambungkan ke database

// Ambil data diskon yang statusnya 'Aktif'
$query = mysqli_query($conn, "SELECT * FROM diskon WHERE status='Aktif' ORDER BY id DESC");
$vouchers = [];
while($row = mysqli_fetch_assoc($query)){
    $vouchers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo & Voucher - Jembatan Ilmu Store</title>
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
            <a href="diskon.php" class="hover:text-primary transition duration-300 border-b-2 border-primary">Diskon</a>
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
            <h1 class="text-3xl font-bold text-primary text-center">Diskon Harga</h1>
            <p class="text-gray-500 mt-1 text-center">Temukan berbagai potongan harga dari kami</p>
        </div>

        <?php if(empty($vouchers)): ?>
    <div class="col-span-full text-center py-16 bg-white rounded-2xl shadow-sm border border-dashed border-gray-300">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
            <i class="fa-solid fa-ticket text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-700">Belum Ada Voucher</h3>
        <p class="text-gray-500 mt-2">Nantikan promo menarik lainnya segera!</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($vouchers as $item): ?>
            <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col sm:flex-row overflow-hidden border border-gray-100 relative h-full">
                
                <div class="p-6 flex-1 flex flex-col justify-between relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-red-100 text-red-600 text-[10px] font-bold uppercase tracking-wider rounded-md">Limited</span>
                            <span class="text-xs text-gray-400 font-medium">Voucher Belanja</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-800 mb-1">
                            Rp <?php echo number_format($item['potongan'], 0, ',', '.'); ?>
                        </h3>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed mb-4">
                            <?php echo $item['deskripsi']; ?>
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-50 w-fit px-3 py-1.5 rounded-lg border border-gray-200">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        Min. Belanja: <span class="font-bold text-gray-700">Rp <?php echo number_format($item['min_belanja'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="relative hidden sm:flex flex-col items-center justify-center w-6">
                    <div class="h-full border-l-2 border-dashed border-gray-200"></div>
                    <div class="absolute -top-3 w-6 h-6 bg-gray-50 rounded-full z-20"></div>
                    <div class="absolute -bottom-3 w-6 h-6 bg-gray-50 rounded-full z-20"></div>
                </div>

                <div class="bg-gradient-to-br from-blue-600 to-blue-800 sm:w-48 p-6 flex flex-col justify-center items-center text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                    
                    <p class="text-blue-100 text-xs font-medium mb-2">Gunakan Kode:</p>
                    <div class="w-full bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg p-2 mb-4">
                        <p class="font-mono font-bold text-white text-lg tracking-widest select-all">
                            <?php echo $item['kode_diskon']; ?>
                        </p>
                    </div>
                    
                    <a href="checkout.php?kode=<?php echo $item['kode_diskon']; ?>" class="w-full bg-white text-blue-700 font-bold py-2 rounded-lg hover:bg-yellow-400 hover:text-blue-900 transition shadow-lg transform active:scale-95 text-sm">
                        Pakai Sekarang
                    </a>
                </div>

            </div>
            <?php endforeach; ?>
    </div>
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