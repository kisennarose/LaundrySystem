<?php
require 'cek_login.php';
require "config.php";

$layanan = [
    [
        "nama" => "Cuci Reguler",
        "harga" => 7000,
        "ikon" => "🧺",
        "deskripsi" => "Solusi praktis untuk cucian sehari-hari.",
        "durasi" => "Layanan reguler",
        "warna" => "reguler"
    ],
    [
        "nama" => "Cuci Express",
        "harga" => 12000,
        "ikon" => "⚡",
        "deskripsi" => "Pilihan untuk kamu yang membutuhkan layanan lebih cepat.",
        "durasi" => "Layanan express",
        "warna" => "express"
    ],
    [
        "nama" => "Cuci Setrika",
        "harga" => 10000,
        "ikon" => "👕",
        "deskripsi" => "Pakaian dicuci dan disetrika agar siap digunakan.",
        "durasi" => "Cuci + setrika",
        "warna" => "setrika"
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Layanan Laundry | FRESHLAUNDRY</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #F7F5EE;
            color: #303B31;
            font-family: 'DM Sans', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 245px;
            min-height: 100vh;
            padding: 30px 20px;
            background: #405B46;
            color: white;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            font-weight: 700;
        }

        .brand-sub {
            color: #D1DDCE;
            font-size: 12px;
            margin-bottom: 45px;
        }

        .menu-label {
            color: #BDCCBB;
            font-size: 10px;
            letter-spacing: 2px;
            margin: 25px 12px 12px;
        }

        .menu-link {
            display: block;
            padding: 13px 14px;
            margin-bottom: 8px;
            color: #E2E9DF;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
        }

        .menu-link:hover,
        .menu-link.active {
            background: #718D75;
            color: white;
        }

        .main {
            margin-left: 245px;
            padding: 38px;
        }

        .eyebrow {
            color: #66836B;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        h1 {
            margin: 7px 0;
            font-family: 'Playfair Display', serif;
            font-size: 32px;
        }

        .subtitle {
            color: #899087;
            font-size: 13px;
        }

        .intro {
            margin-top: 25px;
            padding: 28px;
            border-radius: 18px;
            background: #66836B;
            color: white;
        }

        .intro h2 {
            font-family: 'Playfair Display', serif;
            font-size: 25px;
        }

        .intro p {
            margin: 8px 0 0;
            color: #F0F4ED;
            font-size: 13px;
            line-height: 1.8;
        }

        .layanan-card {
            height: 100%;
            padding: 25px;
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 17px;
            transition: transform .2s, box-shadow .2s;
        }

        .layanan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(48, 59, 49, .08);
        }

        .ikon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            margin-bottom: 20px;
            border-radius: 15px;
            font-size: 27px;
        }

        .reguler .ikon {
            background: #E6EEE4;
        }

        .express .ikon {
            background: #F8EDCF;
        }

        .setrika .ikon {
            background: #E2EAF6;
        }

        .nama-layanan {
            font-family: 'Playfair Display', serif;
            font-size: 21px;
            font-weight: 700;
        }

        .deskripsi {
            min-height: 45px;
            margin-top: 10px;
            color: #899087;
            font-size: 13px;
            line-height: 1.7;
        }

        .harga {
            margin-top: 22px;
            color: #405B46;
            font-size: 25px;
            font-weight: 700;
        }

        .per-kg {
            color: #899087;
            font-size: 12px;
            font-weight: 400;
        }

        .durasi {
            display: inline-block;
            margin-top: 12px;
            padding: 7px 10px;
            background: #F3F4EE;
            color: #66836B;
            border-radius: 20px;
            font-size: 11px;
        }

        .btn-pesan {
            display: block;
            margin-top: 22px;
            padding: 12px;
            border-radius: 9px;
            background: #405B46;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-pesan:hover {
            background: #66836B;
            color: white;
        }

        .catatan {
            margin-top: 25px;
            padding: 18px 20px;
            border: 1px solid #EAE9E0;
            border-radius: 13px;
            background: #FFFFFF;
            color: #899087;
            font-size: 12px;
            line-height: 1.8;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #899087;
            font-size: 12px;
        }

        @media (max-width: 700px) {
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
                padding: 20px;
            }

            .brand-sub {
                margin-bottom: 15px;
            }

            .main {
                margin: 0;
                padding: 22px 14px;
            }

            h1 {
                font-size: 27px;
            }

            .intro {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">✳ FRESHLAUNDRY</div>
    <div class="brand-sub">Laundry Management System</div>

    <div class="menu-label">MENU UTAMA</div>

    <a href="index.php" class="menu-link">
        ▦ &nbsp; Dashboard
    </a>

    <a href="pesanan.php" class="menu-link">
        ▤ &nbsp; Data Pesanan
    </a>

    <div class="menu-label">INFORMASI</div>

    <a href="layanan.php" class="menu-link active">
        ♧ &nbsp; Layanan Laundry
    </a>
</aside>

<main class="main">

    <div class="eyebrow">INFORMASI LAYANAN</div>
    <h1>Layanan Laundry</h1>

    <p class="subtitle">
        Kenali pilihan layanan dan harga yang tersedia di FRESHLAUNDRY.
    </p>

    <section class="intro">
        <h2>Cucian bersih, hati senang.</h2>
        <p>
            Pilih layanan yang sesuai dengan kebutuhanmu.
            Kami menyediakan pilihan layanan laundry untuk membantu
            perawatan pakaian sehari-hari.
        </p>
    </section>

    <div class="row g-4 mt-2">

        <?php foreach ($layanan as $item): ?>

        <div class="col-12 col-md-6 col-xl-4">

            <div class="layanan-card <?= $item['warna'] ?>">

                <div class="ikon">
                    <?= $item['ikon'] ?>
                </div>

                <div class="nama-layanan">
                    <?= htmlspecialchars($item['nama']) ?>
                </div>

                <p class="deskripsi">
                    <?= htmlspecialchars($item['deskripsi']) ?>
                </p>

                <div class="harga">
                    Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                    <span class="per-kg">/ kg</span>
                </div>

                <span class="durasi">
                    <?= htmlspecialchars($item['durasi']) ?>
                </span>

                <a href="tambah.php" class="btn-pesan">
                    + Buat Pesanan
                </a>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <div class="catatan">
        <strong>Informasi:</strong><br>
        Harga dihitung berdasarkan berat cucian. Total biaya akhir
        mengikuti berat dan layanan yang dipilih saat membuat pesanan.
    </div>

    <div class="footer">
        © <?= date('Y') ?> FRESHLAUNDRY · Clean clothes, happy days.
    </div>

</main>

</body>
</html>
