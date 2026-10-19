<?php
require 'cek_login.php';
require "config.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Nomor pesanan tidak valid.");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM pesanan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Pesanan tidak ditemukan.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Laundry | FRESHLAUNDRY</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px 15px;
            background: #F7F5EE;
            color: #303B31;
            font-family: 'DM Sans', sans-serif;
        }

        .nota {
            max-width: 520px;
            margin: auto;
            padding: 35px;
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(48,59,49,.08);
        }

        .header {
            text-align: center;
            padding-bottom: 22px;
            border-bottom: 2px dashed #D9E1D5;
        }

        .brand {
            color: #405B46;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
        }

        .tagline {
            margin-top: 5px;
            color: #899087;
            font-size: 12px;
        }

        .judul {
            margin-top: 25px;
            text-align: center;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .nomor {
            margin-top: 5px;
            text-align: center;
            color: #66836B;
            font-size: 12px;
        }

        .detail {
            margin-top: 25px;
        }

        .baris {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #F0F0E9;
            font-size: 13px;
        }

        .label {
            color: #899087;
        }

        .nilai {
            text-align: right;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
            padding: 18px;
            background: #EDF2E9;
            border-radius: 12px;
        }

        .total-label {
            font-size: 12px;
            font-weight: 700;
        }

        .harga {
            color: #405B46;
            font-size: 20px;
            font-weight: 700;
            text-align: right;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #F8EDCF;
            color: #896D2F;
            font-size: 11px;
        }

        .status.Diproses {
            background: #E2EAF6;
            color: #456895;
        }

        .status.Selesai {
            background: #E2EFE2;
            color: #47714B;
        }

        .footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #D9E1D5;
            text-align: center;
            color: #899087;
            font-size: 12px;
            line-height: 1.8;
        }

        .tombol {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 22px auto;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 9px;
            background: #405B46;
            color: white;
            text-decoration: none;
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
        }

        .btn:hover {
            background: #66836B;
            color: white;
        }

        .btn-kembali {
            background: #E6EEE4;
            color: #405B46;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 10mm;
            }

            body {
                padding: 0;
                background: white;
            }

            .nota {
                max-width: 100%;
                padding: 10px;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }

            .tombol {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="nota">

    <div class="header">
        <div class="brand">✳ FRESHLAUNDRY</div>
        <div class="tagline">Clean clothes, happy days.</div>
    </div>

    <div class="judul">NOTA PEMESANAN</div>
    <div class="nomor">
        Nomor Nota: #<?= (int)$data['id'] ?>
    </div>

    <div class="detail">

        <div class="baris">
            <span class="label">Tanggal Pesanan</span>
            <span class="nilai">
                <?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?>
            </span>
        </div>

        <div class="baris">
            <span class="label">Nama Pelanggan</span>
            <span class="nilai"><?= e($data['nama_pelanggan']) ?></span>
        </div>

        <div class="baris">
            <span class="label">Nomor HP</span>
            <span class="nilai"><?= e($data['no_hp']) ?></span>
        </div>

        <div class="baris">
            <span class="label">Layanan</span>
            <span class="nilai"><?= e($data['layanan']) ?></span>
        </div>

        <div class="baris">
            <span class="label">Berat Cucian</span>
            <span class="nilai">
                <?= number_format((float)$data['berat'], 2, ',', '.') ?> kg
            </span>
        </div>

        <div class="baris">
            <span class="label">Status</span>
            <span class="nilai">
                <span class="status <?= e($data['status']) ?>">
                    <?= e($data['status']) ?>
                </span>
            </span>
        </div>

    </div>

    <div class="total">
        <span class="total-label">TOTAL PEMBAYARAN</span>
        <span class="harga">
            Rp <?= number_format((float)$data['total_harga'], 0, ',', '.') ?>
        </span>
    </div>

    <div class="footer">
        Terima kasih telah menggunakan jasa kami!<br>
        Simpan nota ini sebagai bukti pemesanan.<br>
        <strong>FRESHLAUNDRY</strong>
    </div>

</div>

<div class="tombol">
    <button class="btn" onclick="window.print()">Cetak Nota</button>
    <a href="pesanan.php" class="btn btn-kembali">Kembali</a>
</div>

</body>
</html>