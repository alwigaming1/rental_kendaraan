<?php 
include 'config.php'; cek_login(); 

$id=""; $nik=""; $nama=""; $hp=""; $alamat=""; $tombol="Simpan";

if(isset($_GET['id'])){
    $id = anti_injection($koneksi, $_GET['id']);
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id='$id'"));
    if($data){
        $nik=$data['nik']; $nama=$data['nama_pelanggan']; $hp=$data['no_hp']; $alamat=$data['alamat'];
        $tombol="Update";
    }
}

if(isset($_POST['simpan'])){
    $nik = anti_injection($koneksi, $_POST['nik']);
    $nm = anti_injection($koneksi, $_POST['nama']);
    $hp = anti_injection($koneksi, $_POST['no_hp']);
    $al = anti_injection($koneksi, $_POST['alamat']);
    
    if($_POST['id'] != ""){
        $q = "UPDATE pelanggan SET nik='$nik', nama_pelanggan='$nm', no_hp='$hp', alamat='$al' WHERE id='$_POST[id]'";
    } else {
        $q = "INSERT INTO pelanggan (nik, nama_pelanggan, no_hp, alamat) VALUES ('$nik', '$nm', '$hp', '$al')";
    }

    if(mysqli_query($koneksi, $q)){
        echo "<script>alert('Data pelanggan berhasil disimpan!'); window.location='data_pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($koneksi)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $tombol ?> Pelanggan - <?= $conf['app_name'] ?></title>
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
        <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-3"><?= $tombol ?> Data Pelanggan</h1>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 max-w-2xl">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">NIK (KTP)</label>
                        <input type="text" name="nik" value="<?= $nik ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $nama ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">No. Handphone</label>
                    <input type="text" name="no_hp" value="<?= $hp ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-<?= $conf['color'] ?>-500"><?= $alamat ?></textarea>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <a href="data_pelanggan.php" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-bold hover:bg-gray-300">Batal</a>
                    <button type="submit" name="simpan" class="bg-<?= $conf['color'] ?>-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-<?= $conf['color'] ?>-700">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>