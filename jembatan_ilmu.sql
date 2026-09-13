-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 11:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jembatan_ilmu`
--

-- --------------------------------------------------------

--
-- Table structure for table `atk`
--

CREATE TABLE `atk` (
  `id_atk` int(11) NOT NULL,
  `nama_atk` varchar(255) DEFAULT NULL,
  `jenis_atk` varchar(100) DEFAULT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `warna` varchar(100) DEFAULT NULL,
  `berat` varchar(20) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `atk`
--

INSERT INTO `atk` (`id_atk`, `nama_atk`, `jenis_atk`, `merk`, `harga`, `cover`, `warna`, `berat`, `deskripsi`) VALUES
(1, 'Pulpen Standard AE7', 'Pulpen', 'Standard', 2000, 'uploads/696b1be3c680c.png', 'Hitam', '0.01 kg', 'Pulpen Standard AE7 adalah legenda alat tulis di Indonesia. Dikenal dengan tinta yang pekat.'),
(2, 'Set Pulpen Gel (12 Warna)', 'Pulpen', 'Joyko', 36000, 'https://media.monotaro.id/mid01/big/Kebutuhan%20Kantor/Alat%20Tulis/Pena/Pulpen/Joyko%20Pulpen%20Gel%20Warna%20Warni/Joyko%20Pulpen%20Gel%20Warna%20Warni%20GPC%20-%20297%2012%20Color%200_5mm%201pc/ovS029360895-1.jpg', '12 Warna Warni', '0.15 kg', 'Ekspresikan kreativitasmu dengan Set Pulpen Gel 12 Warna ini.'),
(3, 'Kalkulator Saintifik', 'Alat Bantu Hitung', 'Joyko', 40000, 'https://img.lazcdn.com/g/ff/kf/S56df852afb364935a2a50a22978fd38aC.jpg_720x720q80.jpg', 'Hitam', '0.25 kg', 'Kalkulator ilmiah dengan 240 fungsi yang lengkap untuk membantu pelajar SMA dan Mahasiswa Teknik/MIPA.'),
(4, 'Memo Sticky Notes', 'Lainnya', 'Joyko', 10000, 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//catalog-image/97/MTA-112065558/br-m036969-06925_memo-sticky-note-kertas-tempel-mms-12-joyko-alat-tulis-perlengkapan-kantor-warna-warni-panjang-stick_full01-33b5dc55.jpg', 'Neon Mix', '0.05 kg', 'Jangan biarkan ide atau tugas penting terlewat! Sticky notes berkualitas tinggi.'),
(5, 'Pensil Faber-Castell 2B', 'Pensil', 'Faber-Castell', 6000, 'https://parto.id/asset/foto_produk/2b_fc_jpg_172067566934.jpg', 'Hijau (Kayu)', '0.02 kg', 'Pensil 2B standar ujian komputer. Dibuat dengan teknologi bonding SV.'),
(6, 'Pensil Staedler EE', 'Pensil', 'Staedler', 8000, 'https://siplah-oss.tokoladang.co.id/merchant/13777/product/xa0iuH0Xh7t2hqoW7bLVEuhvexAQbmKT6Cbf8sLd.jpg', 'Biru (Kayu)', '0.02 kg', 'Pensil grade EE dengan karakter hitam pekat dan lembut.'),
(7, 'Penghapus Faber-Castell (Putih)', 'Penghapus', 'Faber-Castell', 3000, 'https://faber-castell.co.id/cfind/source/images/product/gwm/700x700-gwm/187120.jpg', 'Putih', '0.02 kg', 'Penghapus berkualitas tinggi yang bebas debu (Dust-Free).'),
(8, 'Buku Matematika 98 Lembar', 'Pulpen', 'Kiky', 7000, 'https://cdn.gramedia.com/uploads/items/1023601_edit.jpg', 'Random Cover', '0.3 kg', 'Buku tulis bergaris kotak-kotak kecil khusus untuk pelajaran Matematika.');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `tanggal` varchar(50) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `isi_singkat` text DEFAULT NULL,
  `isi_lengkap` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `tanggal`, `kategori`, `penulis`, `thumbnail`, `isi_singkat`, `isi_lengkap`) VALUES
(1, 'Tips Membangun Kebiasaan Membaca Sejak Dini', '12 Januari 2026', 'Edukasi', 'Admin Jembatan Ilmu', 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=800', 'Membaca adalah jendela dunia. Namun, bagaimana cara agar anak-anak menyukai buku sejak kecil? Simak tips lengkapnya di sini...', '<p>Membaca adalah salah satu keterampilan paling penting yang bisa dimiliki seseorang.</p><p>Berikut adalah 3 langkah mudah: Sediakan buku menarik, Jadilah contoh, Buat rutinitas.</p>'),
(2, 'Pentingnya Alat Tulis Ergonomis untuk Pelajar', '10 Januari 2026', 'Produk', 'Dr. Santoso', 'https://images.unsplash.com/photo-1585336261022-680e295ce3fe?q=80&w=800', 'Menggunakan pulpen atau pensil yang tidak nyaman dapat mempengaruhi tulisan dan kesehatan tangan.', '<p>Sering merasa pegal saat menulis catatan panjang di sekolah? Bisa jadi alat tulis yang kamu gunakan tidak ergonomis.</p>'),
(3, 'Review Buku: Filosofi Teras', '05 Januari 2026', 'Review Buku', 'Siti Rahma', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800', 'Buku yang mengajarkan kita untuk hidup lebih tenang di tengah hiruk pikuk dunia modern.', 'Isi review lengkap tentang buku Filosofi Teras...'),
(4, 'Daftar Pemenang Lomba Menulis Cerpen 2025', '01 Januari 2026', 'Event', 'Panitia Lomba', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=800', 'Selamat kepada para pemenang! Berikut adalah daftar nama siswa yang berhasil menjuarai kompetisi.', 'Daftar lengkap pemenang lomba...');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `halaman` int(11) DEFAULT NULL,
  `berat` varchar(20) DEFAULT NULL,
  `sinopsis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `penulis`, `kategori`, `harga`, `cover`, `isbn`, `penerbit`, `halaman`, `berat`, `sinopsis`) VALUES
