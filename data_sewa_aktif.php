<?php 
include 'config.php'; cek_login(); 

// --- LOGIC PENGEMBALIAN KENDARAAN (Return) ---
if(isset($_GET['action']) && $_GET['action'] == 'kembali' && isset($_GET['id'])){
    $id_trx = anti_injection($koneksi, $_GET['id']);
    
    // 1. Ambil Data Transaksi
    $q_cek = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id_trx' AND status_transaksi='berlangsung'");
    
    if(mysqli_num_rows($q_cek) > 0){
        $trx = mysqli_fetch_assoc($q_cek);
        $kendaraan_id = $trx['kendaraan_id'];
        $tgl_kembali_rencana = $trx['tgl_kembali_rencana'];
        $tgl_kembali_real = date('Y-m-d H:i:s'); // Waktu saat ini

        // 2. Hitung Denda (Jika Terlambat)
        $denda = 0;
        $denda_per_hari = 100000; // CONTOH: Denda 100rb per hari (Bisa disesuaikan)
        
        $date_rencana = new DateTime($tgl_kembali_rencana);
        $date_real = new DateTime($tgl_kembali_real);
        
        // Cek jika tanggal real lebih besar dari rencana (abaikan jam untuk toleransi, atau hitung full)
        if($date_real > $date_rencana){
            // Hitung selisih hari
            $diff = $date_real->diff($date_rencana);
            $hari_telat = $diff->days;
            
            // Pastikan benar-benar lewat hari (bukan hanya beda jam di hari yang sama jika logic ketat)
            // Logic sederhana: Jika tgl_real > tgl_rencana maka denda
            if($date_real->format('Y-m-d') > $date_rencana->format('Y-m-d')){
                 $denda = $hari_telat * $denda_per_hari;
            }
        }

        mysqli_begin_transaction($koneksi);
        $ok = true;

        // 3. Update Transaksi (Set Selesai & Isi Denda)
        $q_up_trx = "UPDATE transaksi SET tgl_kembali_real='$tgl_kembali_real', status_transaksi='selesai', denda='$denda' WHERE id='$id_trx'";
        if(!mysqli_query($koneksi, $q_up_trx)) $ok = false;

        // 4. Update Kendaraan (Set Tersedia)
        $q_up_ken = "UPDATE kendaraan SET status_kendaraan='tersedia' WHERE id='$kendaraan_id'";
        if(!mysqli_query($koneksi, $q_up_ken)) $ok = false;

        if($ok){
            mysqli_commit($koneksi);
            $msg_denda = ($denda > 0) ? " (Ada Denda Keterlambatan: Rp ".number_format($denda).")" : "";
            echo "<script>alert('Kendaraan Berhasil Dikembalikan! Transaksi Selesai.$msg_denda'); window.location='data_sewa_aktif.php';</script>";
        } else {
            mysqli_rollback($koneksi);
            echo "<script>alert('Gagal Proses Kembali: ".mysqli_error($koneksi)."'); window.location='data_sewa_aktif.php';</script>";
        }
    } else {
        echo "<script>alert('Data transaksi tidak valid atau sudah selesai.'); window.location='data_sewa_aktif.php';</script>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sewa Aktif - <?= $conf['app_name'] ?></title>
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
                <h1 class="text-2xl font-bold text-slate-800">Sewa Berjalan (Aktif)</h1>
                <p class="text-gray-500 text-sm">Daftar kendaraan yang sedang dibawa pelanggan.</p>
            </div>
            <a href="form_sewa.php" class="bg-<?= $conf['color'] ?>-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Transaksi Baru
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-<?= $conf['color'] ?>-50 text-<?= $conf['color'] ?>-700 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-4">Kode TRX</th>
                            <th class="p-4">Unit Kendaraan</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4 text-center">Tanggal Sewa</th>
                            <th class="p-4 text-center bg-red-50 text-red-600">Jatuh Tempo</th>
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
                            WHERE t.status_transaksi = 'berlangsung'
                            ORDER BY t.tgl_kembali_rencana ASC
                        ");
                        
                        if(mysqli_num_rows($sql) > 0){
                            while($row = mysqli_fetch_array($sql)){
                                $hari_ini = date('Y-m-d');
                                $rencana = date('Y-m-d', strtotime($row['tgl_kembali_rencana']));
                                $is_late = ($hari_ini > $rencana);
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-mono font-bold text-slate-700"><?= $row['kode_trx'] ?></td>
                            <td class="p-4">
                                <p class="font-bold text-<?= $conf['color'] ?>-700"><?= $row['nama_kendaraan'] ?></p>
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs border"><?= $row['no_polisi'] ?></span>
                            </td>
                            <td class="p-4">
                                <p class="font-medium"><?= $row['nama_pelanggan'] ?></p>
                                <a href="https://wa.me/<?= $row['no_hp'] ?>" target="_blank" class="text-xs text-green-600 hover:underline">
                                    <i class="fa-brands fa-whatsapp"></i> <?= $row['no_hp'] ?>
                                </a>
                            </td>
                            <td class="p-4 text-center text-gray-600"><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?></td>
                            
                            <td class="p-4 text-center font-bold <?= $is_late ? 'text-red-600' : 'text-green-600' ?>">
                                <?= date('d/m/Y', strtotime($row['tgl_kembali_rencana'])) ?>
                                <?php if($is_late): ?>
                                    <div class="mt-1">
                                        <span class="text-[10px] bg-red-600 text-white px-2 py-0.5 rounded-full animate-pulse">TERLAMBAT</span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="p-4 text-center">
                                <a href="data_sewa_aktif.php?action=kembali&id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Konfirmasi Pengembalian Kendaraan?\n\nJika terlambat, denda akan dihitung otomatis Rp 100.000/hari.')" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-rotate-left"></i> Proses Kembali
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="6" class="p-8 text-center text-gray-400 font-medium bg-white">Tidak ada kendaraan yang sedang disewa saat ini.</td></tr>';
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>