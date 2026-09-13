<?php
session_start();

// Cek apakah ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form detail
    $item_baru = [
        'nama' => $_POST['nama_barang'],
        'harga' => $_POST['harga_satuan'],
        'qty' => $_POST['qty'],
        'gambar' => $_POST['gambar'],
        // ID unik untuk membedakan barang (menggunakan MD5 dari nama barang agar simpel)
        'id_unik' => md5($_POST['nama_barang']) 
    ];

    // Cek apakah keranjang sudah ada di session? Jika belum, buat array baru
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Cek apakah barang yang sama sudah ada di keranjang?
    $sudah_ada = false;
    foreach ($_SESSION['keranjang'] as $key => $item) {
        if ($item['id_unik'] == $item_baru['id_unik']) {
            // Jika ada, tambahkan jumlahnya saja
            $_SESSION['keranjang'][$key]['qty'] += $item_baru['qty'];
            $sudah_ada = true;
            break;
        }
    }

    // Jika barang belum ada, masukkan sebagai item baru
    if (!$sudah_ada) {
        $_SESSION['keranjang'][] = $item_baru;
    }

    // Kembali ke halaman sebelumnya dengan pesan
    // Menggunakan HTTP_REFERER agar kembali tepat ke halaman produk tadi
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'produk_buku.php';
    echo "<script>
            alert('Berhasil masuk keranjang!'); 
            window.location.href = '$referer';
          </script>";
    exit;
} else {
    // Jika diakses langsung tanpa POST, lempar ke beranda
    header("Location: beranda.php");
    exit;
}
?>