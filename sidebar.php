<aside class="w-64 bg-white h-screen fixed left-0 top-0 border-r border-gray-200 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300" 
       x-show="isSidebarOpen" 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       @click.away="isSidebarOpen = false" 
       :class="{'!translate-x-0': isSidebarOpen && window.innerWidth < 1024}"> 
    
    <div class="h-20 flex items-center px-8 border-b border-gray-100">
        <div class="flex items-center gap-3 text-<?= $conf['color'] ?>-600">
            <i class="fa-solid fa-car-side text-3xl"></i>
            <div>
                <h1 class="font-bold text-lg leading-tight text-slate-800"><?= $conf['app_brand'] ?><span class="text-<?= $conf['color'] ?>-600"><?= $conf['app_brand2'] ?></span></h1>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider"><?= $conf['app_ver'] ?></p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-2 px-4">Menu Utama</p>
        
        <a href="dashboard.php" @click="isSidebarOpen = false" class="<?= ($current_page == 'dashboard.php') ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
        </a>

       <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-6 px-4">Armada & Pelanggan</p>
        
        <a href="data_kendaraan.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_kendaraan.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-car w-5"></i> Data Kendaraan
        </a>
        
        <a href="data_kategori.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_kategori.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-tags w-5"></i> Kategori Kendaraan
        </a>
        
        <a href="data_pelanggan.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_pelanggan.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-user-friends w-5"></i> Data Pelanggan
        </a>
        
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-6 px-4">Transaksi Rental</p>

        <a href="data_booking.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_booking.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-bell w-5"></i> Permintaan Booking
        </a>

        <a href="form_sewa.php" @click="isSidebarOpen = false" class="<?= $current_page == 'form_sewa.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-file-circle-plus w-5"></i> Sewa Baru (Manual)
        </a>

        <a href="data_sewa_aktif.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_sewa.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-clock-rotate-left w-5"></i> Sewa Berjalan
        </a>
        
        <a href="data_riwayat.php" @click="isSidebarOpen = false" class="<?= $current_page == 'data_riwayat.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-clipboard-check w-5"></i> Riwayat Transaksi
        </a>
        
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-6 px-4">Pengaturan</p>

        <a href="profil.php" @click="isSidebarOpen = false" class="<?= $current_page == 'profil.php' ? 'bg-'.$conf['color'].'-50 text-'.$conf['color'].'-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm">
            <i class="fa-solid fa-user-gear w-5"></i> Profil & Password
        </a>

    </nav>

    <div class="p-4 border-t border-gray-100">
        <a href="logout.php" onclick="return confirm('Keluar aplikasi?')" class="flex items-center justify-center gap-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white py-3 rounded-xl font-bold transition-all duration-200 text-sm group">
            <i class="fa-solid fa-power-off transition-transform group-hover:scale-110"></i> Logout
        </a>
    </div>
</aside>

<div class="fixed top-0 left-0 right-0 bg-white shadow-md lg:hidden z-40 p-4 flex justify-between items-center">
    <div class="flex items-center gap-2 text-<?= $conf['color'] ?>-600">
        <i class="fa-solid fa-car-side"></i> 
        <h1 class="font-bold text-sm"><?= $conf['app_brand'] ?><span class="text-<?= $conf['color'] ?>-600"><?= $conf['app_brand2'] ?></span></h1>
    </div>
    <button @click="isSidebarOpen = true" class="text-gray-600 text-lg p-2 rounded hover:bg-gray-100">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>