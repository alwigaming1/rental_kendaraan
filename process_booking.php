<?php
include 'config.php';

if(isset($_POST['booking'])){
    $kendaraan_id = anti_injection($koneksi, $_POST['kendaraan_id']);
    $nik = anti_injection($koneksi, $_POST['nik']);
    $nama = anti_injection($koneksi, $_POST['nama']);
    $hp = anti_injection($koneksi, $_POST['hp']);
    $alamat = anti_injection($koneksi, $_POST['alamat']);
    $tgl_sewa = anti_injection($koneksi, $_POST['tgl_sewa']);
    $lama = anti_injection($koneksi, $_POST['lama']);
    $harga_per_hari = $_POST['harga_per_hari'];

    // 1. CEK / INPUT PELANGGAN
    $q_pelanggan = mysqli_query($koneksi, "SELECT id FROM pelanggan WHERE nik='$nik'");
    if(mysqli_num_rows($q_pelanggan) > 0){
        $pelanggan = mysqli_fetch_assoc($q_pelanggan);
        $pelanggan_id = $pelanggan['id'];
        // Opsional: Update data pelanggan jika ada perubahan
    } else {
        $q_insert_p = "INSERT INTO pelanggan (nik, nama_pelanggan, no_hp, alamat) VALUES ('$nik', '$nama', '$hp', '$alamat')";
        mysqli_query($koneksi, $q_insert_p);
        $pelanggan_id = mysqli_insert_id($koneksi);
    }

    // 2. INPUT TRANSAKSI (STATUS: BOOKING)
    $kode_trx = "TRX-" . date("ymdHis");
    $total_bayar = $harga_per_hari * $lama;
    $tgl_kembali_rencana = date('Y-m-d', strtotime($tgl_sewa . ' + ' . $lama . ' days'));
    // User ID 1 (Admin) default karena ini booking publik
    $user_id = 1; 

    $q_trx = "INSERT INTO transaksi (kode_trx, pelanggan_id, kendaraan_id, tgl_sewa, tgl_kembali_rencana, lama_sewa_hari, total_bayar, status_transaksi, user_id) 
              VALUES ('$kode_trx', '$pelanggan_id', '$kendaraan_id', '$tgl_sewa', '$tgl_kembali_rencana', '$lama', '$total_bayar', 'booking', '$user_id')";

    if(mysqli_query($koneksi, $q_trx)){
        echo "<script>alert('Booking Berhasil! Silakan datang ke outlet untuk pengambilan unit dengan menunjukkan KODE TRANSAKSI: $kode_trx'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal Booking: ".mysqli_error($koneksi)."'); window.location='index.php';</script>";
    }
}
?>