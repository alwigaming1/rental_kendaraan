<?php 
include 'config.php'; cek_login(); 

// --- LOGIC HAPUS DATA ---
if(isset($_GET['hapus'])){
    $id = anti_injection($koneksi, $_GET['hapus']);
    
    // Cek apakah kategori sedang digunakan oleh kendaraan
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan WHERE kategori_id='$id'");
    $jml = mysqli_fetch_assoc($cek)['total'];

    if($jml > 0){
        echo "<script>alert('Gagal! Kategori ini masih digunakan oleh $jml kendaraan. Hapus atau ubah data kendaraan terlebih dahulu.'); window.location='data_kategori.php';</script>";
    } else {
        if(mysqli_query($koneksi, "DELETE FROM kategori WHERE id='$id'")){
            echo "<script>alert('Kategori berhasil dihapus!'); window.location='data_kategori.php';</script>";
        } else {
            echo "<script>alert('Gagal hapus: ".mysqli_error($koneksi)."'); window.location='data_kategori.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kategori - <?= $conf['app_name'] ?></title>
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
                <h1 class="text-2xl font-bold text-slate-800">Kategori Kendaraan</h1>
                <p class="text-gray-500 text-sm">Klasifikasi jenis kendaraan (Mobil/Motor).</p>
            </div>
            <a href="form_kategori.php" class="bg-<?= $conf['color'] ?>-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kategori
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-<?= $conf['color'] ?>-50 text-<?= $conf['color'] ?>-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4 w-16 text-center">No</th>
                            <th class="p-4">Nama Kategori</th>
                            <th class="p-4 text-center">Jumlah Unit</th>
                            <th class="p-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        // Query ini menghitung jumlah kendaraan di setiap kategori secara otomatis
                        $sql = mysqli_query($koneksi, "
                            SELECT k.*, 
                            (SELECT COUNT(*) FROM kendaraan WHERE kategori_id = k.id) as jumlah_unit 
                            FROM kategori k 
                            ORDER BY k.nama_kategori ASC
                        ");
                        $no = 1;
                        while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 text-center font-bold text-gray-500"><?= $no++ ?></td>
                            <td class="p-4 font-bold text-slate-700"><?= $row['nama_kategori'] ?></td>
                            <td class="p-4 text-center">
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">
                                    <?= $row['jumlah_unit'] ?> Unit
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="form_kategori.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="data_kategori.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus kategori ini?')" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <?php if(mysqli_num_rows($sql) == 0): ?>
                    <div class="p-6 text-center text-gray-400">Belum ada data kategori.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>