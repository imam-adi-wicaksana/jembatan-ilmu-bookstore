<?php
// admin.php - DASHBOARD ANALITIK
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


// === 1. RINGKASAN DATA (KARTU ATAS) ===
// Total Pendapatan (Hanya yang Lunas)
$q_income = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi WHERE status_pembayaran = 'Lunas'");
$income   = mysqli_fetch_assoc($q_income)['total'] ?? 0;

// Total Pengguna
$q_users  = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'member'");
$users    = mysqli_fetch_assoc($q_users)['total'];

// Total Transaksi Sukses
$q_trx    = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status_pembayaran = 'Lunas'");
$trx_sukses = mysqli_fetch_assoc($q_trx)['total'];

// === 2. DATA UNTUK GRAFIK PENJUALAN (7 HARI TERAKHIR) ===
$tgl_labels = [];
$total_sales = [];

// Loop 7 hari ke belakang
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    // Query total per tanggal
    $query_chart = "SELECT SUM(total_bayar) as total FROM transaksi 
                    WHERE DATE(tanggal_transaksi) = '$date' AND status_pembayaran = 'Lunas'";
    $res_chart = mysqli_query($conn, $query_chart);
    $row_chart = mysqli_fetch_assoc($res_chart);
    
    $tgl_labels[] = date('d M', strtotime($date)); // Label tgl (misal: 12 Jan)
    $total_sales[] = $row_chart['total'] ?? 0; // Kalau null ganti 0
}

// === 3. DATA UNTUK GRAFIK STATUS TRANSAKSI (PIE CHART) ===
$q_status = mysqli_query($conn, "SELECT status_pembayaran, COUNT(*) as jumlah FROM transaksi GROUP BY status_pembayaran");
$data_status = ['Pending' => 0, 'Lunas' => 0, 'Batal' => 0];
while($row = mysqli_fetch_assoc($q_status)) {
    $data_status[$row['status_pembayaran']] = $row['jumlah'];
}

// === 4. DATA TABEL STOK TERBARU ===
$query_tabel = "
    SELECT id AS id_barang, judul AS nama_produk, kategori, harga, cover, 'buku' AS tipe FROM buku
    UNION ALL
    SELECT id_atk AS id_barang, nama_atk AS nama_produk, jenis_atk AS kategori, harga, cover, 'atk' AS tipe FROM atk
    ORDER BY id_barang DESC LIMIT 5
";
$result_tabel = mysqli_query($conn, $query_tabel);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik Admin - Jembatan Ilmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="admin.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-xl shadow-lg transition">
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
                    <h2 class="text-xl font-bold text-slate-800">Analitik Penjualan</h2>
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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-6 text-white shadow-lg shadow-blue-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-medium mb-1">Total Pendapatan</p>
                                <h3 class="text-3xl font-bold">Rp <?= number_format($income, 0, ',', '.'); ?></h3>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-wallet text-2xl"></i></div>
                        </div>
                        <p class="text-xs text-blue-100 mt-4 opacity-80">Akumulasi transaksi status 'Lunas'</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-slate-500 text-sm font-bold uppercase">Total Member</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $users; ?></h3>
                            </div>
                            <div class="h-12 w-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
                        </div>
                        <p class="text-xs text-green-500 mt-4 font-bold"><i class="fas fa-arrow-up"></i> Data User Aktif</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-slate-500 text-sm font-bold uppercase">Transaksi Sukses</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $trx_sukses; ?>x</h3>
                            </div>
                            <div class="h-12 w-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <p class="text-xs text-slate-400 mt-4">Pesanan selesai diproses</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Grafik Penjualan (7 Hari Terakhir)</h3>
                        <div class="relative h-72">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Status Transaksi</h3>
                        <div class="relative h-64 flex justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800">5 Barang Terbaru</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs">
                                <tr>
                                    <th class="p-4">Produk</th>
                                    <th class="p-4">Harga</th>
                                    <th class="p-4 text-center">Tipe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php while ($row = mysqli_fetch_assoc($result_tabel)) { ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-4 flex items-center gap-3">
                                        <img src="<?= $row['cover']; ?>" class="h-8 w-8 rounded object-cover" onerror="this.src='https://via.placeholder.com/50'">
                                        <span class="font-bold text-slate-700 line-clamp-1"><?= $row['nama_produk']; ?></span>
                                    </td>
                                    <td class="p-4 text-slate-600">Rp <?= number_format($row['harga']); ?></td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?= $row['tipe'] == 'buku' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600'; ?>">
                                            <?= $row['tipe']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Data dari PHP dilempar ke Javascript
        const tglLabels = <?= json_encode($tgl_labels); ?>;
        const salesData = <?= json_encode($total_sales); ?>;
        
        const statusData = [
            <?= $data_status['Pending']; ?>, 
            <?= $data_status['Lunas']; ?>, 
            <?= $data_status['Batal']; ?>
        ];

        // 1. Konfigurasi Grafik Penjualan (Line Chart)
        const ctxSales = document.getElementById('salesChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: tglLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: salesData,
                    borderColor: '#2563eb', // Biru
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.4, // Garis melengkung halus
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Konfigurasi Grafik Status (Doughnut Chart)
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Lunas', 'Batal'],
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        '#f59e0b', // Pending (Kuning)
                        '#10b981', // Lunas (Hijau)
                        '#ef4444'  // Batal (Merah)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });
    </script>
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