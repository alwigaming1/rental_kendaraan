<?php 
include 'config.php'; 
cek_login(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>LAPORAN DATA UTAMA</h1>
        <p><?= $conf['app_name'] ?></p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kolom 1</th>
                <th>Kolom 2</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Data Contoh A</td>
                <td>Keterangan A</td>
                <td>Aktif</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Data Contoh B</td>
                <td>Keterangan B</td>
                <td>Non-Aktif</td>
            </tr>
        </tbody>
    </table>

    <div style="float: right; margin-top: 30px; text-align: center;">
        <p>Kota, <?= date('d-m-Y') ?></p>
        <br><br><br>
        <p>_______________________</p>
        <p>Administrator</p>
    </div>

</body>
</html>