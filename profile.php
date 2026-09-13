<?php
session_start();
require 'koneksi.php'; 


// Cek Login
if (!isset($_SESSION['user_login'])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi variabel pesan
$pesan_sukses = "";
$pesan_gagal = "";

// === LOGIKA UPDATE DATABASE ===
if (isset($_POST['update_profil'])) {
    
    // 1. AMBIL ID USER DENGAN AMAN
    $id_user = $_SESSION['user_id'] ?? null; // Cek apakah ID ada di session

    // Jika ID tidak ada di session, cari manual berdasarkan email
    if (!$id_user) {
        $email_sess = $_SESSION['user_email'] ?? '';
        
        // Query cari ID
        $cek_user = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_sess'");
        
        // PENTING: Cek apakah datanya ketemu?
        if ($cek_user && mysqli_num_rows($cek_user) > 0) {
            $data_user = mysqli_fetch_assoc($cek_user);
            $id_user = $data_user['id'];     // Ambil ID dari database
            $_SESSION['user_id'] = $id_user; // Simpan ke session biar aman
        } else {
            // Jika email di session ternyata tidak ada di database
            $pesan_gagal = "Gagal: Data pengguna tidak ditemukan. Silakan login ulang.";
        }
    }

    // 2. PROSES UPDATE (Hanya jalan jika ID User ketemu)
    if ($id_user && empty($pesan_gagal)) {
        
        // Amankan Input
        $nama_baru = mysqli_real_escape_string($conn, htmlspecialchars($_POST['nama']));
        $email_baru = mysqli_real_escape_string($conn, htmlspecialchars($_POST['email']));
        $password_baru = mysqli_real_escape_string($conn, $_POST['password']);

        // Validasi Form
        if (empty($nama_baru) || empty($email_baru)) {
            $pesan_gagal = "Nama dan Email tidak boleh kosong!";
        }
        elseif (!filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {
            $pesan_gagal = "Format email tidak valid!";
        }
        else {
            // Cek Ganti Password
            if (!empty($password_baru)) {
                if (strlen($password_baru) < 6) {
                    $pesan_gagal = "Password minimal 6 karakter!";
                } else {
                    $query = "UPDATE users SET nama = '$nama_baru', email = '$email_baru', password = '$password_baru' WHERE id = '$id_user'";
                }
            } else {
                // Update Tanpa Ganti Password
                $query = "UPDATE users SET nama = '$nama_baru', email = '$email_baru' WHERE id = '$id_user'";
            }

            // Eksekusi Query
            if (empty($pesan_gagal)) {
                if (mysqli_query($conn, $query)) {
                    $pesan_sukses = "Profil berhasil diperbarui!";
                    // Update Session Tampilan
                    $_SESSION['user_nama'] = $nama_baru;
                    $_SESSION['user_email'] = $email_baru;
                } else {
                    $pesan_gagal = "Database Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// ... (Lanjutan kode ambil riwayat transaksi di bawah tetap sama) ...
$email_user = $_SESSION['user_email'];
$query_history = "SELECT * FROM transaksi WHERE email = '$email_user' ORDER BY id DESC";
$result_history = mysqli_query($conn, $query_history);
$history = [];
while ($row = mysqli_fetch_assoc($result_history)) {
    $history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Jembatan Ilmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { primary: '#003366', accent: '#F4A261' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans flex flex-col min-h-screen overflow-y-scroll">
    
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
                    <i class="fa-solid fa-user text-black-600"></i>
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
        
        <div class="text-sm text-gray-500 mb-6">
            <a href="beranda.php" class="hover:text-primary">Beranda</a> <span class="mx-2">/</span> <span class="text-gray-800 font-bold">Akun Saya</span>
        </div>

        <?php if($pesan_sukses): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-1"></i>
                <div><p class="font-bold">Sukses!</p><p><?php echo $pesan_sukses; ?></p></div>
            </div>
        <?php endif; ?>

        <?php if($pesan_gagal): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-1"></i>
                <div><p class="font-bold">Gagal!</p><p><?php echo $pesan_gagal; ?></p></div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-24 bg-gradient-to-r from-primary to-blue-800"></div>
                    <div class="px-6 pb-6 text-center -mt-12">
                        <div class="w-24 h-24 bg-white p-1 rounded-full mx-auto shadow-md">
                            <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center text-3xl font-bold text-gray-500 uppercase">
                                <?php echo substr($_SESSION['user_nama'], 0, 1); ?>
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mt-3"><?php echo $_SESSION['user_nama']; ?></h2>
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-bold mt-1">
                            <?php echo ucfirst($_SESSION['user_role']); ?>
                        </span>
                        
                        <div class="mt-6 border-t pt-4">
                            <a href="logout.php" class="w-full block bg-red-50 text-red-600 font-bold py-2 rounded-lg hover:bg-red-100 transition border border-red-200">
                                <i class="fa-solid fa-power-off mr-2"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border-l-4 border-accent overflow-hidden">
                    <div class="bg-orange-50 px-6 py-4 border-b border-orange-100">
                        <h3 class="font-bold text-orange-800 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i> Tata Tertib & Panduan
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4 text-sm text-gray-600">
                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span>Pastikan alamat pengiriman diisi dengan lengkap (RT/RW, Kelurahan) agar kurir tidak nyasar.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-clock text-blue-500 mt-1"></i>
                                <span>Pembayaran via Transfer Bank maksimal dilakukan <b>1x24 jam</b> setelah checkout.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-truck-fast text-primary mt-1"></i>
                                <span>Pesanan yang masuk sebelum jam <b>14.00 WIB</b> akan dikirim di hari yang sama.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-rotate-left text-red-500 mt-1"></i>
                                <span>Retur barang hanya diterima jika menyertakan <b>Video Unboxing</b> tanpa jeda.</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Profil
                    </h3>
                    
                    <form action="" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" name="nama" value="<?php echo $_SESSION['user_nama']; ?>" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" placeholder="Masukkan password baru..." class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                </div>
                            </div>

                        </div>

                        <div class="mt-6 text-right">
                            <button type="submit" name="update_profil" class="bg-primary text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-800 transition shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fa-solid fa-receipt text-primary"></i> Riwayat Pesanan Terakhir
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-100 text-gray-500 text-sm uppercase">
                    <th class="p-3">ID Trx</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Metode</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if(empty($history)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400 italic">
                            <i class="fa-solid fa-box-open text-2xl mb-2 block opacity-50"></i>
                            Belum ada riwayat belanja. Yuk mulai belanja!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($history as $h): ?>
                    <tr class="hover:bg-blue-50 transition group">
                        
                        <td class="p-3 font-mono text-sm text-blue-600 font-bold group-hover:text-blue-800">
                            #<?php echo $h['id']; ?>
                        </td>

                        <td class="p-3 text-sm text-gray-600">
                            <?php echo date('d/m/Y H:i', strtotime($h['tanggal_transaksi'])); ?>
                        </td>

                        <td class="p-3 font-bold text-gray-800">
                            Rp <?php echo number_format($h['total_bayar'], 0, ',', '.'); ?>
                        </td>

                        <td class="p-3 text-sm text-gray-600">
                            <?php echo $h['metode_pembayaran']; ?>
                        </td>

                        <td class="p-3">
                            <?php if($h['status_pembayaran'] == 'Lunas' || $h['status_pembayaran'] == 'Selesai'): ?>
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-check-circle"></i> Selesai
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-clock"></i> Pending
                                </span>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

            </div>
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