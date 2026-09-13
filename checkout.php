<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_login'])) {
    echo "<script>
            alert('Akses ditolak! Silakan login terlebih dahulu untuk melakukan Checkout.');
            window.location='login.php';
          </script>";
    exit;
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['index'])) {
    $index = $_GET['index'];
    
    if (isset($_SESSION['keranjang'][$index])) {
        unset($_SESSION['keranjang'][$index]);
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
    }
    
    header("Location: checkout.php");
    exit;
}

$query_diskon = mysqli_query($conn, "SELECT * FROM diskon WHERE status='Aktif'");
$list_diskon_db = [];
while($d = mysqli_fetch_assoc($query_diskon)){
    $list_diskon_db[] = $d;
}

$daftar_belanja = [];
$total_belanja_keseluruhan = 0;
$pesanan_berhasil = false;
$struk_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_transaksi'])) {
    $nama = htmlspecialchars($_POST['nama_penerima']);
    $metode_pengiriman = isset($_POST['metode_pengiriman']) ? $_POST['metode_pengiriman'] : '';
    $alamat = htmlspecialchars($_POST['alamat_lengkap']) . ($metode_pengiriman ? " (Pengiriman: $metode_pengiriman)" : ""); 
    $metode = $_POST['payment'];
    $kode_input = strtoupper($_POST['kode_diskon_input']); 
    $final_total_frontend = $_POST['final_total'];    
    $pesanan_berhasil = true;
    $email_user = $_SESSION['user_email'] ?? 'guest@example.com';
    $tgl_skrg = date('Y-m-d H:i:s');

    $query_trx = "INSERT INTO transaksi (nama_penerima, email, alamat, metode_pembayaran, total_bayar, status_pembayaran, tanggal_transaksi) 
                  VALUES ('$nama', '$email_user', '$alamat', '$metode', '$final_total_frontend', 'Pending', '$tgl_skrg')";

    if(mysqli_query($conn, $query_trx)){
        $struk_data = [
            'tanggal' => date('d-m-Y H:i'),
            'nama_penerima' => $nama,
            'metode' => $metode,
            'items' => json_decode($_POST['list_barang_json'], true),
            'total_bayar' => $final_total_frontend,
            'diskon' => $_POST['final_diskon'] 
        ];
        
        // Hapus Keranjang
        unset($_SESSION['keranjang']);
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

// === 3. LOGIKA KERANJANG ===
elseif (!$pesanan_berhasil) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['beli_sekarang'])) {
        $item = [
            'nama' => $_POST['nama_barang'],
            'harga' => $_POST['harga_satuan'],
            'qty' => $_POST['qty'],
            'gambar' => $_POST['gambar'],
            'subtotal' => $_POST['harga_satuan'] * $_POST['qty']
        ];
        $daftar_belanja[] = $item;
    } elseif (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
        foreach ($_SESSION['keranjang'] as $item_session) {
            $item = $item_session;
            $item['subtotal'] = $item_session['harga'] * $item_session['qty'];
            $daftar_belanja[] = $item;
        }
    }
    foreach ($daftar_belanja as $b) {
        $total_belanja_keseluruhan += $b['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Jembatan Ilmu Store</title>
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

    <?php if (!$pesanan_berhasil): ?>
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

    <?php endif; ?>

    <main class="container mx-auto px-4 py-8 flex-grow">
        
        <?php if (!$pesanan_berhasil): ?>
            
            <h1 class="text-3xl font-bold text-primary mb-8 border-b pb-4 text-center">Checkout & Pembayaran</h1>

            <!-- Form utama yang akan disubmit -->
            <form action="" method="POST" id="formCheckout">
                <input type="hidden" name="list_barang_json" value='<?php echo json_encode($daftar_belanja); ?>'>
                <input type="hidden" name="final_total" id="inputFinalTotal" value="<?php echo $total_belanja_keseluruhan; ?>">
                <input type="hidden" name="final_diskon" id="inputFinalDiskon" value="0">
                <input type="hidden" name="kode_diskon_input" id="inputKodeHidden" value="">
                <input type="hidden" name="proses_transaksi" value="1">
                
                <!-- Input hidden yang akan diisi dari modal -->
                <input type="hidden" name="nama_penerima" id="inputNamaForm">
                <input type="hidden" name="alamat_lengkap" id="inputAlamatForm">
                <input type="hidden" name="metode_pengiriman" id="inputPengirimanForm">
                <input type="hidden" name="payment" id="inputPaymentForm">

                <div class="flex flex-col lg:flex-row gap-8">
                    
                    <!-- Kiri: Produk yang dipilih -->
                    <div class="w-full lg:w-2/3">
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                            <div class="flex justify-between items-center mb-4 border-b pb-4">
                                <h2 class="text-xl font-bold flex items-center">
                                    <i class="fa-solid fa-box-open mr-3 text-primary"></i> Ringkasan Pesanan
                                </h2>
                                <a href="produk_buku.php" class="text-sm font-bold text-primary hover:text-blue-800 hover:underline flex items-center gap-2 transition" title="Tambah Produk Lain">
                                    <i class="fa-solid fa-plus"></i> Tambah Produk
                                </a>
                            </div>

                            <div class="max-h-[500px] overflow-y-auto pr-2">
                                <?php if (empty($daftar_belanja)): ?>
                                    <div class="text-center py-8">
                                        <i class="fa-solid fa-basket-shopping text-4xl text-gray-200 mb-2"></i>
                                        <p class="text-sm text-gray-500">Keranjang kosong.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($daftar_belanja as $index => $item): ?>
                                        <div class="flex gap-4 mb-4 group relative border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                            <div class="relative w-20 h-20 flex-shrink-0">
                                                <img src="<?php echo $item['gambar']; ?>" class="w-full h-full object-cover rounded bg-gray-100 border border-gray-200">
                                            </div>

                                            <div class="flex-1 flex flex-col justify-center">
                                                <h4 class="font-bold text-base line-clamp-2 text-gray-800"><?php echo $item['nama']; ?></h4>
                                                <p class="text-gray-500 text-sm mt-1">
                                                    <?php echo $item['qty']; ?> x Rp <?php echo number_format($item['harga'],0,',','.'); ?>
                                                </p>
                                            </div>

                                            <div class="text-right flex flex-col justify-center items-end min-w-[120px]">
                                                <span class="font-bold text-base text-gray-800">
                                                    Rp <?php echo number_format($item['subtotal'],0,',','.'); ?>
                                                </span>

                                                <?php if(isset($_SESSION['keranjang'])): ?>
                                                    <a href="checkout.php?aksi=hapus&index=<?php echo $index; ?>" 
                                                       onclick="return confirm('Hapus produk ini dari keranjang?')"
                                                       class="text-gray-400 hover:text-red-500 transition text-sm flex items-center gap-1 mt-2" 
                                                       title="Hapus Produk">
                                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Nominal Total dan Diskon -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 sticky top-24">
                            
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Kode Diskon</label>
                                <div class="flex gap-2">
                                    <input type="text" id="inputKode" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm uppercase focus:ring-primary focus:border-primary" placeholder="Masukan Kode">
                                    <button type="button" onclick="cekDiskon()" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-black transition font-bold">Pakai</button>
                                </div>
                                <p id="pesanDiskon" class="text-xs mt-2 italic"></p>
                            </div>

                            <div class="space-y-3 text-base mb-6 border-t pt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-bold">Rp <span id="valSubtotal"><?php echo number_format($total_belanja_keseluruhan,0,',','.'); ?></span></span>
                                </div>
                                <div class="flex justify-between text-red-500">
                                    <span>Diskon</span>
                                    <span class="font-bold">- Rp <span id="valDiskon">0</span></span>
                                </div>
                                <div class="flex justify-between border-t border-dashed border-gray-300 pt-3 mt-3">
                                    <span class="font-bold text-xl">Total</span>
                                    <span class="font-bold text-xl text-primary">Rp <span id="valTotal"><?php echo number_format($total_belanja_keseluruhan,0,',','.'); ?></span></span>
                                </div>
                            </div>

                            <?php if (!empty($daftar_belanja)): ?>
                                <button type="button" onclick="bukaModalPengiriman()" class="w-full bg-primary text-white font-bold py-3.5 rounded-lg hover:bg-blue-800 transition shadow-lg text-lg flex justify-center items-center gap-2">
                                    Konfirmasi <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" disabled class="w-full bg-gray-300 text-white font-bold py-3.5 rounded-lg cursor-not-allowed text-lg">
                                    Keranjang Kosong
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal Data Pengiriman -->
            <div id="modalPengiriman" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex justify-center items-center p-4">
                <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-2xl transform transition-all">
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h2 class="text-xl font-bold text-primary">Informasi Pengiriman</h2>
                        <button onclick="tutupModalPengiriman()" class="text-gray-400 hover:text-red-500 text-xl"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima</label>
                            <input type="text" id="modalNama" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-primary focus:border-primary" placeholder="Nama Lengkap" value="<?php echo $_SESSION['user_nama'] ?? ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" id="modalEmail" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap (Apabila pilih ambil barang langsung harap ditulis "-" saja)</label>
                            <textarea id="modalAlamat" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-primary focus:border-primary" rows="3" placeholder="Jalan, No Rumah, RT/RW, Kota..."></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 mt-4">Pilihan Pengiriman</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="border rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition flex flex-col items-center text-center gap-2" id="labelPaketCOD" onclick="pilihPengiriman('Paket COD')">
                                    <input type="radio" name="pilihan_pengiriman" value="Paket COD" class="hidden">
                                    <i class="fa-solid fa-truck-fast text-2xl text-primary"></i>
                                    <span class="font-bold text-sm">Sistem Paket COD</span>
                                </label>
                                <label class="border rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition flex flex-col items-center text-center gap-2" id="labelAmbilToko" onclick="pilihPengiriman('Ambil di Toko')">
                                    <input type="radio" name="pilihan_pengiriman" value="Ambil di Toko" class="hidden">
                                    <i class="fa-solid fa-store text-2xl text-primary"></i>
                                    <span class="font-bold text-sm">Ambil Barang Langsung</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-red-700 mb-2 mt-4">Catatan: Setelah melakukan konfirmasi pesanan melalui nomor WhatsApp, harap untuk mencetak struk pembayaran agar dapat diserahkan kepada kurir atau karyawan di toko.</label>
                        </div>
                        <p id="errorPengiriman" class="text-red-500 text-xs hidden">Harap lengkapi nama, alamat dan pilih pengiriman.</p>
                    </div>

                    <div class="mt-6">
                        <button onclick="lanjutPembayaran()" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition">
                            Lanjut Pembayaran
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Metode Pembayaran -->
            <div id="modalPembayaran" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex justify-center items-center p-4">
                <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-2xl transform transition-all">
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h2 class="text-xl font-bold text-primary">Metode Pembayaran</h2>
                        <button onclick="kembaliKePengiriman()" class="text-gray-400 hover:text-gray-600 text-sm font-bold"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</button>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="pilihPembayaran('Tunai (COD)')">
                            <input type="radio" name="pilihan_pembayaran" value="Tunai (COD)" class="h-5 w-5 text-primary">
                            <div class="ml-4 font-bold">Bayar Tunai / COD</div>
                            <i class="fa-solid fa-money-bill-wave ml-auto text-green-600 text-xl"></i>
                        </label>
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="pilihPembayaran('Transfer Bank')">
                            <input type="radio" name="pilihan_pembayaran" value="Transfer Bank" class="h-5 w-5 text-primary">
                            <div class="ml-4 font-bold">Transfer Bank</div>
                            <i class="fa-solid fa-building-columns ml-auto text-blue-600 text-xl"></i>
                        </label>
                    </div>
                    
                    <p id="errorPembayaran" class="text-red-500 text-xs hidden mb-3">Harap pilih metode pembayaran.</p>

                    <button onclick="selesaikanPesanan()" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-xl"></i> Konfirmasi Pesanan & WhatsApp
                    </button>
                </div>
            </div>

        <?php else: ?>

            <div class="flex justify-center items-center min-h-[60vh]">
                <div class="bg-white p-8 rounded-lg shadow-2xl border border-gray-200 max-w-md w-full font-mono text-sm">
                    <div class="text-center mb-6 border-b border-dashed border-gray-400 pb-4">
                        <h2 class="font-bold text-lg uppercase">Jembatan Ilmu Store</h2>
                        <p class="text-xs">Struk Transaksi # Resmi</p>
                    </div>
                    <div class="mb-4">
                        <div class="flex justify-between">
                            <span>Tgl: <?php echo $struk_data['tanggal']; ?></span>
                            <span><?php echo $struk_data['nama_penerima']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Metode: <?php echo $struk_data['metode']; ?></span>
                        </div>
                    </div>
                    <div class="border-b border-dashed border-gray-400 pb-4 mb-4">
                        <?php foreach($struk_data['items'] as $item): ?>
                            <div class="mb-2">
                                <div class="font-bold"><?php echo $item['nama']; ?></div>
                                <div class="flex justify-between">
                                    <span><?php echo $item['qty']; ?> x <?php echo number_format($item['harga'],0,',','.'); ?></span>
                                    <span><?php echo number_format($item['subtotal'],0,',','.'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="space-y-1 mb-6">
                        <div class="flex justify-between text-red-600">
                            <span>Diskon</span>
                            <span>- Rp <?php echo number_format($struk_data['diskon'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t border-dashed border-gray-400 pt-2 mt-2">
                            <span>TOTAL BAYAR</span>
                            <span>Rp <?php echo number_format($struk_data['total_bayar'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    <div class="mt-8 flex gap-3 no-print">
                        <button onclick="window.print()" class="flex-1 bg-gray-800 text-white py-2 rounded font-bold hover:bg-black">
                            <i class="fa-solid fa-print mr-2"></i> Cetak
                        </button>
                        <a href="beranda.php" class="flex-1 bg-primary text-white py-2 rounded font-bold hover:bg-blue-800 text-center">Selesai</a>
                    </div>
                </div>
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

    <?php if (!$pesanan_berhasil): ?>
    <script>
        // 1. Ambil Data Diskon dari PHP ke variabel JS
        const databaseDiskon = <?php echo json_encode($list_diskon_db); ?>;
        
        // Ambil subtotal dari PHP
        let subtotal = <?php echo $total_belanja_keseluruhan; ?>;
        
        // === FUNGSI UTAMA CEK DISKON ===
        function cekDiskon() {
            let inputField = document.getElementById('inputKode');
            let kodeInput = inputField.value.toUpperCase();
            let pesan = document.getElementById('pesanDiskon');
            let potongan = 0;
            let found = false;

            if(kodeInput.trim() === "") return; // Jangan cek kalau kosong

            pesan.innerHTML = "Mengecek...";
            pesan.className = "text-xs mt-2 italic text-gray-500";

            // Loop database JS
            for (let d of databaseDiskon) {
                if (d.kode_diskon === kodeInput) {
                    // Cek Minimal Belanja
                    if (subtotal >= parseInt(d.min_belanja)) {
                        potongan = parseInt(d.potongan);
                        found = true;
                    } else {
                        pesan.className = "text-xs mt-2 italic text-red-500";
                        pesan.innerHTML = "Minimal belanja Rp " + formatRupiah(d.min_belanja);
                        updateTampilan(0);
                        return;
                    }
                    break;
                }
            }

            if (!found) {
                pesan.className = "text-xs mt-2 italic text-red-500";
                pesan.innerHTML = "Kode tidak valid atau tidak ditemukan.";
                updateTampilan(0);
                return;
            }

            // Validasi: Diskon tidak boleh melebihi total belanja (minus)
            if (potongan >= subtotal) {
                // Opsional: Batasi potongan maksimal sebesar subtotal
                potongan = subtotal; 
                // Atau tampilkan error jika tidak ingin gratis total
                // pesan.innerHTML = "Diskon melebihi harga barang.";
            }

            // SUKSES
            pesan.className = "text-xs mt-2 font-bold text-green-600";
            pesan.innerHTML = "Berhasil! Potongan Rp " + formatRupiah(potongan);
            updateTampilan(potongan);
            
            // Simpan ke hidden input
            document.getElementById('inputKodeHidden').value = kodeInput;
        }

        function updateTampilan(potongan) {
            let total = subtotal - potongan;
            if(total < 0) total = 0;

            document.getElementById('valDiskon').innerText = formatRupiah(potongan);
            document.getElementById('valTotal').innerText = formatRupiah(total);
            
            // Update input hidden untuk dikirim ke PHP saat submit
            document.getElementById('inputFinalDiskon').value = potongan;
            document.getElementById('inputFinalTotal').value = total;
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // === FITUR AUTO DETECT KODE DARI URL ===
        // Kode ini akan berjalan saat halaman selesai dimuat
        window.addEventListener('DOMContentLoaded', (event) => {
            // Ambil parameter dari URL (contoh: checkout.php?kode=MERDEKA)
            const urlParams = new URLSearchParams(window.location.search);
            const kodeDariUrl = urlParams.get('kode');

            if (kodeDariUrl) {
                // Isi input box
                document.getElementById('inputKode').value = kodeDariUrl;
                // Jalankan fungsi cek otomatis
                cekDiskon();
            }
        });

        // === FITUR MODAL CHECKOUT ===
        let pengirimanPilihan = "";
        let pembayaranPilihan = "";

        function bukaModalPengiriman() {
            document.getElementById('modalPengiriman').classList.remove('hidden');
        }

        function tutupModalPengiriman() {
            document.getElementById('modalPengiriman').classList.add('hidden');
        }

        function pilihPengiriman(pilihan) {
            pengirimanPilihan = pilihan;
            document.getElementById('labelPaketCOD').classList.remove('ring-2', 'ring-primary', 'bg-blue-50');
            document.getElementById('labelAmbilToko').classList.remove('ring-2', 'ring-primary', 'bg-blue-50');
            
            if (pilihan === 'Paket COD') {
                document.getElementById('labelPaketCOD').classList.add('ring-2', 'ring-primary', 'bg-blue-50');
            } else {
                document.getElementById('labelAmbilToko').classList.add('ring-2', 'ring-primary', 'bg-blue-50');
            }
        }

        function lanjutPembayaran() {
            let nama = document.getElementById('modalNama').value.trim();
            let alamat = document.getElementById('modalAlamat').value.trim();
            let err = document.getElementById('errorPengiriman');
            
            if (nama === "" || alamat === "" || pengirimanPilihan === "") {
                err.classList.remove('hidden');
                return;
            }
            err.classList.add('hidden');
            
            document.getElementById('inputNamaForm').value = nama;
            document.getElementById('inputAlamatForm').value = alamat;
            document.getElementById('inputPengirimanForm').value = pengirimanPilihan;

            document.getElementById('modalPengiriman').classList.add('hidden');
            document.getElementById('modalPembayaran').classList.remove('hidden');
        }

        function kembaliKePengiriman() {
            document.getElementById('modalPembayaran').classList.add('hidden');
            document.getElementById('modalPengiriman').classList.remove('hidden');
        }

        function pilihPembayaran(pilihan) {
            pembayaranPilihan = pilihan;
            let radios = document.getElementsByName('pilihan_pembayaran');
            for(let i=0; i<radios.length; i++){
                if(radios[i].value === pilihan){
                    radios[i].checked = true;
                }
            }
        }

        function selesaikanPesanan() {
            let err = document.getElementById('errorPembayaran');
            if (pembayaranPilihan === "") {
                err.classList.remove('hidden');
                return;
            }
            err.classList.add('hidden');
            
            document.getElementById('inputPaymentForm').value = pembayaranPilihan;

            // Generate WhatsApp message
            let nama = document.getElementById('inputNamaForm').value;
            let email = document.getElementById('modalEmail').value;
            let alamat = document.getElementById('inputAlamatForm').value;
            let total = document.getElementById('inputFinalTotal').value;
            
            let noWA = "6289502506737"; // Nomor WhatsApp admin
            let pesanWA = "";

            if (pengirimanPilihan === "Paket COD") {
                pesanWA = `Halo Admin Jembatan Ilmu, saya ingin melakukan pemesanan (Sistem Paket COD).\n\n*Data Pemesan:*\nNama: ${nama}\nEmail: ${email}\nAlamat: ${alamat}\nMetode Pembayaran: ${pembayaranPilihan}\nTotal Tagihan: Rp ${formatRupiah(total)}\n\nMohon segera diproses. Terima kasih.`;
            } else {
                pesanWA = `Halo Admin Jembatan Ilmu, saya ingin melakukan pemesanan (Ambil di Toko).\n\n*Data Pemesan:*\nNama: ${nama}\nEmail: ${email}\nAlamat: ${alamat}\nMetode Pembayaran: ${pembayaranPilihan}\nTotal Tagihan: Rp ${formatRupiah(total)}\n\nSaya akan segera mengambilnya di toko. Terima kasih.`;
            }

            let waLink = `https://api.whatsapp.com/send?phone=${noWA}&text=${encodeURIComponent(pesanWA)}`;
            
            // Buka WhatsApp di tab baru
            window.open(waLink, '_blank');
            
            // Submit form untuk simpan ke database dan tampilkan struk
            document.getElementById('formCheckout').submit();
        }
    </script>
    <?php endif; ?>


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