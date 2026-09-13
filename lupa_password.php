<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_login'])) {
    header("Location: beranda.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identitas = mysqli_real_escape_string($conn, $_POST['identitas']);
    $password_baru = $_POST['password_baru'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$identitas' OR nama = '$identitas'");

    if (mysqli_num_rows($result) > 0) {
        $update = mysqli_query($conn, "UPDATE users SET password = '$password_baru' WHERE email = '$identitas' OR nama = '$identitas'");
        if ($update) {
            $success = "Password berhasil diubah! Silakan login.";
        } else {
            $error = "Gagal mengubah password.";
        }
    } else {
        $error = "Username atau Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Jembatan Ilmu Store</title>
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
            <a href="beranda.php">
                <img src="Jembatan Ilmu.png" alt="Logo" class="h-16 mx-auto mb-4 object-contain">
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Lupa Password</h2>
            <p class="text-gray-500 text-sm">Masukkan username atau email untuk mereset password Anda</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm" role="alert">
                <p class="font-bold">Gagal</p>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-6 text-sm" role="alert">
                <p class="font-bold">Berhasil</p>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="identitas">
                    Username atau Alamat Email
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="identitas" name="identitas" type="text" placeholder="Username atau Email" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password_baru">
                    Password Baru
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                           id="password_baru" name="password_baru" type="password" placeholder="Password Baru" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 cursor-pointer" onclick="togglePassword('password_baru', this)">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <button class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 shadow-lg transform hover:-translate-y-1" type="submit">
                Reset Password
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Ingat password? <a href="login.php" class="text-primary font-bold hover:underline">Login di sini</a>
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
