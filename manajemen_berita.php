<?php
// manajemen_berita.php - CRUD BERITA / ARTIKEL
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


// === 1. FUNGSI UPLOAD GAMBAR ===
function uploadThumbnail() {
    $namaFile   = $_FILES['thumbnail']['name'];
    $ukuranFile = $_FILES['thumbnail']['size'];
    $error      = $_FILES['thumbnail']['error'];
    $tmpName    = $_FILES['thumbnail']['tmp_name'];

    if ($error === 4) { return false; } // Tidak ada gambar

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiValid)) {
        echo "<script>alert('Format file harus JPG, JPEG, PNG, atau WEBP!');</script>";
        return false;
    }

    if ($ukuranFile > 2000000) { // Max 2MB
        echo "<script>alert('Ukuran gambar terlalu besar! (Max 2MB)');</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
    move_uploaded_file($tmpName, 'uploads/' . $namaFileBaru);

    return 'uploads/' . $namaFileBaru;
}

// === 2. LOGIKA SIMPAN DATA (POST) ===
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    
    // Ambil Input
    $judul       = htmlspecialchars($_POST['judul']);
    $penulis     = htmlspecialchars($_POST['penulis']);
    $kategori    = htmlspecialchars($_POST['kategori']);
    $tanggal     = htmlspecialchars($_POST['tanggal']); // Format YYYY-MM-DD
    $isi_singkat = htmlspecialchars($_POST['isi_singkat']);
    $isi_lengkap = htmlspecialchars($_POST['isi_lengkap']); // Bisa diganti strip_tags jika ingin plain text
    $thumb_lama  = $_POST['thumbnail_lama'];

    // Cek Upload Gambar
    $upload = uploadThumbnail();
    if ($upload) {
        $thumbnail = $upload;
    } else {
        $thumbnail = $thumb_lama;
    }

    if ($id) {
        // UPDATE
        $query = "UPDATE berita SET 
                  judul='$judul', penulis='$penulis', kategori='$kategori', tanggal='$tanggal', 
                  thumbnail='$thumbnail', isi_singkat='$isi_singkat', isi_lengkap='$isi_lengkap' 
                  WHERE id='$id'";
        $pesan = "Berita berhasil diperbarui!";
    } else {
        // INSERT
        if (!$upload) { $thumbnail = 'uploads/default_news.jpg'; }
        
        $query = "INSERT INTO berita (judul, penulis, kategori, tanggal, thumbnail, isi_singkat, isi_lengkap) 
                  VALUES ('$judul', '$penulis', '$kategori', '$tanggal', '$thumbnail', '$isi_singkat', '$isi_lengkap')";
        $pesan = "Berita baru berhasil diterbitkan!";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('$pesan'); window.location='manajemen_berita.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
    }
}

// HAPUS DATA
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Hapus file fisik
    $q = mysqli_query($conn, "SELECT thumbnail FROM berita WHERE id='$id'");
    $f = mysqli_fetch_assoc($q);
    if (file_exists($f['thumbnail']) && $f['thumbnail'] != 'uploads/default_news.jpg') { 
        unlink($f['thumbnail']); 
    }

    mysqli_query($conn, "DELETE FROM berita WHERE id='$id'");
    echo "<script>alert('Berita berhasil dihapus!'); window.location='manajemen_berita.php';</script>";
}

// === 3. SETUP VIEW VARIABLES ===
$mode = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

$data_edit = [
    'id' => '', 'judul' => '', 'penulis' => '', 'kategori' => '', 
    'tanggal' => date('Y-m-d'), 'thumbnail' => '', 'isi_singkat' => '', 'isi_lengkap' => ''
];

