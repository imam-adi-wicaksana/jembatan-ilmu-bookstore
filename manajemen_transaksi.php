<?php
// manajemen_transaksi.php - CRUD TRANSAKSI
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


// === 1. LOGIKA PROSES DATA (POST) ===
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    
    // Ambil input
    $nama   = htmlspecialchars($_POST['nama_penerima']);
    $email  = htmlspecialchars($_POST['email']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $metode = htmlspecialchars($_POST['metode_pembayaran']);
    $total  = htmlspecialchars($_POST['total_bayar']);
    $status = htmlspecialchars($_POST['status_pembayaran']);

    if ($id) {
        // UPDATE DATA (Biasanya untuk ganti status pembayaran)
        $query = "UPDATE transaksi SET 
                  nama_penerima='$nama', email='$email', alamat='$alamat', 
                  metode_pembayaran='$metode', total_bayar='$total', status_pembayaran='$status' 
                  WHERE id='$id'";
        $pesan = "Data transaksi berhasil diperbarui!";
    } else {
        // INSERT DATA BARU (Manual Input Admin)
        $query = "INSERT INTO transaksi (nama_penerima, email, alamat, metode_pembayaran, total_bayar, status_pembayaran) 
                  VALUES ('$nama', '$email', '$alamat', '$metode', '$total', '$status')";
        $pesan = "Transaksi baru berhasil ditambahkan!";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('$pesan'); window.location='manajemen_transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
    }
}

// HAPUS DATA
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM transaksi WHERE id='$id'");
    echo "<script>alert('Data transaksi dihapus!'); window.location='manajemen_transaksi.php';</script>";
}

// === 2. SETUP VIEW VARIABLES ===
$mode = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// Data default form
$data_edit = [
    'id' => '', 'nama_penerima' => '', 'email' => '', 'alamat' => '', 
    'metode_pembayaran' => '', 'total_bayar' => '', 'status_pembayaran' => 'Pending'
];

if ($mode == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $ambil = mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Transaksi - Jembatan Ilmu</title>
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
                
                <a href="manajemen_transaksi.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
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
                    <h2 class="text-xl font-bold text-slate-800">Data Transaksi</h2>
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
                
                <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg">
                            <?= ($mode == 'edit') ? '<i class="fas fa-edit mr-2"></i> Update Transaksi' : '<i class="fas fa-plus mr-2"></i> Input Transaksi Manual'; ?>
                        </h3>
                        <a href="manajemen_transaksi.php" class="text-slate-400 hover:text-white"><i class="fas fa-times text-xl"></i></a>
                    </div>
                    
                    <form action="manajemen_transaksi.php" method="POST" class="p-8">
                        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Penerima</label>
                                    <input type="text" name="nama_penerima" value="<?= $data_edit['nama_penerima']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                                    <input type="email" name="email" value="<?= $data_edit['email']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="3" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"><?= $data_edit['alamat']; ?></textarea>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" class="w-full px-4 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="Tunai (COD)" <?= ($data_edit['metode_pembayaran'] == 'Tunai (COD)') ? 'selected' : ''; ?>>Tunai (COD)</option>
                                        <option value="Transfer Bank" <?= ($data_edit['metode_pembayaran'] == 'Transfer Bank') ? 'selected' : ''; ?>>Transfer Bank</option>
                                        <option value="E-Wallet" <?= ($data_edit['metode_pembayaran'] == 'E-Wallet') ? 'selected' : ''; ?>>E-Wallet (Dana/OVO/Gopay)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Total Tagihan (Rp)</label>
                                    <input type="number" name="total_bayar" value="<?= $data_edit['total_bayar']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Status Pembayaran</label>
                                    <select name="status_pembayaran" class="w-full px-4 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="Pending" <?= ($data_edit['status_pembayaran'] == 'Pending') ? 'selected' : ''; ?>>🕒 Pending (Menunggu)</option>
                                        <option value="Lunas" <?= ($data_edit['status_pembayaran'] == 'Lunas') ? 'selected' : ''; ?>>✅ Lunas (Selesai)</option>
                                        <option value="Batal" <?= ($data_edit['status_pembayaran'] == 'Batal') ? 'selected' : ''; ?>>❌ Batal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3 border-t pt-6">
                            <button type="submit" name="simpan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition flex-1">
                                <i class="fas fa-save mr-2"></i> Simpan Data
                            </button>
                            <a href="manajemen_transaksi.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition">Batal</a>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <form method="GET" class="relative w-full md:w-96">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="cari" placeholder="Cari Nama / Email..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </form>
                    <a href="manajemen_transaksi.php?aksi=tambah" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Tambah Transaksi
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-4 text-center">ID</th>
                                    <th class="p-4">Info Pelanggan</th>
                                    <th class="p-4">Detail Pembayaran</th>
                                    <th class="p-4 text-center">Status</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php
                                $keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
                                $query_sql = "SELECT * FROM transaksi WHERE nama_penerima LIKE '%$keyword%' OR email LIKE '%$keyword%' ORDER BY id DESC";
                                $result = mysqli_query($conn, $query_sql);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Styling Badge Status
                                        $statusClass = 'bg-gray-100 text-gray-600';
                                        if ($row['status_pembayaran'] == 'Lunas') {
                                            $statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                        } elseif ($row['status_pembayaran'] == 'Pending') {
                                            $statusClass = 'bg-yellow-100 text-yellow-700 border border-yellow-200';
                                        } elseif ($row['status_pembayaran'] == 'Batal') {
                                            $statusClass = 'bg-red-100 text-red-700 border border-red-200';
                                        }
                                        
                                        // Format Tanggal
                                        $tanggal = date('d M Y H:i', strtotime($row['tanggal_transaksi']));
                                ?>
                                <tr class="hover:bg-blue-50 transition duration-200">
                                    <td class="p-4 text-center font-bold text-slate-500">#<?= $row['id']; ?></td>
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800 text-base"><?= $row['nama_penerima']; ?></p>
                                        <p class="text-xs text-slate-500 mt-1"><i class="fas fa-envelope"></i> <?= $row['email']; ?></p>
                                        <p class="text-xs text-slate-400 mt-1 line-clamp-1" title="<?= $row['alamat']; ?>"><i class="fas fa-map-marker-alt"></i> <?= $row['alamat']; ?></p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></p>
                                        <p class="text-xs text-slate-500"><?= $row['metode_pembayaran']; ?></p>
                                        <p class="text-[10px] text-slate-400 mt-1"><?= $tanggal; ?></p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?= $statusClass; ?>">
                                            <?= $row['status_pembayaran']; ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="manajemen_transaksi.php?aksi=edit&id=<?= $row['id']; ?>" class="w-10 h-10 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white flex items-center justify-center shadow transition hover:scale-110" title="Edit / Update Status">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="manajemen_transaksi.php?aksi=hapus&id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus history transaksi ini?')" class="w-10 h-10 rounded-lg bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow transition hover:scale-110" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='p-8 text-center text-slate-400'>Belum ada data transaksi.</td></tr>";
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