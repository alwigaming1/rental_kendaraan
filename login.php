<?php
include 'config.php';

// Redirect jika sudah login
if(isset($_SESSION['status']) && $_SESSION['status'] == 'login'){
    header("Location: dashboard.php");
    exit;
}

$msg = "";
if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = md5($_POST['password']); 

    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    
    if(mysqli_num_rows($cek) > 0){
        $d = mysqli_fetch_array($cek);
        
        $_SESSION['status'] = "login";
        $_SESSION['user_id'] = $d['id'];
        $_SESSION['nama_lengkap'] = $d['nama_lengkap']; // Pastikan kolom ini ada
        $_SESSION['role'] = $d['role']; 

        header("Location: dashboard.php"); 
        exit;
    } else {
        $msg = "Username atau Password Salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - <?= $conf['app_name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .bg-pattern {
            background-color: #f0fdfa; /* Teal-50 */
            background-image: radial-gradient(#ccfbf1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-pattern h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-2xl border border-teal-100 shadow-teal-100 transform hover:scale-[1.01] transition duration-300">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-<?= $conf['color'] ?>-100 text-<?= $conf['color'] ?>-600 mb-4 shadow-md">
                <i class="fa-solid fa-car text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">DIGI<span class="text-<?= $conf['color'] ?>-600">RENT</span></h1>
            <p class="text-gray-500 text-sm mt-1">Sistem Informasi Rental Kendaraan</p>
        </div>

        <?php if(!empty($msg)) echo "<p class='bg-red-100 text-red-600 p-3 rounded-xl text-center text-sm mb-6 border border-red-200'>$msg</p>"; ?>
        
        <form method="POST">
            <div class="mb-4 relative">
                <label class="block text-gray-600 text-xs font-bold mb-1 uppercase">Username</label>
                <i class="fa-solid fa-user absolute left-4 top-10 text-gray-400"></i>
                <input type="text" name="username" class="w-full px-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 transition" placeholder="Masukkan Username" required>
            </div>
            <div class="mb-6 relative">
                <label class="block text-gray-600 text-xs font-bold mb-1 uppercase">Password</label>
                <i class="fa-solid fa-lock absolute left-4 top-10 text-gray-400"></i>
                <input type="password" name="password" class="w-full px-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 transition" placeholder="Masukkan Password" required>
            </div>
            
            <button type="submit" name="login" class="w-full bg-<?= $conf['color'] ?>-600 text-white font-bold py-3.5 rounded-xl hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg shadow-<?= $conf['color'] ?>-200 text-lg">
                MASUK SEKARANG
            </button>
        </form>
        
        <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-bold text-gray-500 mb-2 uppercase flex items-center gap-2"><i class="fa-solid fa-circle-info text-<?= $conf['color'] ?>-500"></i> Info Akun Demo:</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-100">
                    <span class="font-medium text-gray-700"><i class="fa-solid fa-user-tie text-blue-500 mr-2"></i> Admin</span>
                    <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-600">admin / 123</code>
                </div>
                <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-100">
                    <span class="font-medium text-gray-700"><i class="fa-solid fa-user-gear text-orange-500 mr-2"></i> Petugas</span>
                    <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-600">petugas / 123</code>
                </div>
            </div>
        </div>
        
    </div>

</body>
</html>