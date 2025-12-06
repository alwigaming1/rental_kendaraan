<?php
session_start();

// --- KONFIGURASI UTAMA ---
$conf = [
    "app_name"  => "Rental Kendaraan", 
    "app_brand" => "DIGI", // Bagian depan logo
    "app_brand2"=> "RENT", // Bagian belakang logo (warna)
    "app_ver"   => "V.1.0",
    "author"    => "SkripsiCode",
    
    // TEMA WARNA: teal (biru kehijauan - cocok untuk travel/rental)
    "color"     => "teal", 
    
    // Database
    "db_host"   => "localhost",
    "db_user"   => "root",
    "db_pass"   => "",
    "db_name"   => "db_rental" 
];

$koneksi = mysqli_connect($conf['db_host'], $conf['db_user'], $conf['db_pass'], $conf['db_name']);

if (!$koneksi) { 
    die("<h3>🚨 Gagal Koneksi Database!</h3><p>Pastikan database <b>db_rental</b> sudah dibuat.</p>"); 
}

// Fungsi Helper
function cek_login(){
    if(empty($_SESSION['status']) || $_SESSION['status'] != 'login'){
        header("location:login.php");
        exit;
    }
}

function anti_injection($koneksi, $input) {
    return mysqli_real_escape_string($koneksi, trim(htmlspecialchars($input)));
}
?>