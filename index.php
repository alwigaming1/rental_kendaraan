<?php 
include 'config.php'; 

// --- LOGIC FILTER & PENCARIAN ---
$where = "status_kendaraan = 'tersedia'";
$search_value = "";
$kategori_value = "";

// Cek apakah ada pencarian
if(isset($_GET['cari']) || isset($_GET['kategori'])){
    // Filter Keyword
    if(!empty($_GET['cari'])){
        $keyword = anti_injection($koneksi, $_GET['cari']);
        $where .= " AND (nama_kendaraan LIKE '%$keyword%' OR merk LIKE '%$keyword%')";
        $search_value = $keyword;
    }
    // Filter Kategori
    if(!empty($_GET['kategori'])){
        $kat_id = anti_injection($koneksi, $_GET['kategori']);
        $where .= " AND kategori_id = '$kat_id'";
        $kategori_value = $kat_id;
    }
}

// Query Mobil (Limit 9 agar rapi di grid 3x3)
$q_mobil = mysqli_query($koneksi, "
    SELECT k.*, c.nama_kategori 
    FROM kendaraan k
    LEFT JOIN kategori c ON k.kategori_id = c.id
    WHERE $where
    ORDER BY k.id DESC LIMIT 9
");

// Query Kategori untuk Dropdown
$q_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <title>Rental Mobil - <?= $conf['app_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Background Hero Premium */
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Efek Parallax */
        }
        .text-shadow { text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 antialiased" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <nav :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-md py-3' : 'bg-transparent py-5'" 
         class="fixed w-full z-50 transition-all duration-300 top-0 left-0 border-b border-white/10">
        <div class="container mx-auto px-6 flex justify-between items-center">
            
            <a href="index.php" class="flex items-center gap-2 group">
                <div :class="scrolled ? 'bg-teal-600 text-white' : 'bg-white text-teal-600'" 
                     class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shadow-lg transition-colors">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <span :class="scrolled ? 'text-slate-800' : 'text-white'" 
                      class="font-extrabold text-xl tracking-tight">
                    DIGI<span :class="scrolled ? 'text-teal-600' : 'text-teal-300'">RENT</span>
                </span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="index.php" :class="scrolled ? 'text-slate-600 hover:text-teal-600' : 'text-slate-200 hover:text-white'" class="transition">Beranda</a>
                <a href="#armada" :class="scrolled ? 'text-slate-600 hover:text-teal-600' : 'text-slate-200 hover:text-white'" class="transition">Armada</a>
                <a href="#layanan" :class="scrolled ? 'text-slate-600 hover:text-teal-600' : 'text-slate-200 hover:text-white'" class="transition">Layanan</a>
            </div>

            <?php if(isset($_SESSION['status'])): ?>
                <a href="dashboard.php" class="hidden md:inline-block bg-teal-600 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/30">
                    Dashboard
                </a>
            <?php else: ?>
                <a href="login.php" :class="scrolled ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-white text-teal-900 hover:bg-slate-100'" 
                   class="hidden md:inline-block px-6 py-2.5 rounded-full text-sm font-bold transition shadow-lg">
                    Login Staff
                </a>
            <?php endif; ?>

            <button class="md:hidden text-2xl" :class="scrolled ? 'text-slate-800' : 'text-white'">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <section id="beranda" class="hero-bg relative h-[600px] flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>
        
        <div class="container mx-auto px-6 relative z-10 text-center pt-20">
            <span class="inline-block py-1 px-3 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold uppercase tracking-wider mb-6 backdrop-blur-sm animate-bounce">
                🚀 Solusi Perjalanan Terbaik #1
            </span>
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 leading-tight tracking-tight text-shadow">
                Sewa Mobil Mudah <br> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-emerald-400">Harga Transparan</span>
            </h1>
            
            <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Nikmati perjalanan aman dan nyaman dengan armada terbaru kami. 
                Layanan lepas kunci atau dengan supir profesional siap melayani 24 jam.
            </p>
        </div>
    </section>

    <div class="container mx-auto px-6 relative z-20 -mt-24">
        <div class="bg-white rounded-3xl p-6 shadow-2xl shadow-slate-300/50 border border-slate-100 max-w-4xl mx-auto">
            <form action="index.php" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                
                <div class="md:col-span-5 relative">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Cari Mobil</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400"></i>
                        <input type="text" name="cari" value="<?= $search_value ?>" placeholder="Nama mobil, merk..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-semibold text-slate-700 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Kategori</label>
                    <div class="relative">
                        <i class="fa-solid fa-car absolute left-4 top-3.5 text-slate-400"></i>
                        <select name="kategori" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-8 text-sm font-semibold text-slate-700 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 appearance-none cursor-pointer transition">
                            <option value="">Semua Kategori</option>
                            <?php 
                            // Reset pointer data kategori karena sudah dipakai di atas (jika ada)
                            mysqli_data_seek($q_kategori, 0); 
                            while($k = mysqli_fetch_array($q_kategori)): 
                            ?>
                                <option value="<?= $k['id'] ?>" <?= $k['id'] == $kategori_value ? 'selected' : '' ?>>
                                    <?= $k['nama_kategori'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="md:col-span-3 mt-auto">
                    <label class="block text-xs font-bold text-transparent uppercase mb-1 hidden md:block">Action</label>
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-teal-600/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter"></i> Temukan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <section id="armada" class="py-24 container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Armada Pilihan</h2>
            <p class="text-slate-500 mt-3 text-lg">Unit bersih, terawat, dan siap jalan kemanapun tujuan Anda.</p>
        </div>

        <?php if(mysqli_num_rows($q_mobil) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($row = mysqli_fetch_array($q_mobil)): ?>
            <div class="group bg-white rounded-3xl p-5 border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-teal-100/50 hover:border-teal-100 transition-all duration-300 flex flex-col h-full">
                
                <div class="relative h-56 bg-slate-100 rounded-2xl mb-6 overflow-hidden flex items-center justify-center group-hover:scale-[1.02] transition-transform duration-500">
                    <?php 
                        $img_src = $row['gambar'];
                        // Cek apakah URL atau File Lokal
                        if ($img_src && !filter_var($img_src, FILTER_VALIDATE_URL)) {
                            $img_src = "assets/img/" . $img_src;
                        }
                    ?>
                    <?php if($img_src): ?>
                        <img src="<?= $img_src ?>" class="w-full h-full object-cover" alt="<?= $row['nama_kendaraan'] ?>">
                    <?php else: ?>
                        <i class="fa-solid fa-car text-6xl text-slate-300"></i>
                    <?php endif; ?>
                    
                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-[10px] font-bold text-slate-700 shadow-sm">
                        <?= $row['tahun'] ?>
                    </div>
                    <div class="absolute bottom-3 right-3 bg-teal-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-teal-600/20">
                        Rp <?= number_format($row['harga_sewa']/1000) ?>k / hari
                    </div>
                </div>

                <div class="flex-1">
                    <div class="mb-4">
                        <p class="text-[10px] font-bold text-teal-600 uppercase tracking-wider mb-1"><?= $row['merk'] ?></p>
                        <h3 class="text-xl font-bold text-slate-900 leading-tight"><?= $row['nama_kendaraan'] ?></h3>
                        <p class="text-xs text-slate-400 mt-1"><?= $row['nama_kategori'] ?> • <?= $row['warna'] ?></p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-4 border-t border-b border-slate-100 mb-4 text-center">
                        <div><i class="fa-solid fa-gas-pump text-slate-400 mb-1"></i><p class="text-[10px] font-bold text-slate-600">BBM Irit</p></div>
                        <div class="border-l border-slate-100"><i class="fa-solid fa-gears text-slate-400 mb-1"></i><p class="text-[10px] font-bold text-slate-600">Auto/Man</p></div>
                        <div class="border-l border-slate-100"><i class="fa-solid fa-chair text-slate-400 mb-1"></i><p class="text-[10px] font-bold text-slate-600">Nyaman</p></div>
                    </div>

                    <button onclick="openBooking('<?= $row['id'] ?>', '<?= $row['nama_kendaraan'] ?>', '<?= $row['harga_sewa'] ?>')" 
                        class="w-full bg-slate-900 hover:bg-teal-600 text-white py-3.5 rounded-xl font-bold transition shadow-lg flex justify-between px-6 items-center group-hover:pl-8 group-hover:pr-4">
                        <span>Booking Sekarang</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <i class="fa-regular fa-face-frown-open text-6xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-600">Kendaraan tidak ditemukan</h3>
                <p class="text-slate-400">Silakan cari dengan kata kunci lain.</p>
                <a href="index.php" class="mt-4 inline-block text-teal-600 font-bold hover:underline">Reset Filter</a>
            </div>
        <?php endif; ?>
    </section>

    <footer id="kontak" class="bg-slate-900 text-slate-400 py-16 border-t border-slate-800">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12 text-sm">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-car-side text-3xl text-teal-500"></i>
                    <span class="font-extrabold text-2xl text-white tracking-tight">DIGI<span class="text-teal-500">RENT</span></span>
                </div>
                <p class="mb-6 leading-relaxed max-w-sm">
                    Platform penyewaan kendaraan terpercaya dengan armada terlengkap. 
                    Siap menemani perjalanan bisnis maupun liburan Anda dengan aman.
                </p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6 text-lg">Navigasi</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="hover:text-teal-400 transition">Beranda</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6 text-lg">Kontak</h4>
                <ul class="space-y-3">
                    <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-teal-500"></i> Jl. Raya Merdeka No. 45</li>
                    <li class="flex gap-3"><i class="fa-solid fa-phone mt-1 text-teal-500"></i> +62 812-3456-7890</li>
                </ul>
            </div>
        </div>
        <div class="text-center pt-10 mt-10 border-t border-slate-800 text-xs">
            &copy; <?= date('Y') ?> <?= $conf['app_name'] ?>. All rights reserved.
        </div>
    </footer>

    <div id="bookingModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-0 overflow-hidden relative animate-fade-in-up">
            
            <div class="bg-teal-600 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i class="fa-solid fa-car-side text-9xl transform rotate-12 translate-x-4 -translate-y-4"></i>
                </div>
                <h3 class="text-2xl font-bold relative z-10">Formulir Booking</h3>
                <p class="text-teal-100 text-sm relative z-10">Lengkapi data untuk reservasi unit.</p>
                <button onclick="closeBooking()" class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full w-8 h-8 flex items-center justify-center backdrop-blur-sm transition z-20">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="p-8">
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="kendaraan_id" id="modal_id">
                    <input type="hidden" name="harga_per_hari" id="modal_harga_val">

                    <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl mb-6">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-teal-600 text-xl">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Unit Dipilih</p>
                            <p class="font-bold text-slate-800 text-lg leading-none" id="modal_nama">Mobil</p>
                            <p class="text-xs text-teal-600 font-bold mt-1" id="modal_harga">Rp 0</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="nik" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-sm" placeholder="NIK (KTP)" required>
                            <input type="text" name="hp" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-sm" placeholder="No. WhatsApp" required>
                        </div>
                        <input type="text" name="nama" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-sm" placeholder="Nama Lengkap" required>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 mb-1 block">Tgl Sewa</label>
                                <input type="date" name="tgl_sewa" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 transition text-sm text-slate-600" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 mb-1 block">Lama (Hari)</label>
                                <input type="number" name="lama" min="1" value="1" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 transition text-sm" required>
                            </div>
                        </div>
                        
                        <textarea name="alamat" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-sm" placeholder="Alamat Domisili Lengkap" required></textarea>
                    </div>

                    <button type="submit" name="booking" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold text-sm mt-8 hover:bg-teal-600 transition shadow-xl shadow-slate-900/20 active:scale-95 transform">
                        Konfirmasi Pesanan <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBooking(id, nama, harga) {
            document.getElementById('modal_id').value = id;
            document.getElementById('modal_nama').innerText = nama;
            document.getElementById('modal_harga').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga) + ' / hari';
            document.getElementById('modal_harga_val').value = harga;
            document.getElementById('bookingModal').classList.remove('hidden');
        }
        function closeBooking() {
            document.getElementById('bookingModal').classList.add('hidden');
        }
    </script>
</body>
</html>