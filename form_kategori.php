<?php 
include 'config.php'; cek_login(); 

$id=""; $nama=""; $tombol="Simpan";

// LOGIC EDIT
if(isset($_GET['id'])){
    $id = anti_injection($koneksi, $_GET['id']);
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kategori WHERE id='$id'"));
    if($data){
        $nama=$data['nama_kategori'];
        $tombol="Update";
    }
}

// LOGIC SIMPAN
if(isset($_POST['simpan'])){
    $nm = anti_injection($koneksi, $_POST['nama']);
    
    if($_POST['id'] != ""){
        $q = "UPDATE kategori SET nama_kategori='$nm' WHERE id='$_POST[id]'";
    } else {
        $q = "INSERT INTO kategori (nama_kategori) VALUES ('$nm')";
    }

    if(mysqli_query($koneksi, $q)){
        echo "<script>alert('Data kategori berhasil disimpan!'); window.location='data_kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($koneksi)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $tombol ?> Kategori - <?= $conf['app_name'] ?></title>
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
        <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-3"><?= $tombol ?> Kategori</h1>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 max-w-lg">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2 text-slate-700">Nama Kategori</label>
                    <input type="text" name="nama" value="<?= $nama ?>" class="w-full border p-3 rounded-xl focus:ring-2 focus:ring-<?= $conf['color'] ?>-500 outline-none transition" placeholder="Contoh: MPV, City Car, Motor Matic" required autofocus>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="data_kategori.php" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" name="simpan" class="bg-<?= $conf['color'] ?>-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-<?= $conf['color'] ?>-700 transition shadow-lg">
                        <i class="fa-solid fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>