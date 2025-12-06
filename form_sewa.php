<?php 
include 'config.php'; cek_login(); 

$kode_trx = "TRX-" . date("ymdHis"); // Generate Kode Unik
$tgl_sewa = date('Y-m-d');
$tombol = "Proses Sewa";

if(isset($_POST['simpan_sewa'])){
    $kode = $_POST['kode_trx'];
    $pelanggan_id = anti_injection($koneksi, $_POST['pelanggan_id']);
    $kendaraan_id = anti_injection($koneksi, $_POST['kendaraan_id']);
    $tgl_ambil = anti_injection($koneksi, $_POST['tgl_sewa']);
    $lama = anti_injection($koneksi, $_POST['lama']);
    $user_id = $_SESSION['user_id']; 

    // 1. Ambil Harga Sewa Kendaraan untuk hitung total
    $q_harga = mysqli_query($koneksi, "SELECT harga_sewa FROM kendaraan WHERE id='$kendaraan_id'");
    $d_harga = mysqli_fetch_assoc($q_harga);
    $harga_per_hari = $d_harga['harga_sewa'];
    
    // 2. Hitung Total & Tanggal Kembali Rencana
    $total_bayar = $harga_per_hari * $lama;
    $tgl_kembali_rencana = date('Y-m-d', strtotime($tgl_ambil . ' + ' . $lama . ' days'));

    mysqli_begin_transaction($koneksi);
    $ok = true;

    // 3. Insert ke Transaksi (Status: berlangsung)
    $q_trx = "INSERT INTO transaksi (kode_trx, pelanggan_id, kendaraan_id, tgl_sewa, tgl_kembali_rencana, lama_sewa_hari, total_bayar, status_transaksi, user_id) 
              VALUES ('$kode', '$pelanggan_id', '$kendaraan_id', '$tgl_ambil', '$tgl_kembali_rencana', '$lama', '$total_bayar', 'berlangsung', '$user_id')";
    
    if(!mysqli_query($koneksi, $q_trx)) $ok = false;

    // 4. Update Status Kendaraan jadi 'disewa'
    $q_update_mobil = "UPDATE kendaraan SET status_kendaraan='disewa' WHERE id='$kendaraan_id'";
    if(!mysqli_query($koneksi, $q_update_mobil)) $ok = false;

    if($ok){
        mysqli_commit($koneksi);
        // REDIRECT KE DATA SEWA BERJALAN (data_sewa_aktif.php)
        echo "<script>alert('Transaksi Berhasil! Kendaraan telah masuk ke Sewa Berjalan.'); window.location='data_sewa_aktif.php';</script>";
    } else {
        mysqli_rollback($koneksi);
        echo "<script>alert('Gagal Transaksi: ".mysqli_error($koneksi)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sewa Baru - <?= $conf['app_name'] ?></title>
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
        <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-3">Transaksi Sewa Baru</h1>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 max-w-4xl">
            <form method="POST">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Kode Transaksi</label>
                        <input type="text" name="kode_trx" value="<?= $kode_trx ?>" class="w-full border p-2.5 rounded-lg bg-gray-100 text-gray-500 font-mono" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Tanggal Sewa</label>
                        <input type="date" name="tgl_sewa" value="<?= $tgl_sewa ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">Pilih Pelanggan</label>
                    <select name="pelanggan_id" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 bg-white" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php 
                        $q_p = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                        while($p = mysqli_fetch_array($q_p)){
                            echo "<option value='{$p['id']}'>{$p['nama_pelanggan']} (NIK: {$p['nik']})</option>";
                        }
                        ?>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pelanggan belum ada? <a href="form_pelanggan.php" class="text-<?= $conf['color'] ?>-600 hover:underline">Tambah Pelanggan Baru</a></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold mb-1">Pilih Kendaraan (Tersedia)</label>
                        <select name="kendaraan_id" id="kendaraan" onchange="updateHarga()" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 bg-white" required>
                            <option value="" data-harga="0">-- Pilih Unit --</option>
                            <?php 
                            // Hanya tampilkan kendaraan yang TERSEDIA
                            $q_k = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status_kendaraan='tersedia' ORDER BY nama_kendaraan ASC");
                            if(mysqli_num_rows($q_k) > 0){
                                while($k = mysqli_fetch_array($q_k)){
                                    echo "<option value='{$k['id']}' data-harga='{$k['harga_sewa']}'>{$k['nama_kendaraan']} - {$k['no_polisi']} (Rp ".number_format($k['harga_sewa'])."/hari)</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Semua unit sedang disewa</option>";
                            }
                            ?>
                        </select>
                        <p class="text-xs text-red-500 mt-1">* Hanya unit 'Tersedia' yang muncul.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Lama Sewa (Hari)</label>
                        <input type="number" name="lama" id="lama" min="1" value="1" oninput="updateHarga()" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                </div>

                <div class="bg-<?= $conf['color'] ?>-50 p-4 rounded-xl border border-<?= $conf['color'] ?>-200 mb-6 flex justify-between items-center">
                    <span class="text-<?= $conf['color'] ?>-800 font-bold">Total Estimasi Biaya:</span>
                    <span class="text-2xl font-extrabold text-<?= $conf['color'] ?>-600" id="total_display">Rp 0</span>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <a href="data_sewa_aktif.php" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-bold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" name="simpan_sewa" class="bg-<?= $conf['color'] ?>-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg">
                        <i class="fa-solid fa-check-circle mr-2"></i> Proses Sewa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateHarga() {
            const select = document.getElementById('kendaraan');
            const harga = select.options[select.selectedIndex].getAttribute('data-harga') || 0;
            const lama = document.getElementById('lama').value || 0;
            const total = harga * lama;
            
            // Format Rupiah
            document.getElementById('total_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }
    </script>
</body>
</html>