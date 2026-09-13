<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_login'])) {
    header("Location: beranda.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' OR nama = '$email'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        // Cek password (gunakan password_verify jika hash, tapi ini string biasa sesuai request awal)
        if ($password === $row['password']) {
            $_SESSION['user_login'] = true;
            $_SESSION['user_nama'] = $row['nama'];
            $_SESSION['user_role'] = $row['role']; // 'admin' atau 'member'
            $_SESSION['user_email'] = $row['email'];

            // Redirect berdasarkan role
            if($row['role'] == 'admin'){
                header("Location: admin.php");
            } else {
                header("Location: beranda.php");
            }
            exit;
        }
    }
    $error = "Email atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jembatan Ilmu Store</title>
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
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen overflow-y-scroll">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-primary">
        
        <div class="text-center mb-8">
            <img src="Jembatan Ilmu.png" alt="Logo" class="h-16 mx-auto mb-4 object-contain">
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali</h2>
            <p class="text-gray-500 text-sm">Silakan login untuk melanjutkan belanja</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm" role="alert">
                <p class="font-bold">Gagal Login</p>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Username atau Alamat Email
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="email" name="email" type="text" placeholder="Username atau nama@email.com" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="password" name="password" type="password" placeholder="********" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 cursor-pointer" onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" class="form-checkbox text-primary rounded focus:ring-primary">
                    <span class="ml-2">Ingat Saya</span>
                </label>
                <a href="lupa_password.php" class="inline-block align-baseline font-bold text-sm text-primary hover:text-blue-800">
                    Lupa Password?
                </a>
            </div>

            <button class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 shadow-lg transform hover:-translate-y-1" type="submit">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Belum punya akun? <a href="register.php" class="text-primary font-bold hover:underline">Daftar di sini</a>
        </div>
        
        <div class="mt-6 p-3 bg-blue-50 text-blue-800 text-xs rounded border border-blue-200 text-center">
            <p class="font-bold">Akun Admin:</p>
            <p>Email: admin@jembatanilmu.com</p>
            <p>Pass: admin123</p>
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