<?php include 'config.php'; cek_login(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Transaksi - <?= $conf['app_name'] ?></title>
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
                <h1 class="text-2xl font-bold text-slate-800">Riwayat Transaksi</h1>
                <p class="text-gray-500 text-sm">Laporan sewa yang telah selesai.</p>
            </div>
            
            <a href="cetak_riwayat.php" target="_blank" class="bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded-lg font-bold hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Kode TRX</th>
                            <th class="p-4">Unit / Pelanggan</th>
                            <th class="p-4 text-center">Tgl Sewa</th>
                            <th class="p-4 text-center">Tgl Kembali</th>
                            <th class="p-4 text-center">Total Bayar</th>
                            <th class="p-4 text-center">Denda</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        $sql = mysqli_query($koneksi, "
                            SELECT t.*, k.nama_kendaraan, p.nama_pelanggan 
                            FROM transaksi t
                            JOIN kendaraan k ON t.kendaraan_id = k.id
                            JOIN pelanggan p ON t.pelanggan_id = p.id
                            WHERE t.status_transaksi = 'selesai'
                            ORDER BY t.tgl_kembali_real DESC
                        ");
                        
                        while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-mono text-gray-600"><?= $row['kode_trx'] ?></td>
                            <td class="p-4">
                                <p class="font-bold text-slate-700"><?= $row['nama_kendaraan'] ?></p>
                                <p class="text-xs text-gray-500"><?= $row['nama_pelanggan'] ?></p>
                            </td>
                            <td class="p-4 text-center text-gray-600"><?= date('d/m/y', strtotime($row['tgl_sewa'])) ?></td>
                            <td class="p-4 text-center font-medium text-green-700">
                                <?= date('d/m/y H:i', strtotime($row['tgl_kembali_real'])) ?>
                            </td>
                            <td class="p-4 text-center font-bold">Rp <?= number_format($row['total_bayar']) ?></td>
                            <td class="p-4 text-center text-red-600 font-bold">
                                <?= ($row['denda'] > 0) ? "Rp ".number_format($row['denda']) : "-" ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">SELESAI</span>
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