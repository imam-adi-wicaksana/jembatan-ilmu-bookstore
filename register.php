<?php
session_start();
require 'koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $konfirmasi = mysqli_real_escape_string($conn, $_POST['konfirmasi_password']);

    // Cek email kembar
    $cek = query("SELECT email FROM users WHERE email = '$email'");
    if (!empty($cek)) {
        $error = "Email sudah terdaftar!";
    } elseif ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        // Insert ke DB
        // Idealnya password di-hash: $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'member')";
        mysqli_query($conn, $query);
        
        echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Jembatan Ilmu Store</title>
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
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen py-10 px-4 overflow-y-scroll">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-primary">
        
        <div class="text-center mb-8">
            <a href="beranda.php">
                <img src="Jembatan Ilmu.png" alt="Logo" class="h-16 mx-auto mb-4 object-contain">
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm">Bergabunglah untuk akses belanja lebih mudah</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm flex items-start gap-2" role="alert">
                <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                <div>
                    <p class="font-bold">Gagal Mendaftar</p>
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           name="nama" type="text" placeholder="Contoh: Budi Santoso" value="<?php echo isset($_POST['nama']) ? $_POST['nama'] : ''; ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           name="email" type="email" placeholder="nama@email.com" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-10 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="password" name="password" type="password" placeholder="Minimal 6 karakter" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 cursor-pointer" onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-10 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="konfirmasi_password" name="konfirmasi_password" type="password" placeholder="Ulangi password" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 cursor-pointer" onclick="togglePassword('konfirmasi_password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="flex items-start mb-6">
                <div class="flex items-center h-5">
                    <input id="terms" type="checkbox" required class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-500">Saya menyetujui <a href="#" class="font-medium text-primary hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="font-medium text-primary hover:underline">Kebijakan Privasi</a>.</label>
                </div>
            </div>

            <button class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 shadow-lg transform hover:-translate-y-1" type="submit">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Sudah punya akun? <a href="login.php" class="text-primary font-bold hover:underline">Login di sini</a>
        </div>

    </div>

    <script>
        function togglePassword(inputId, iconContainer) {
            const input = document.getElementById(inputId);
            const icon = iconContainer.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>