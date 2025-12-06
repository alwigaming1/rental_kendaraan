<?php 
include 'config.php'; cek_login(); 

$id=""; $no_polisi=""; $nama=""; $merk=""; $warna=""; $tahun=""; $harga=""; $kategori_id=""; $status="tersedia";
$tombol="Simpan";

if(isset($_GET['id'])){
    $id = anti_injection($koneksi, $_GET['id']);
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id='$id'"));
    if($data){
        $no_polisi=$data['no_polisi']; $nama=$data['nama_kendaraan']; $merk=$data['merk'];
        $warna=$data['warna']; $tahun=$data['tahun']; $harga=$data['harga_sewa'];
        $kategori_id=$data['kategori_id']; $status=$data['status_kendaraan'];
        $tombol="Update";
    }
}

if(isset($_POST['simpan'])){
    $nopol = anti_injection($koneksi, $_POST['no_polisi']);
    $nm = anti_injection($koneksi, $_POST['nama']);
    $mr = anti_injection($koneksi, $_POST['merk']);
    $wr = anti_injection($koneksi, $_POST['warna']);
    $th = anti_injection($koneksi, $_POST['tahun']);
    $hr = anti_injection($koneksi, $_POST['harga']);
    $kt = anti_injection($koneksi, $_POST['kategori_id']);
    $st = anti_injection($koneksi, $_POST['status']);
    
    if($_POST['id'] != ""){
        $q = "UPDATE kendaraan SET no_polisi='$nopol', nama_kendaraan='$nm', merk='$mr', warna='$wr', tahun='$th', harga_sewa='$hr', kategori_id='$kt', status_kendaraan='$st' WHERE id='$_POST[id]'";
    } else {
        $q = "INSERT INTO kendaraan (no_polisi, nama_kendaraan, merk, warna, tahun, harga_sewa, kategori_id, status_kendaraan) VALUES ('$nopol', '$nm', '$mr', '$wr', '$th', '$hr', '$kt', '$st')";
    }

    if(mysqli_query($koneksi, $q)){
        echo "<script>alert('Data berhasil disimpan!'); window.location='data_kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($koneksi)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $tombol ?> Kendaraan - <?= $conf['app_name'] ?></title>
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
        <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-3"><?= $tombol ?> Unit Kendaraan</h1>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 max-w-4xl">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">No. Polisi</label>
                        <input type="text" name="no_polisi" value="<?= $no_polisi ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 bg-gray-50" placeholder="B 1234 XX" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold mb-1">Nama Kendaraan</label>
                        <input type="text" name="nama" value="<?= $nama ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" placeholder="Contoh: Avanza Veloz" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Merk</label>
                        <input type="text" name="merk" value="<?= $merk ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" placeholder="Honda">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Tahun</label>
                        <input type="number" name="tahun" value="<?= $tahun ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" placeholder="2022">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Warna</label>
                        <input type="text" name="warna" value="<?= $warna ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" placeholder="Putih">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Kategori</label>
                        <select name="kategori_id" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500">
                            <?php 
                            $q_kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                            while($k = mysqli_fetch_array($q_kat)){
                                $sel = ($k['id'] == $kategori_id) ? 'selected' : '';
                                echo "<option value='{$k['id']}' $sel>{$k['nama_kategori']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold mb-1">Harga Sewa (Per Hari)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="harga" value="<?= $harga ?>" class="w-full border pl-10 p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Status Unit</label>
                        <select name="status" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500">
                            <option value="tersedia" <?= $status=='tersedia'?'selected':'' ?>>Tersedia (Ready)</option>
                            <option value="disewa" <?= $status=='disewa'?'selected':'' ?>>Sedang Disewa</option>
                            <option value="service" <?= $status=='service'?'selected':'' ?>>Sedang Service</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <a href="data_kendaraan.php" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-bold hover:bg-gray-300">Batal</a>
                    <button type="submit" name="simpan" class="bg-<?= $conf['color'] ?>-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>