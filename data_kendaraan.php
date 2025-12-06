<?php 
include 'config.php'; cek_login(); 

// --- LOGIC HAPUS DATA ---
if(isset($_GET['hapus'])){
    $id = anti_injection($koneksi, $_GET['hapus']);
    if(mysqli_query($koneksi, "DELETE FROM kendaraan WHERE id='$id'")){
        echo "<script>alert('Data kendaraan berhasil dihapus!'); window.location='data_kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus data: ".mysqli_error($koneksi)."'); window.location='data_kendaraan.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Kendaraan - <?= $conf['app_name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-[#F3F4F6] text-slate-800"
      x-data="{ isSidebarOpen: window.innerWidth >= 1024 }"
      x-init="() => {
          if (window.innerWidth >= 1024) { isSidebarOpen = true; }
          window.addEventListener('resize', () => { if (window.innerWidth >= 1024) { isSidebarOpen = true; } });
      }">

    <?php include 'sidebar.php'; ?>
    
    <div class="lg:ml-64 p-4 md:p-8 pt-20 lg:pt-8 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Armada Kendaraan</h1>
                <p class="text-gray-500 text-sm">Kelola data mobil dan motor rental.</p>
            </div>
            <a href="form_kendaraan.php" class="bg-<?= $conf['color'] ?>-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Unit
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-<?= $conf['color'] ?>-50 text-<?= $conf['color'] ?>-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Info Kendaraan</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Harga / Hari</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        $sql = mysqli_query($koneksi, "
                            SELECT k.*, c.nama_kategori 
                            FROM kendaraan k
                            LEFT JOIN kategori c ON k.kategori_id = c.id 
                            ORDER BY k.id DESC
                        ");
                        while($row = mysqli_fetch_array($sql)){
                            $status_color = ($row['status_kendaraan'] == 'tersedia') ? 'bg-green-100 text-green-700' : 
                                           (($row['status_kendaraan'] == 'disewa') ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700');
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800"><?= $row['nama_kendaraan'] ?></p>
                                        <p class="text-xs text-gray-500 font-mono bg-gray-100 px-1 rounded inline-block mt-0.5"><?= $row['no_polisi'] ?></p>
                                        <span class="text-xs text-gray-400 ml-1"><?= $row['merk'] ?> (<?= $row['tahun'] ?>)</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold"><?= $row['nama_kategori'] ?></span>
                            </td>
                            <td class="p-4 font-bold text-<?= $conf['color'] ?>-600">
                                Rp <?= number_format($row['harga_sewa']) ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="<?= $status_color ?> px-3 py-1 rounded-full text-xs font-bold uppercase"><?= $row['status_kendaraan'] ?></span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="form_kendaraan.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700 mx-1" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="data_kendaraan.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus permanen?')" class="text-red-500 hover:text-red-700 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></a>
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