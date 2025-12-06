<?php 
include 'config.php'; cek_login(); 

// LOGIC SETUJUI BOOKING
if(isset($_GET['action']) && $_GET['action'] == 'setuju' && isset($_GET['id'])){
    $id_trx = anti_injection($koneksi, $_GET['id']);
    
    // 1. Ambil ID Kendaraan
    $trx = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT kendaraan_id FROM transaksi WHERE id='$id_trx'"));
    $kendaraan_id = $trx['kendaraan_id'];

    mysqli_begin_transaction($koneksi);
    $ok = true;

    // 2. Update Status Transaksi -> berlangsung
    if(!mysqli_query($koneksi, "UPDATE transaksi SET status_transaksi='berlangsung' WHERE id='$id_trx'")) $ok = false;

    // 3. Update Status Kendaraan -> disewa
    if(!mysqli_query($koneksi, "UPDATE kendaraan SET status_kendaraan='disewa' WHERE id='$kendaraan_id'")) $ok = false;

    if($ok){
        mysqli_commit($koneksi);
        echo "<script>alert('Booking Disetujui! Masuk ke Sewa Aktif.'); window.location='data_sewa_aktif.php';</script>";
    } else {
        mysqli_rollback($koneksi);
        echo "<script>alert('Gagal memproses.'); window.location='data_booking.php';</script>";
    }
}

// LOGIC TOLAK / BATAL
if(isset($_GET['action']) && $_GET['action'] == 'batal' && isset($_GET['id'])){
    $id_trx = anti_injection($koneksi, $_GET['id']);
    mysqli_query($koneksi, "UPDATE transaksi SET status_transaksi='batal' WHERE id='$id_trx'");
    echo "<script>alert('Booking Dibatalkan.'); window.location='data_booking.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Permintaan Booking - <?= $conf['app_name'] ?></title>
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
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Permintaan Booking Masuk</h1>
        <p class="text-gray-500 text-sm mb-6">Daftar pesanan dari website yang menunggu konfirmasi.</p>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-orange-50 text-orange-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Kode / Tgl</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Unit Kendaraan</th>
                            <th class="p-4 text-center">Lama</th>
                            <th class="p-4 text-center">Total Biaya</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                        $sql = mysqli_query($koneksi, "
                            SELECT t.*, k.nama_kendaraan, k.no_polisi, p.nama_pelanggan, p.no_hp 
                            FROM transaksi t
                            JOIN kendaraan k ON t.kendaraan_id = k.id
                            JOIN pelanggan p ON t.pelanggan_id = p.id
                            WHERE t.status_transaksi = 'booking'
                            ORDER BY t.id DESC
                        ");
                        
                        if(mysqli_num_rows($sql) > 0){
                            while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <p class="font-bold text-slate-800"><?= $row['kode_trx'] ?></p>
                                <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?></p>
                            </td>
                            <td class="p-4">
                                <p class="font-medium"><?= $row['nama_pelanggan'] ?></p>
                                <p class="text-xs text-gray-500"><i class="fa-solid fa-phone"></i> <?= $row['no_hp'] ?></p>
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-teal-700"><?= $row['nama_kendaraan'] ?></p>
                                <span class="bg-gray-100 px-2 rounded text-xs text-gray-600"><?= $row['no_polisi'] ?></span>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-600"><?= $row['lama_sewa_hari'] ?> Hari</td>
                            <td class="p-4 text-center font-bold text-slate-800">Rp <?= number_format($row['total_bayar']) ?></td>
                            
                            <td class="p-4 text-center flex gap-2 justify-center">
                                <a href="data_booking.php?id=<?= $row['id'] ?>&action=setuju" onclick="return confirm('Setujui Sewa ini? Status unit akan berubah jadi DISEWA.')" class="bg-teal-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-teal-700 transition shadow-md">
                                    <i class="fa-solid fa-check"></i> Setujui
                                </a>
                                <a href="data_booking.php?id=<?= $row['id'] ?>&action=batal" onclick="return confirm('Batalkan booking ini?')" class="bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-200 transition">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="6" class="p-8 text-center text-gray-400 font-medium">Belum ada booking baru masuk.</td></tr>';
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>