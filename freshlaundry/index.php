<?php
require 'cek_login.php';
require "config.php";

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan");
$totalData = mysqli_fetch_assoc($totalQuery);
$totalPesanan = $totalData['total'];

$prosesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Diproses'");
$prosesData = mysqli_fetch_assoc($prosesQuery);
$pesananProses = $prosesData['total'];

$selesaiQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Selesai'");
$selesaiData = mysqli_fetch_assoc($selesaiQuery);
$pesananSelesai = $selesaiData['total'];

$pendapatanQuery = mysqli_query($conn, "SELECT COALESCE(SUM(total_harga), 0) AS total FROM pesanan WHERE status = 'Selesai'");
$pendapatanData = mysqli_fetch_assoc($pendapatanQuery);
$pendapatan = $pendapatanData['total'];

$terbaru = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRESHLAUNDRY | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sage: #66836B;
            --sage-dark: #405B46;
            --cream: #F7F5EE;
            --white: #FFFFFF;
            --text: #303B31;
            --muted: #899087;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        .sidebar {
            width: 245px;
            min-height: 100vh;
            background: var(--sage-dark);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 30px 20px;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .brand-sub {
            color: #D1DDCE;
            font-size: 12px;
            margin-bottom: 45px;
        }

        .menu-label {
            font-size: 10px;
            letter-spacing: 2px;
            color: #BDCCBB;
            margin: 25px 12px 12px;
        }

        .menu-link {
            display: block;
            color: #E2E9DF;
            text-decoration: none;
            padding: 13px 14px;
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 14px;
            transition: .2s;
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

        .topline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .eyebrow {
            color: var(--sage);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin: 7px 0;
        }

        .subtitle {
            color: var(--muted);
            font-size: 13px;
        }

        .admin {
            background: white;
            border-radius: 30px;
            padding: 10px 16px;
            font-size: 13px;
            white-space: nowrap;
        }

        .welcome {
            background: var(--sage);
            color: white;
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .welcome h2 {
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #E8EEE5;
            font-size: 13px;
            margin-bottom: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 17px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 15px;
            padding: 20px;
            min-width: 0;
        }

        .stat-icon {
            width: 39px;
            height: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #E9EFE6;
            margin-bottom: 17px;
            font-size: 18px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
        }

        .stat-value {
            font-size: 25px;
            font-weight: 700;
            margin-top: 6px;
            overflow-wrap: anywhere;
        }

        .section-card {
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 16px;
            padding: 23px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 21px;
        }

        .btn-sage {
            background: var(--sage-dark);
            color: white;
            border-radius: 9px;
            padding: 10px 15px;
            font-size: 12px;
            text-decoration: none;
            border: none;
            display: inline-block;
        }

        .btn-sage:hover {
            background: var(--sage);
            color: white;
        }

        .table {
            font-size: 12px;
            vertical-align: middle;
        }

        .table thead th {
            background: #F6F6F0;
            color: #778174;
            font-weight: 600;
            padding: 13px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 13px;
        }

        .status {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 10px;
            white-space: nowrap;
        }

        .Menunggu { background: #F8EDCF; color: #896D2F; }
        .Diproses { background: #E2EAF6; color: #456895; }
        .Selesai { background: #E2EFE2; color: #47714B; }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 35px !important;
        }

        @media (max-width: 1100px) {
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main { padding: 25px; }
        }

        @media (max-width: 700px) {
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
                padding: 20px;
            }

            .brand-sub { margin-bottom: 15px; }
            .menu-label { margin-top: 15px; }
            .main { margin-left: 0; padding: 20px 14px; }
            .topline { align-items: flex-start; }
            h1 { font-size: 27px; }
            .admin { font-size: 11px; }
            .stats { gap: 10px; }
            .stat-card { padding: 15px; }
            .stat-value { font-size: 21px; }
            .section-card { padding: 15px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">✳ FRESHLAUNDRY</div>
    <div class="brand-sub">Laundry Management System</div>

    <div class="menu-label">MENU UTAMA</div>
    <a href="index.php" class="menu-link active">▦ &nbsp; Dashboard</a>
    <a href="pesanan.php" class="menu-link">▤ &nbsp; Data Pesanan</a>

    
    <div class="menu-label">INFORMASI</div>
    <a href="layanan.php" class="menu-link">♧ &nbsp; Layanan Laundry</a>

    <div class="menu-label">AKUN</div>
    <a href="logout.php" class="menu-link">↪ &nbsp; Logout</a>
</aside>


<main class="main">
    <div class="topline">
        <div>
            <div class="eyebrow">OVERVIEW</div>
            <h1>Dashboard</h1>
            <div class="subtitle">Pantau bisnis laundry kamu hari ini.</div>
        </div>
        <div class="admin">● &nbsp; Administrator</div>
    </div>

    <section class="welcome">
        <div class="eyebrow" style="color:#DCE8D8;">FRESH START, FRESH CLOTHES</div>
        <h2>Selamat datang di FRESHLAUNDRY!</h2>
        <p>Kelola pesanan dengan praktis, berikan pelayanan terbaik untuk setiap pelanggan.</p>
    </section>

    <section class="stats">
        <div class="stat-card">
            <div class="stat-icon">▤</div>
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value"><?= $totalPesanan ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">◷</div>
            <div class="stat-label">Sedang Diproses</div>
            <div class="stat-value"><?= $pesananProses ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✓</div>
            <div class="stat-label">Pesanan Selesai</div>
            <div class="stat-value"><?= $pesananSelesai ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">Rp</div>
            <div class="stat-label">Pendapatan Selesai</div>
            <div class="stat-value" style="font-size:20px;">
                Rp <?= number_format($pendapatan, 0, ',', '.') ?>
            </div>
        </div>
    </section>

    <section class="section-card">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="section-title">Pesanan Terbaru</div>
                <div class="subtitle">Lima pesanan terakhir yang masuk.</div>
            </div>
            <a href="tambah.php" class="btn-sage">+ Tambah Pesanan</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Berat</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($terbaru) > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($terbaru)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                        <td><?= htmlspecialchars($row['layanan']) ?></td>
                        <td><?= number_format((float)$row['berat'], 2, ',', '.') ?> kg</td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <span class="status <?= htmlspecialchars($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty">
                            Belum ada pesanan. Yuk, tambahkan pesanan pertamamu!
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="text-center subtitle mt-4">
        © <?= date('Y') ?> FRESHLAUNDRY · Clean clothes, happy days.
    </div>
</main>

</body>
</html>
