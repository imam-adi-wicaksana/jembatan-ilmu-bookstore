<?php
// manajemen_atk.php - CRUD UNTUK ATK (ALAT TULIS KANTOR)
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


// === 1. FUNGSI UPLOAD GAMBAR (Sama persis dengan Buku) ===
function uploadGambar() {
    $namaFile   = $_FILES['cover']['name'];
    $ukuranFile = $_FILES['cover']['size'];
    $error      = $_FILES['cover']['error'];
    $tmpName    = $_FILES['cover']['tmp_name'];

    if ($error === 4) { return false; } // Tidak ada gambar diupload

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiValid)) {
        echo "<script>alert('Yang anda upload bukan gambar!');</script>";
        return false;
    }

    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran gambar terlalu besar! (Max 2MB)');</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
    move_uploaded_file($tmpName, 'uploads/' . $namaFileBaru);

    return 'uploads/' . $namaFileBaru;
}

// === 2. LOGIKA PROSES DATA (POST) ===
if (isset($_POST['simpan'])) {
    $id = $_POST['id_atk']; // Primary Key tabel ATK
    
    // Ambil inputan sesuai kolom tabel ATK
    $nama_atk   = htmlspecialchars($_POST['nama_atk']);
    $jenis_atk  = htmlspecialchars($_POST['jenis_atk']);
    $merk       = htmlspecialchars($_POST['merk']);
    $harga      = htmlspecialchars($_POST['harga']);
    $warna      = htmlspecialchars($_POST['warna']);
    $berat      = htmlspecialchars($_POST['berat']);
    $deskripsi  = htmlspecialchars($_POST['deskripsi']);
    $cover_lama = $_POST['cover_lama'];

    // Cek Upload Gambar
    $upload = uploadGambar();
    if ($upload) {
        $cover = $upload; // Pakai gambar baru
    } else {
        $cover = $cover_lama; // Pakai gambar lama
    }

    if ($id) {
        // UPDATE DATA
        $query = "UPDATE atk SET 
                  nama_atk='$nama_atk', jenis_atk='$jenis_atk', merk='$merk', harga='$harga', 
                  cover='$cover', warna='$warna', berat='$berat', deskripsi='$deskripsi' 
                  WHERE id_atk='$id'";
        $pesan = "Data ATK berhasil diperbarui!";
    } else {
        // INSERT DATA BARU
        if (!$upload) { $cover = 'uploads/default_atk.jpg'; } // Default jika tidak ada gambar
        
        $query = "INSERT INTO atk (nama_atk, jenis_atk, merk, harga, cover, warna, berat, deskripsi) 
                  VALUES ('$nama_atk', '$jenis_atk', '$merk', '$harga', '$cover', '$warna', '$berat', '$deskripsi')";
        $pesan = "ATK baru berhasil ditambahkan!";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('$pesan'); window.location='manajemen_atk.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
    }
}

// HAPUS DATA
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Hapus file gambar fisik
    $q = mysqli_query($conn, "SELECT cover FROM atk WHERE id_atk='$id'");
    $f = mysqli_fetch_assoc($q);
    if (file_exists($f['cover'])) { unlink($f['cover']); }

    mysqli_query($conn, "DELETE FROM atk WHERE id_atk='$id'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='manajemen_atk.php';</script>";
}

// === 3. SETUP VIEW VARIABLES ===
$mode = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// Default data kosong untuk form tambah
$data_edit = [
    'id_atk' => '', 'nama_atk' => '', 'jenis_atk' => '', 'merk' => '', 
    'harga' => '', 'warna' => '', 'berat' => '', 'cover' => '', 'deskripsi' => ''
];

