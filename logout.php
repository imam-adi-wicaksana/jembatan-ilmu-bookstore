<?php
session_start();

// Menghapus semua variabel session
$_SESSION = [];

// Menghapus cookie session (jika ada)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Menghancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan
echo "<script>
        alert('Anda berhasil logout.');
        window.location.href = 'login.php';
      </script>";
exit;
?>