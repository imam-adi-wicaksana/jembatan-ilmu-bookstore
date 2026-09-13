<?php
// pengaturan.php - PROFIL & PENGATURAN ADMIN
include 'koneksi.php';

// Pastikan session dimulai (jika belum di koneksi.php)
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Cek Login
if (!isset($_SESSION['user_login'])) {
    header("Location: login.php");
    exit;
}

// Ambil Data Admin yang sedang Login
// Kita gunakan email dari session untuk mencari data user
$email_login = $_SESSION['user_email'];
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_login'");
$admin = mysqli_fetch_assoc($query_admin);
$id_admin = $admin['id'];

// === LOGIKA UPDATE PROFIL ===
if (isset($_POST['update_profil'])) {
    $nama     = htmlspecialchars($_POST['nama']);
    $email    = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']); // Ambil input password

    // Cek apakah kolom password diisi atau tidak
    if (!empty($password)) {
        // Jika diisi, update nama, email, dan password
        $query = "UPDATE users SET nama = '$nama', email = '$email', password = '$password' WHERE id = '$id_admin'";
    } else {
        // Jika kosong, update nama dan email saja
        $query = "UPDATE users SET nama = '$nama', email = '$email' WHERE id = '$id_admin'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['user_email'] = $email;
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='pengaturan_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal update: ".mysqli_error($conn)."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun - Jembatan Ilmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <aside id="sidebar" class="w-64 bg-slate-900 text-white flex-col shadow-2xl hidden md:flex absolute md:relative z-50 h-full">
            <div class="h-20 flex items-center justify-between px-4 border-b border-slate-700 bg-slate-800 relative">
                <div class="flex-1 flex justify-center">
                    <img src="Jembatan Ilmu.png" alt="Logo Jembatan Ilmu" class="h-12 w-auto brightness-0 invert transition-all duration-300 hover:scale-105">
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-gray-300 hover:text-white absolute right-4">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto">
                <a href="admin.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-chart-line w-8"></i> 
                    <span class="font-semibold">Dashboard</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-4">Manajemen Toko</p>
                
                <a href="manajemen_buku.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-book w-8"></i> <span>Data Buku</span>
                </a>
                
                <a href="manajemen_atk.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-pen w-8"></i> <span>Data ATK</span>
                </a>
                
                <a href="manajemen_transaksi.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-shopping-cart w-8"></i> <span>Transaksi</span>
                </a>

                <a href="manajemen_diskon.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-tags w-8"></i> <span>Kelola Diskon</span>
                </a>

                <a href="manajemen_pengguna.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-users w-8"></i> <span>Data Pengguna</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-4">Konten & Sistem</p>

                <a href="manajemen_berita.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-newspaper w-8"></i> <span>Berita / Artikel</span>
                </a>

                <a href="pengaturan_admin.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
                    <i class="fas fa-cog w-8"></i> <span>Pengaturan</span>
                </a>
            </nav>
            <div class="p-4 bg-slate-800">
                <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 transition"><i class="fas fa-power-off"></i> <span>Logout</span></a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 z-10">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-slate-800">Pengaturan Akun & Sistem</h2>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold"><?= $admin['nama']; ?></p>
                        <p class="text-xs text-slate-400">Administrator</p>
                    </div>
                    <div class="h-10 w-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                        <?= strtoupper(substr($admin['nama'], 0, 1)); ?>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                            <div class="bg-slate-800 px-6 py-4 border-b border-slate-700">
                                <h3 class="text-white font-bold text-lg"><i class="fas fa-user-cog mr-2"></i> Edit Profil Saya</h3>
                            </div>
                            
                            <form action="" method="POST" class="p-8">
                                <div class="flex items-center gap-6 mb-8">
                                    <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl font-bold text-blue-600 shadow-inner">
                                        <?= strtoupper(substr($admin['nama'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg"><?= $admin['nama']; ?></h4>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold border border-blue-200">
                                            Role: <?= strtoupper($admin['role']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                        <input type="text" name="nama" value="<?= $admin['nama']; ?>" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Email Login</label>
                                        <input type="email" name="email" value="<?= $admin['email']; ?>" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none transition bg-slate-50">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Ubah Password <span class="text-xs text-slate-400 font-normal">(Kosongkan jika tidak ingin diubah)</span></label>
                                        <div class="relative">
                                            <input type="password" name="password" id="inputPassword" placeholder="Masukkan password baru" class="w-full px-4 py-3 pr-12 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none transition bg-slate-50">
                                            
                                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none transition">
                                                <i class="fas fa-eye" id="eyeIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 pt-6 border-t">
                                    <button type="submit" name="update_profil" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg border border-slate-700 text-white overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-700 flex items-center gap-3">
                                <i class="fas fa-clipboard-list text-yellow-400 text-xl"></i>
                                <h3 class="font-bold text-lg">Tata Tertib Admin</h3>
                            </div>
                            <div class="p-6">
                                <ul class="space-y-4 text-sm leading-relaxed text-slate-300">
                                    <li class="flex gap-3">
                                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                        <div>
                                            <strong class="text-white block">Update Stok Berkala</strong>
                                            Pastikan stok buku dan ATK selalu sesuai dengan fisik di gudang.
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                        <div>
                                            <strong class="text-white block">Cek Transaksi Harian</strong>
                                            Verifikasi pembayaran masuk minimal 2x sehari (Pagi & Sore).
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                        <div>
                                            <strong class="text-white block">Kerahasian Data</strong>
                                            Dilarang menyebarkan data pribadi pelanggan (Alamat/No HP) kepada pihak luar.
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                        <div>
                                            <strong class="text-white block">Konfirmasi Pengiriman</strong>
                                            Segera update status menjadi 'Lunas' atau 'Dikirim' setelah barang diproses.
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                                        <div>
                                            <strong class="text-white block">Keamanan Akun</strong>
                                            Wajib Logout setelah selesai bekerja. Ganti password secara berkala.
                                        </div>
                                    </li>
                                </ul>

                                <div class="mt-6 pt-6 border-t border-slate-700 text-center">
                                    <p class="text-xs text-slate-500">Versi Sistem: v1.2.0 (Stable)</p>
                                    <p class="text-xs text-slate-500 mt-1">&copy; 2024 Tim IT Jembatan Ilmu</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                            <h4 class="font-bold text-slate-700 mb-4">Status Sistem</h4>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-500">Server Status</span>
                                <span class="text-xs font-bold text-green-600">● Online</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-500">Database</span>
                                <span class="text-xs font-bold text-slate-700">Connected</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Waktu Server</span>
                                <span class="text-xs font-bold text-slate-700"><?= date('H:i:s'); ?> WIB</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>
<script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
            } else {
                sidebar.classList.remove('flex');
                sidebar.classList.add('hidden');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
            } else {
                sidebar.classList.remove('flex');
                sidebar.classList.add('hidden');
            }
        }

        function togglePassword() {
            const passwordInput = document.getElementById('inputPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            
            // Cek jika tipe input saat ini adalah password
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'; // Ubah jadi teks agar terlihat
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash'); // Ganti icon ke mata dicoret
            } else {
                passwordInput.type = 'password'; // Kembalikan ke password (titik-titik)
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye'); // Ganti icon ke mata biasa
            }
        }
    </script>
</body>
</html>