<?php
include 'config.php';
cek_login();

$user_id = $_SESSION['user_id'];
$q_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
$data_user = mysqli_fetch_assoc($q_user);

$msg = "";
$msg_type = "";

// LOGIC GANTI PASSWORD
if(isset($_POST['ganti_pass'])){
    $pass_baru = $_POST['pass_baru'];
    $pass_konf = $_POST['pass_konf'];
    
    if ($pass_baru !== $pass_konf) {
        $msg = "Konfirmasi password tidak cocok!";
        $msg_type = "error";
    } else {
        $pass_hash = md5($pass_baru); // Sesuaikan dengan hashing Anda
        $q_update = "UPDATE users SET password='$pass_hash' WHERE id='$user_id'";
        if(mysqli_query($koneksi, $q_update)){
            $msg = "Password berhasil diubah!";
            $msg_type = "success";
        } else {
            $msg = "Gagal update database.";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil - <?= $conf['app_name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-[#F3F4F6] text-slate-800"
      x-data="{ isSidebarOpen: window.innerWidth >= 1024 }"
      x-init="() => { if (window.innerWidth >= 1024) isSidebarOpen = true; window.addEventListener('resize', () => { if (window.innerWidth >= 1024) isSidebarOpen = true; }); }">

    <?php include 'sidebar.php'; ?>

    <div class="lg:ml-64 p-4 md:p-8 pt-20 lg:pt-8 min-h-screen">
        
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Pengaturan Akun</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg text-slate-700 mb-4 border-b pb-2">Profil Saya</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Nama Lengkap</label>
                    <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-medium">
                        <?= $data_user['nama_lengkap'] ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Username</label>
                    <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-medium text-gray-600">
                        <?= $data_user['username'] ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Jabatan / Role</label>
                    <span class="bg-<?= $conf['color'] ?>-100 text-<?= $conf['color'] ?>-700 px-3 py-1 rounded-lg text-sm font-bold uppercase">
                        <?= $data_user['role'] ?>
                    </span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg text-slate-700 mb-4 border-b pb-2">Ganti Password</h3>

                <?php if ($msg): ?>
                    <div class="mb-4 p-3 rounded-lg text-sm font-bold text-center <?= $msg_type == 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                        <?= $msg ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Password Baru</label>
                        <input type="password" name="pass_baru" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Konfirmasi Password</label>
                        <input type="password" name="pass_konf" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                    
                    <button type="submit" name="ganti_pass" class="w-full bg-<?= $conf['color'] ?>-600 text-white font-bold py-3 rounded-lg hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg">
                        Simpan Password
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>