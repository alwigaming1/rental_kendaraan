<?php 
include 'config.php'; 
cek_login(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Riwayat - <?= $conf['app_name'] ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>Laporan Riwayat Transaksi Rental</h1>
        <p><?= $conf['app_name'] ?> | Dicetak pada: <?= date('d-m-Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode TRX</th>
                <th>Kendaraan</th>
                <th>Pelanggan</th>
                <th class="text-center">Tgl Sewa</th>
                <th class="text-center">Tgl Kembali</th>
                <th class="text-right">Total Bayar</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = mysqli_query($koneksi, "
                SELECT t.*, k.nama_kendaraan, k.no_polisi, p.nama_pelanggan 
                FROM transaksi t
                JOIN kendaraan k ON t.kendaraan_id = k.id
                JOIN pelanggan p ON t.pelanggan_id = p.id
                WHERE t.status_transaksi = 'selesai'
                ORDER BY t.tgl_kembali_real DESC
            ");
            $no = 1;
            $total_pendapatan = 0;
            while($row = mysqli_fetch_array($sql)){
                $total_pendapatan += ($row['total_bayar'] + $row['denda']);
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row['kode_trx'] ?></td>
                <td><?= $row['nama_kendaraan'] ?> (<?= $row['no_polisi'] ?>)</td>
                <td><?= $row['nama_pelanggan'] ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($row['tgl_kembali_real'])) ?></td>
                <td class="text-right">Rp <?= number_format($row['total_bayar']) ?></td>
                <td class="text-right"><?= ($row['denda'] > 0) ? number_format($row['denda']) : "-" ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="6" class="text-right"><strong>GRAND TOTAL</strong></td>
                <td colspan="2" class="text-right"><strong>Rp <?= number_format($total_pendapatan) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; float: right; text-align: center; width: 200px;">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>___________________</p>
        <p>Manager Operasional</p>
    </div>

</body>
</html>