if ($mode == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $ambil = mysqli_query($conn, "SELECT * FROM berita WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Berita - Jembatan Ilmu</title>
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

                <a href="manajemen_berita.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
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
                    <h2 class="text-xl font-bold text-slate-800">Kelola Berita & Artikel</h2>
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
                            <?= ($mode == 'edit') ? '<i class="fas fa-edit mr-2"></i> Edit Artikel' : '<i class="fas fa-plus mr-2"></i> Tulis Artikel Baru'; ?>
                        </h3>
                        <a href="manajemen_berita.php" class="text-slate-400 hover:text-white"><i class="fas fa-times text-xl"></i></a>
                    </div>
                    
                    <form action="manajemen_berita.php" method="POST" enctype="multipart/form-data" class="p-8">
                        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">
                        <input type="hidden" name="thumbnail_lama" value="<?= $data_edit['thumbnail']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            
                            <div class="md:col-span-2 space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Judul Artikel</label>
                                    <input type="text" name="judul" value="<?= $data_edit['judul']; ?>" placeholder="Contoh: Tips Membaca Efektif" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Penulis</label>
                                        <input type="text" name="penulis" value="<?= $data_edit['penulis']; ?>" placeholder="Nama Penulis" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Publish</label>
                                        <input type="date" name="tanggal" value="<?= $data_edit['tanggal']; ?>" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Isi Singkat (Teaser)</label>
                                    <textarea name="isi_singkat" rows="3" placeholder="Deskripsi pendek untuk tampilan depan..." required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"><?= $data_edit['isi_singkat']; ?></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Isi Lengkap</label>
                                    <textarea name="isi_lengkap" rows="10" placeholder="Tulis konten lengkap di sini..." required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50"><?= $data_edit['isi_lengkap']; ?></textarea>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Kategori</label>
                                    <select name="kategori" class="w-full px-4 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <?php 
                                        $cats = ['Edukasi', 'Event', 'Promo', 'Review Buku', 'Tips & Trik', 'Pengumuman'];
                                        foreach($cats as $c){
                                            $selected = ($data_edit['kategori'] == $c) ? 'selected' : '';
                                            echo "<option value='$c' $selected>$c</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="bg-white border rounded-xl p-4 shadow-sm">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Thumbnail Artikel</label>
                                    
                                    <?php if($data_edit['thumbnail'] != ''): ?>
                                        <div class="mb-3">
                                            <img src="<?= $data_edit['thumbnail']; ?>" class="w-full h-32 object-cover rounded-lg border">
                                            <p class="text-xs text-slate-500 mt-1 text-center">Gambar saat ini</p>
                                        </div>
                                    <?php endif; ?>

                                    <input type="file" name="thumbnail" class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100 mb-2
                                    "/>
                                    <p class="text-[10px] text-slate-400">Rekomendasi: Landscape (16:9), Max 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3 border-t pt-6">
                            <button type="submit" name="simpan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition flex-1">
                                <i class="fas fa-paper-plane mr-2"></i> Terbitkan Berita
                            </button>
                            <a href="manajemen_berita.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition">Batal</a>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <form method="GET" class="relative w-full md:w-96">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="cari" placeholder="Cari Judul / Kategori..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </form>
                    <a href="manajemen_berita.php?aksi=tambah" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Tulis Berita
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-4 text-center">No</th>
                                    <th class="p-4">Artikel</th>
                                    <th class="p-4">Kategori & Penulis</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php
                                $keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
                                $query_sql = "SELECT * FROM berita WHERE judul LIKE '%$keyword%' OR kategori LIKE '%$keyword%' ORDER BY id DESC";
                                $result = mysqli_query($conn, $query_sql);
                                $no = 1;

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr class="hover:bg-blue-50 transition duration-200">
                                    <td class="p-4 text-center text-slate-400"><?= $no++; ?></td>
                                    <td class="p-4 max-w-sm">
                                        <div class="flex gap-4">
                                            <div class="h-16 w-24 flex-shrink-0">
                                                <img src="<?= $row['thumbnail']; ?>" class="h-full w-full object-cover rounded-lg shadow border" onerror="this.src='https://via.placeholder.com/150x100?text=No+Img'">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base line-clamp-1"><?= $row['judul']; ?></p>
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= $row['isi_singkat']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-block px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-xs font-bold mb-1 border border-indigo-100">
                                            <?= $row['kategori']; ?>
                                        </span>
                                        <p class="text-xs text-slate-500"><i class="fas fa-user-edit"></i> <?= $row['penulis']; ?></p>
                                    </td>
                                    <td class="p-4 text-slate-600 text-xs font-bold">
                                        <?= date('d M Y', strtotime($row['tanggal'])); ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="manajemen_berita.php?aksi=edit&id=<?= $row['id']; ?>" class="w-9 h-9 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white flex items-center justify-center shadow transition" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="manajemen_berita.php?aksi=hapus&id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus berita ini?')" class="w-9 h-9 rounded-lg bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow transition" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='p-8 text-center text-slate-400'>Belum ada berita.</td></tr>";
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