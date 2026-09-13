<?php
// manajemen_diskon.php - CRUD KODE PROMO / VOUCHER
include 'koneksi.php';

// Pastikan session dimulai (jika belum di koneksi.php)
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Cek Login
if (!isset($_SESSION['user_login'])) {
    header("Location: login.php");
    exit;
}

// Ambil Data Admin yang sedang Login
if (isset($_SESSION['user_email'])) {
    $email_login = $_SESSION['user_email'];
    $query_admin = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_login'");
    $admin = mysqli_fetch_assoc($query_admin);
} else {
    // Fallback jika tidak ada session email
    $admin = ['nama' => 'Admin Utama', 'role' => 'admin'];
}


// === 1. LOGIKA SIMPAN DATA (POST) ===
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    
    // Ambil Input & Ubah Kode jadi Huruf Besar semua
    $kode        = strtoupper(htmlspecialchars($_POST['kode_diskon'])); 
    $deskripsi   = htmlspecialchars($_POST['deskripsi']);
    $potongan    = htmlspecialchars($_POST['potongan']);
    $min_belanja = htmlspecialchars($_POST['min_belanja']);
    $status      = htmlspecialchars($_POST['status']);

    if ($id) {
        // UPDATE
        $query = "UPDATE diskon SET 
                  kode_diskon='$kode', deskripsi='$deskripsi', potongan='$potongan', 
                  min_belanja='$min_belanja', status='$status' 
                  WHERE id='$id'";
        $pesan = "Kode diskon berhasil diperbarui!";
    } else {
        // INSERT (Cek dulu apakah kode sudah ada)
        $cek = mysqli_query($conn, "SELECT * FROM diskon WHERE kode_diskon='$kode'");
        if(mysqli_num_rows($cek) > 0){
            echo "<script>alert('Kode Diskon $kode sudah ada! Gunakan kode lain.'); window.location='manajemen_diskon.php';</script>";
            exit;
        }

        $query = "INSERT INTO diskon (kode_diskon, deskripsi, potongan, min_belanja, status) 
                  VALUES ('$kode', '$deskripsi', '$potongan', '$min_belanja', '$status')";
        $pesan = "Voucher baru berhasil dibuat!";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('$pesan'); window.location='manajemen_diskon.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
    }
}

// HAPUS DATA
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM diskon WHERE id='$id'");
    echo "<script>alert('Voucher dihapus!'); window.location='manajemen_diskon.php';</script>";
}

// === 2. SETUP VIEW VARIABLES ===
$mode = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

$data_edit = [
    'id' => '', 'kode_diskon' => '', 'deskripsi' => '', 
    'potongan' => '', 'min_belanja' => '0', 'status' => 'Aktif'
];

if ($mode == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $ambil = mysqli_query($conn, "SELECT * FROM diskon WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Diskon - Jembatan Ilmu</title>
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

                <a href="manajemen_diskon.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
                    <i class="fas fa-tags w-8"></i> <span>Kelola Diskon</span>
                </a>

                <a href="manajemen_pengguna.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-users w-8"></i> <span>Data Pengguna</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-4">Konten & Sistem</p>

                <a href="manajemen_berita.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
                    <i class="fas fa-newspaper w-8"></i> <span>Berita / Artikel</span>
                </a>

                <a href="pengaturan_admin.php" class="flex items-center px-4 py-3 text-slate-300 hover:text-white transition">
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
                    <h2 class="text-xl font-bold text-slate-800">Kelola Voucher & Diskon</h2>
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

                <?php if ($mode == 'tambah' || $mode == 'edit'): ?>
                
                <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg">
                            <?= ($mode == 'edit') ? '<i class="fas fa-edit mr-2"></i> Edit Voucher' : '<i class="fas fa-plus mr-2"></i> Buat Voucher Baru'; ?>
                        </h3>
                        <a href="manajemen_diskon.php" class="text-slate-400 hover:text-white"><i class="fas fa-times text-xl"></i></a>
                    </div>
                    
                    <form action="manajemen_diskon.php" method="POST" class="p-8">
                        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Kode Voucher (Unik)</label>
                                <input type="text" name="kode_diskon" value="<?= $data_edit['kode_diskon']; ?>" placeholder="Contoh: MERDEKA45" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold tracking-wider">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi Promo</label>
                                <input type="text" name="deskripsi" value="<?= $data_edit['deskripsi']; ?>" placeholder="Penjelasan singkat..." required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nominal Potongan (Rp)</label>
                                <input type="number" name="potongan" value="<?= $data_edit['potongan']; ?>" placeholder="10000" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Min. Belanja (Rp)</label>
                                <input type="number" name="min_belanja" value="<?= $data_edit['min_belanja']; ?>" placeholder="0" class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                <p class="text-[10px] text-slate-400 mt-1">Isi 0 jika tanpa minimal belanja</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                                <select name="status" class="w-full px-4 py-3 rounded-lg border bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="Aktif" <?= ($data_edit['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif (Bisa Dipakai)</option>
                                    <option value="Tidak Aktif" <?= ($data_edit['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif (Disembunyikan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3 border-t pt-6">
                            <button type="submit" name="simpan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition flex-1">
                                <i class="fas fa-save mr-2"></i> Simpan Voucher
                            </button>
                            <a href="manajemen_diskon.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition">Batal</a>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="font-bold text-xl text-slate-700">Daftar Kode Promo</h3>
                    <a href="manajemen_diskon.php?aksi=tambah" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Buat Voucher
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-4 text-center">No</th>
                                    <th class="p-4">Kode & Deskripsi</th>
                                    <th class="p-4">Potongan</th>
                                    <th class="p-4">Syarat</th>
                                    <th class="p-4 text-center">Status</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php
                                $query_sql = "SELECT * FROM diskon ORDER BY id DESC";
                                $result = mysqli_query($conn, $query_sql);
                                $no = 1;

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr class="hover:bg-blue-50 transition duration-200">
                                    <td class="p-4 text-center text-slate-400"><?= $no++; ?></td>
                                    <td class="p-4">
                                        <span class="block font-bold text-lg text-blue-600 tracking-wider"><?= $row['kode_diskon']; ?></span>
                                        <span class="text-xs text-slate-500"><?= $row['deskripsi']; ?></span>
                                    </td>
                                    <td class="p-4 font-bold text-emerald-600 text-base">
                                        Rp <?= number_format($row['potongan'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        Min. Belanja: <b>Rp <?= number_format($row['min_belanja'], 0, ',', '.'); ?></b>
                                    </td>
                                    <td class="p-4 text-center">
                                        <?php if($row['status'] == 'Aktif'): ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Aktif</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-xs font-bold">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="manajemen_diskon.php?aksi=edit&id=<?= $row['id']; ?>" class="w-9 h-9 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white flex items-center justify-center shadow transition"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="manajemen_diskon.php?aksi=hapus&id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus kode ini?')" class="w-9 h-9 rounded-lg bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow transition"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='p-8 text-center text-slate-400'>Belum ada kode diskon.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php endif; ?>

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
    </script>
</body>
</html>