// Ambil data jika mode Edit
if ($mode == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $ambil = mysqli_query($conn, "SELECT * FROM atk WHERE id_atk='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen ATK - Jembatan Ilmu</title>
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
                
                <a href="manajemen_atk.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
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
                    <h2 class="text-xl font-bold text-slate-800">Manajemen Data ATK</h2>
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
                
                <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg">
                            <?= ($mode == 'edit') ? '<i class="fas fa-edit mr-2"></i> Edit ATK' : '<i class="fas fa-plus mr-2"></i> Tambah ATK Baru'; ?>
                        </h3>
                        <a href="manajemen_atk.php" class="text-slate-400 hover:text-white"><i class="fas fa-times text-xl"></i></a>
                    </div>
                    
                    <form action="manajemen_atk.php" method="POST" enctype="multipart/form-data" class="p-8">
                        <input type="hidden" name="id_atk" value="<?= $data_edit['id_atk']; ?>">
                        <input type="hidden" name="cover_lama" value="<?= $data_edit['cover']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Barang / ATK</label>
                                    <input type="text" name="nama_atk" value="<?= $data_edit['nama_atk']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Merk / Brand</label>
                                    <input type="text" name="merk" value="<?= $data_edit['merk']; ?>" placeholder="Contoh: Joyko, Kenko" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Jenis ATK</label>
                                    <select name="jenis_atk" class="w-full px-4 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <?php 
                                        $jenis = ['Pulpen', 'Pensil', 'Penghapus', 'Buku Tulis', 'Penggaris', 'Kertas', 'Map', 'Lainnya'];
                                        foreach($jenis as $j){
                                            $selected = ($data_edit['jenis_atk'] == $j) ? 'selected' : '';
                                            echo "<option value='$j' $selected>$j</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Harga (Rp)</label>
                                    <input type="number" name="harga" value="<?= $data_edit['harga']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Upload Foto Produk</label>
                                    <?php if($data_edit['cover'] != ''): ?>
                                        <div class="mb-2 flex items-center gap-3 p-2 bg-slate-100 rounded border">
                                            <img src="<?= $data_edit['cover']; ?>" class="h-16 w-16 object-cover rounded">
                                            <span class="text-xs text-slate-500">Gambar saat ini</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="cover" class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                    "/>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Warna</label>
                                        <input type="text" name="warna" value="<?= $data_edit['warna']; ?>" placeholder="Hitam/Putih/Mix" class="w-full px-3 py-2 rounded border focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Berat</label>
                                        <input type="text" name="berat" value="<?= $data_edit['berat']; ?>" placeholder="0.1 kg" class="w-full px-3 py-2 rounded border focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi Produk</label>
                                    <textarea name="deskripsi" rows="5" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"><?= $data_edit['deskripsi']; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3 border-t pt-6">
                            <button type="submit" name="simpan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition flex-1">
                                <i class="fas fa-save mr-2"></i> Simpan ATK
                            </button>
                            <a href="manajemen_atk.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition">Batal</a>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <form method="GET" class="relative w-full md:w-96">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="cari" placeholder="Cari Nama ATK..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </form>
                    <a href="manajemen_atk.php?aksi=tambah" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Tambah ATK
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-4 text-center">No</th>
                                    <th class="p-4">Detail Produk</th>
                                    <th class="p-4">Jenis & Merk</th>
                                    <th class="p-4">Harga</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php
                                $keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
                                $query_sql = "SELECT * FROM atk WHERE nama_atk LIKE '%$keyword%' OR merk LIKE '%$keyword%' ORDER BY id_atk DESC";
                                $result = mysqli_query($conn, $query_sql);
                                $no = 1;

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr class="hover:bg-blue-50 transition duration-200">
                                    <td class="p-4 text-center text-slate-400"><?= $no++; ?></td>
                                    <td class="p-4">
                                        <div class="flex items-start gap-4">
                                            <div class="h-16 w-16 flex-shrink-0">
                                                <img src="<?= $row['cover']; ?>" class="h-full w-full object-cover rounded shadow border" onerror="this.src='https://via.placeholder.com/64?text=No+Img'">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base"><?= $row['nama_atk']; ?></p>
                                                <p class="text-xs text-slate-500 mt-1">Warna: <?= $row['warna']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="block font-bold text-slate-700"><?= $row['jenis_atk']; ?></span>
                                        <span class="text-xs text-slate-500">Merk: <?= $row['merk']; ?></span>
                                    </td>
                                    <td class="p-4 font-bold text-emerald-600 text-base">
                                        Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="manajemen_atk.php?aksi=edit&id=<?= $row['id_atk']; ?>" class="w-10 h-10 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white flex items-center justify-center shadow transition hover:scale-110"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="manajemen_atk.php?aksi=hapus&id=<?= $row['id_atk']; ?>" onclick="return confirm('Yakin hapus?')" class="w-10 h-10 rounded-lg bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow transition hover:scale-110"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='p-8 text-center text-slate-400'>Belum ada data ATK.</td></tr>";
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