(1, 'Filosofi Teras', 'Henry Manampiring', 'Self Improvement', 98000, 'uploads/696ad99cf35b4.jpg', '978-602-412-518-9', 'Penerbit Buku Kompas', 320, '0.3 kg', 'Filosofi Teras adalah sebuah buku pengantar filsafat Stoisisme yang dibuat khusus untuk generasi masa kini. Buku ini menjelaskan bagaimana menerapkan gaya hidup Stoa untuk mengatasi emosi negatif.'),
(2, 'Laut Bercerita', 'Leila S. Chudori', 'Novel', 115000, 'uploads/696b1b8b881b9.png', '978-602-424-694-5', 'Kepustakaan Populer Gramedia', 394, '0.4 kg', 'Laut Bercerita bertutur tentang kisah keluarga yang kehilangan, sekumpulan sahabat yang merasakan kekosongan di dada, sekelompok orang yang gemar menyiksa dan berkhianat.'),
(3, 'Atomic Habits', 'James Clear', 'Self Improvement', 108000, 'https://images.unsplash.com/photo-1592496431122-2349e0fbc666?q=80&w=800&auto=format&fit=crop', '978-602-06-3317-6', 'Gramedia Pustaka Utama', 352, '0.35 kg', 'Perubahan kecil yang memberikan hasil luar biasa. James Clear mengungkapkan strategi praktis untuk membentuk kebiasaan baik.'),
(4, 'Si Anak Kuat - Amelia', 'Tere Liye', 'Novel', 80000, 'uploads/696b1a1653216.png', '978-443-01-2222-1', 'Mizan', 800, '0.8 kg', 'Novel ini merupakan serial anak-anak mamak bapak dan bercerita tentang Amelia yang merupakan anak terakhir di keluarga.'),
(5, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Sastra', 145000, 'uploads/696b1a415b7a1.png', '978-979-97312-3-4', 'Lentera Dipantara', 535, '0.5 kg', 'Kisah Minke, seorang pribumi di zaman kolonial Belanda yang berjuang melawan ketidakadilan hukum dan adat istiadat demi cintanya pada Annelies.'),
(6, 'Biologi SMA Kelas X', 'Priwiditya Lingga Mahendra', 'Pelajaran', 85000, 'uploads/696b1a7cc82ed.png', '978-602-298-765-1', 'Erlangga', 450, '0.5 kg', 'Buku pelajaran Biologi untuk SMA/MA Kelas X Kurikulum Merdeka yang disusun secara sistematis dan komunikatif.'),
(7, 'Harry Potter', 'J.K. Rowling', 'Novel', 180000, 'uploads/696b1a97393f4.png', '978-123-456-789-0', 'Gramedia', 400, '0.4 kg', 'Kisah petualangan penyihir muda Harry Potter di sekolah sihir Hogwarts.'),
(8, 'Matematika Dasar', 'Sukino', 'Pelajaran', 45000, 'uploads/696b21c9ced78.png', '978-111-222-333-4', 'Erlangga', 200, '0.2 kg', 'Ringkasan materi matematika dasar untuk persiapan ujian sekolah.'),
(9, 'The Psychology of Money', 'Morgan Housel', 'Self Improvement', 70000, 'uploads/696b22c309906.png', '978-999-41-50', 'BACA', 240, '0.3 kg', 'Kesuksesan dalam mengelola uang tidak selalu tentang apa yang anda ketahui, melainkan tentang bagaimana cara anda berperilaku dengan benar.');

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id` int(11) NOT NULL,
  `kode_diskon` varchar(50) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `potongan` int(11) NOT NULL,
  `min_belanja` int(11) DEFAULT 0,
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diskon`
--

INSERT INTO `diskon` (`id`, `kode_diskon`, `deskripsi`, `potongan`, `min_belanja`, `status`) VALUES
(1, 'HEMAT10', 'Potongan Ceria 10 Ribu', 10000, 50000, 'Aktif'),
(2, 'JEMBATAN50', 'Diskon Jumbo Spesial', 50000, 150000, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `nama_penerima` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `total_bayar` int(11) DEFAULT NULL,
  `status_pembayaran` enum('Pending','Lunas','Batal') DEFAULT 'Pending',
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `nama_penerima`, `email`, `alamat`, `metode_pembayaran`, `total_bayar`, `status_pembayaran`, `tanggal_transaksi`) VALUES
(1, 'Budi Santoso', 'budi@gmail.com', 'Jl. Merdeka No 45, Magelang', 'Tunai (COD)', 100000, 'Lunas', '2026-01-15 10:50:46'),
(2, 'Siti Aminah', 'siti@gmail.com', 'Jl. Mawar No 12, Yogyakarta', 'Transfer Bank', 150000, 'Lunas', '2026-01-15 10:50:46'),
(3, 'Imam', 'imamadi@gmail.com', 'Magelang', 'Tunai (COD)', 100000, 'Lunas', '2026-01-16 01:48:09'),
(4, 'Adi', 'imamadi@gmail.com', 'Salatiga', 'Tunai (COD)', 88000, 'Lunas', '2026-01-16 01:48:56'),
(5, 'Imam', 'imamadi@gmail.com', 'Magelang', 'Transfer Bank', 172000, 'Lunas', '2026-01-16 18:10:08'),
(6, 'Imam', 'imamadi@gmail.com', 'salatiga', 'Transfer Bank', 98000, 'Lunas', '2026-02-05 22:45:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Admin Jembatan Ilmu', 'admin@jembatanilmu.com', 'admin123', 'admin'),
(2, 'Budi Santoso', 'budi@gmail.com', '123456', 'member'),
(3, 'Imam Adi W', 'imamadi@gmail.com', 'ganteng banget', 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atk`
--
ALTER TABLE `atk`
  ADD PRIMARY KEY (`id_atk`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_diskon` (`kode_diskon`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atk`
--
ALTER TABLE `atk`
  MODIFY `id_atk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
