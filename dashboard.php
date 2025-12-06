<?php 
include 'config.php'; 
cek_login(); 

// --- 1. AMBIL DATA STATISTIK ---
// Total Kendaraan
$total_unit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kendaraan"))['total'];

// Unit Sedang Disewa
$unit_disewa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kendaraan WHERE status_kendaraan='disewa'"))['total'];

// Unit Tersedia
$unit_ready = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kendaraan WHERE status_kendaraan='tersedia'"))['total'];

// Total Pelanggan
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pelanggan"))['total'];

// --- 2. AMBIL 5 TRANSAKSI TERBARU ---
$q_trx = mysqli_query($koneksi, "
    SELECT t.*, k.nama_kendaraan, k.no_polisi, p.nama_pelanggan 
    FROM transaksi t
    JOIN kendaraan k ON t.kendaraan_id = k.id
    JOIN pelanggan p ON t.pelanggan_id = p.id
    ORDER BY t.id DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard - <?= $conf['app_name'] ?></title>
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
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Dashboard Rental</h1>
                <p class="text-gray-500 text-sm mt-1">Ringkasan operasional hari ini (<?= date('d M Y') ?>)</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold"><?= $_SESSION['nama_lengkap'] ?? 'Administrator' ?></p>
                    <p class="text-xs text-green-500">● Online</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-<?= $conf['color'] ?>-100 text-<?= $conf['color'] ?>-600 flex items-center justify-center font-bold border border-<?= $conf['color'] ?>-200">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            
            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Armada</p>
                        <h3 class="text-4xl font-extrabold text-slate-800 mt-1"><?= $total_unit ?></h3>
                    </div>
                    <div class="bg-indigo-100 p-4 rounded-xl text-indigo-600">
                        <i class="fa-solid fa-car-side text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Unit Ready</p>
                        <h3 class="text-4xl font-extrabold text-slate-800 mt-1"><?= $unit_ready ?></h3>
                    </div>
                    <div class="bg-green-100 p-4 rounded-xl text-green-600">
                        <i class="fa-solid fa-check-circle text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Sedang Disewa</p>
                        <h3 class="text-4xl font-extrabold text-slate-800 mt-1"><?= $unit_disewa ?></h3>
                    </div>
                    <div class="bg-orange-100 p-4 rounded-xl text-orange-600">
                        <i class="fa-solid fa-key text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Pelanggan</p>
                        <h3 class="text-4xl font-extrabold text-slate-800 mt-1"><?= $total_pelanggan ?></h3>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-xl text-blue-600">
                        <i class="fa-solid fa-users text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-bold text-slate-800 mb-4">Transaksi Terbaru</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Kode TRX</th>
                            <th class="p-4">Kendaraan</th>
                            <th class="p-4">Penyewa</th>
                            <th class="p-4">Tgl Sewa</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        if(mysqli_num_rows($q_trx) > 0){
                            while($row = mysqli_fetch_array($q_trx)){
                                $status_badge = ($row['status_transaksi'] == 'berlangsung') ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600';
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-mono font-bold text-slate-700"><?= $row['kode_trx'] ?></td>
                            <td class="p-4">
                                <span class="font-bold"><?= $row['nama_kendaraan'] ?></span>
                                <span class="text-xs bg-gray-200 px-1 rounded ml-1"><?= $row['no_polisi'] ?></span>
                            </td>
                            <td class="p-4"><?= $row['nama_pelanggan'] ?></td>
                            <td class="p-4 text-gray-500"><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?></td>
                            <td class="p-4 text-center">
                                <span class="<?= $status_badge ?> px-2 py-1 rounded text-xs font-bold uppercase"><?= $row['status_transaksi'] ?></span>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada transaksi.</td></tr>';
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>