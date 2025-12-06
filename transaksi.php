<?php 
include 'config.php'; cek_login(); 
if(!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

// --- 1. LOGIC TAMBAH ITEM ---
if(isset($_GET['add'])){
    $id = $_GET['add'];
    $found = false;
    foreach($_SESSION['cart'] as $key => $item){
        if($item['id'] == $id){ $_SESSION['cart'][$key]['qty'] += 1; $found = true; }
    }
    if(!$found){
        $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'"));
        $_SESSION['cart'][] = ['id' => $p['id'], 'nama' => $p['nama_produk'], 'harga' => $p['harga'], 'qty' => 1, 'img' => $p['gambar']];
    }
    header("Location: transaksi.php"); exit;
}

// --- 2. LOGIC RESET ---
if(isset($_GET['reset'])){ $_SESSION['cart'] = []; header("Location: transaksi.php"); exit; }

// --- 3. LOGIC BAYAR (DENGAN DISKON) ---
if(isset($_POST['bayar'])){
    $subtotal = $_POST['total_kotor'];
    $diskon_persen = $_POST['diskon'];
    $uang_bayar = $_POST['uang']; 
    
    $potongan = ($subtotal * $diskon_persen) / 100;
    $total_bersih = $subtotal - $potongan;
    
    if($uang_bayar < $total_bersih){ 
        echo "<script>alert('Uang Kurang!');</script>"; 
    } else {
        $kembali = $uang_bayar - $total_bersih;
        $struk = "INV-".date("ymdHis"); $tgl = date("Y-m-d H:i:s");
        
        mysqli_query($koneksi, "INSERT INTO transaksi VALUES (NULL, '$struk', '$tgl', '$total_bersih', '$potongan', '$uang_bayar', '$kembali', '$_SESSION[user_id]')");
        $id_trx = mysqli_insert_id($koneksi);
        
        foreach($_SESSION['cart'] as $c){
            $sub = $c['harga']*$c['qty'];
            mysqli_query($koneksi, "INSERT INTO transaksi_detail VALUES (NULL, '$id_trx', '$c[id]', '$c[harga]', '$c[qty]', '$sub')");
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $c[qty] WHERE id='$c[id]'");
        }
        $_SESSION['cart'] = []; header("Location: struk.php?id=$id_trx");
    }
}

// Hitung Data Awal untuk JS
$total_php = 0; $qty_php = 0;
foreach($_SESSION['cart'] as $c){ $total_php += ($c['harga']*$c['qty']); $qty_php += $c['qty']; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kasir - <?= $conf['app_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 h-screen w-full overflow-hidden flex flex-col lg:flex-row" 
      x-data="{ 
          mobileCartOpen: false, 
          subtotal: <?= $total_php ?>, 
          diskon: 0,
          bayar: '',
          get potongan() { return Math.round(this.subtotal * (this.diskon / 100)); },
          get total() { return this.subtotal - this.potongan; },
          get kembali() { return (this.bayar - this.total) > 0 ? (this.bayar - this.total) : 0; }
      }">

    <div class="flex-1 flex flex-col h-full relative z-0">
        
        <header class="h-16 bg-white border-b border-gray-200 px-4 flex justify-between items-center shadow-sm shrink-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-orange-600 rounded-lg flex items-center justify-center text-white text-lg shadow-md shadow-orange-200">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h1 class="font-bold text-base text-slate-800 leading-none">CafePOS</h1>
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Shift: <?= $_SESSION['nama'] ?></p>
                </div>
            </div>

            <div class="hidden sm:flex relative w-64 lg:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                <input type="text" id="cariMenu" onkeyup="filterMenu()" placeholder="Cari menu..." class="w-full bg-gray-100 border-none rounded-full py-2 pl-9 pr-4 text-sm focus:ring-2 focus:ring-orange-500 transition">
            </div>

            <div class="flex gap-2">
                <?php if($_SESSION['role']=='admin'): ?>
                <a href="dashboard.php" class="w-9 h-9 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                </a>
                <?php endif; ?>
                <a href="logout.php" onclick="return confirm('Keluar?')" class="w-9 h-9 bg-red-50 text-red-500 rounded-full flex items-center justify-center hover:bg-red-100 transition">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </a>
            </div>
        </header>

        <div class="px-4 py-3 bg-white shrink-0 shadow-sm z-10">
            <div class="flex gap-2 overflow-x-auto no-scrollbar">
                <button onclick="filterKategori('all')" class="cat-btn px-4 py-1.5 bg-orange-600 text-white rounded-full text-xs font-bold shadow-md shadow-orange-200 whitespace-nowrap transition">Semua</button>
                <button onclick="filterKategori('Makanan')" class="cat-btn px-4 py-1.5 bg-white text-gray-500 border border-gray-200 rounded-full text-xs font-bold hover:border-orange-500 hover:text-orange-600 whitespace-nowrap transition">Makanan</button>
                <button onclick="filterKategori('Minuman')" class="cat-btn px-4 py-1.5 bg-white text-gray-500 border border-gray-200 rounded-full text-xs font-bold hover:border-orange-500 hover:text-orange-600 whitespace-nowrap transition">Minuman</button>
                <button onclick="filterKategori('Snack')" class="cat-btn px-4 py-1.5 bg-white text-gray-500 border border-gray-200 rounded-full text-xs font-bold hover:border-orange-500 hover:text-orange-600 whitespace-nowrap transition">Snack</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 pb-24 lg:pb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                <?php 
                $menu = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0");
                while($m = mysqli_fetch_array($menu)){
                    $img = !empty($m['gambar']) ? "assets/".$m['gambar'] : "https://placehold.co/300x300/f8fafc/cbd5e1?text=".urlencode($m['nama_produk']);
                ?>
                <a href="transaksi.php?add=<?= $m['id'] ?>" class="item-card group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-orange-300 transition active:scale-95 flex flex-col relative" data-name="<?= strtolower($m['nama_produk']) ?>" data-cat="<?= $m['kategori'] ?>">
                    <div class="relative h-28 sm:h-36 overflow-hidden bg-gray-100">
                        <img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-0.5 rounded text-[10px] font-bold text-slate-600 shadow-sm">
                            Stok: <?= $m['stok'] ?>
                        </div>
                    </div>
                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-tight mb-1 line-clamp-2"><?= $m['nama_produk'] ?></h4>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-orange-600 font-extrabold text-xs sm:text-sm">Rp <?= number_format($m['harga']/1000, 0) ?>K</span>
                            <div class="w-6 h-6 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="lg:hidden fixed bottom-6 left-6 right-6 z-40">
        <button @click="mobileCartOpen = true" class="w-full bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex justify-between items-center animate-bounce-slow">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs">
                    <?= $qty_php ?>
                </div>
                <span class="font-bold text-sm">Lihat Pesanan</span>
            </div>
            <span class="font-extrabold text-lg">Rp <?= number_format($total_php/1000, 0) ?>K</span>
        </button>
    </div>

    <div class="fixed inset-0 z-50 lg:static lg:inset-auto lg:z-auto w-full lg:w-96 flex flex-col" 
         :class="mobileCartOpen ? 'flex' : 'hidden lg:flex'">
        
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm lg:hidden" @click="mobileCartOpen = false"></div>

        <div class="relative w-full h-[85vh] mt-auto lg:h-full lg:mt-0 bg-white shadow-2xl lg:shadow-none lg:border-l border-gray-200 rounded-t-3xl lg:rounded-none flex flex-col transition-transform duration-300">
            
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mt-3 mb-1 lg:hidden"></div>

            <div class="h-16 px-6 flex items-center justify-between border-b border-gray-100 shrink-0">
                <span class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    Pesanan <span class="bg-orange-100 text-orange-600 text-xs px-2 py-0.5 rounded-full"><?= count($_SESSION['cart']) ?></span>
                </span>
                <a href="transaksi.php?reset=true" class="text-xs font-bold text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-trash-can mr-1"></i> Reset
                </a>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50">
                <?php if(empty($_SESSION['cart'])): ?>
                    <div class="flex flex-col items-center justify-center h-64 text-gray-300">
                        <i class="fa-solid fa-basket-shopping text-6xl mb-4 text-gray-200"></i>
                        <p class="text-sm font-medium">Keranjang kosong</p>
                    </div>
                <?php endif; ?>

                <?php foreach($_SESSION['cart'] as $c): ?>
                <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center group">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <?= $c['qty'] ?>x
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-sm text-gray-800 truncate"><?= $c['nama'] ?></div>
                            <div class="text-[10px] text-gray-400">@ <?= number_format($c['harga']) ?></div>
                        </div>
                    </div>
                    <div class="font-bold text-sm text-slate-700 shrink-0">
                        <?= number_format(($c['harga']*$c['qty'])/1000, 0) ?>K
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="p-6 bg-white border-t border-gray-100 shadow-[0_-5px_20px_rgba(0,0,0,0.03)] shrink-0">
                <form method="POST">
                    <input type="hidden" name="total_kotor" :value="subtotal">
                    
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center text-orange-600">
                            <span class="text-xs font-bold">DISKON (%)</span>
                            <input type="number" name="diskon" x-model="diskon" class="w-12 text-right border-b border-orange-200 focus:border-orange-500 outline-none text-sm font-bold bg-transparent" placeholder="0">
                        </div>
                        <div class="flex justify-between text-gray-500 text-xs" x-show="diskon > 0">
                            <span>Potongan</span>
                            <span x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(potongan)"></span>
                        </div>
                        <div class="flex justify-between text-lg font-extrabold text-slate-800 pt-2 border-t border-dashed">
                            <span>Total</span>
                            <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                        </div>
                    </div>

                    <div class="relative mb-3">
                        <span class="absolute left-4 top-3.5 text-gray-400 text-sm font-bold">Rp</span>
                        <input type="number" name="uang" x-model="bayar" class="w-full pl-12 pr-4 py-3.5 bg-gray-100 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-orange-500 transition outline-none text-lg" placeholder="Uang Tunai" required>
                    </div>

                    <div class="flex justify-between text-xs text-gray-500 mb-4 px-1" x-show="bayar > 0">
                        <span>Kembali:</span>
                        <span class="font-bold text-green-600 text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(kembali)"></span>
                    </div>

                    <button type="submit" name="bayar" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-slate-800 transition shadow-lg flex justify-center items-center gap-2 group active:scale-95 transform duration-100">
                        <span>Bayar Sekarang</span> 
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterKategori(cat) {
            const cards = document.querySelectorAll('.item-card');
            const btns = document.querySelectorAll('.cat-btn');
            
            // Reset style tombol
            btns.forEach(btn => {
                if(btn.innerText === cat || (cat === 'all' && btn.innerText === 'Semua')){
                    btn.className = "cat-btn px-4 py-1.5 bg-orange-600 text-white rounded-full text-xs font-bold shadow-md shadow-orange-200 whitespace-nowrap transition";
                } else {
                    btn.className = "cat-btn px-4 py-1.5 bg-white text-gray-500 border border-gray-200 rounded-full text-xs font-bold hover:border-orange-500 hover:text-orange-600 whitespace-nowrap transition";
                }
            });

            cards.forEach(card => {
                if (cat === 'all' || card.getAttribute('data-cat') === cat) card.style.display = 'flex';
                else card.style.display = 'none';
            });
        }

        function filterMenu() {
            const input = document.getElementById('cariMenu').value.toLowerCase();
            const cards = document.querySelectorAll('.item-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(input)) card.style.display = 'flex';
                else card.style.display = 'none';
            });
        }
    </script>

</body>
</html>