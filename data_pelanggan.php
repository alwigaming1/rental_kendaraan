<?php 
include 'config.php'; cek_login(); 

if(isset($_GET['hapus'])){
    $id = anti_injection($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id='$id'");
    header("Location: data_pelanggan.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Pelanggan - <?= $conf['app_name'] ?></title>
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
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Data Pelanggan</h1>
                <p class="text-gray-500 text-sm">Kelola data penyewa (Customer).</p>
            </div>
            <a href="form_pelanggan.php" class="bg-<?= $conf['color'] ?>-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Tambah Pelanggan
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-<?= $conf['color'] ?>-50 text-<?= $conf['color'] ?>-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Identitas (NIK/Nama)</th>
                            <th class="p-4">Kontak (HP)</th>
                            <th class="p-4">Alamat</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        $sql = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY id DESC");
                        while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <p class="font-bold text-slate-800"><?= $row['nama_pelanggan'] ?></p>
                                <p class="text-xs text-gray-500 mt-1 font-mono">NIK: <?= $row['nik'] ?></p>
                            </td>
                            <td class="p-4 text-gray-700">
                                <i class="fa-solid fa-phone mr-1 text-green-500"></i> <?= $row['no_hp'] ?>
                            </td>
                            <td class="p-4 text-gray-600"><?= $row['alamat'] ?></td>
                            <td class="p-4 text-center">
                                <a href="form_pelanggan.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700 mx-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="data_pelanggan.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus pelanggan ini?')" class="text-red-500 hover:text-red-700 mx-1"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>