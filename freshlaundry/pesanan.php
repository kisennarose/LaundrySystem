<?php
require 'cek_login.php';
require "config.php";

$cari = trim($_GET['cari'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM pesanan WHERE 1=1";
$params = [];
$types = "";

if ($cari !== '') {
    $sql .= " AND nama_pelanggan LIKE ?";
    $params[] = "%" . $cari . "%";
    $types .= "s";
}

if (in_array($statusFilter, ['Menunggu', 'Diproses', 'Selesai'], true)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmtPesanan = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmtPesanan, $types, ...$params);
}

mysqli_stmt_execute($stmtPesanan);
$pesanan = mysqli_stmt_get_result($stmtPesanan);

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan");
$totalPesanan = mysqli_fetch_assoc($totalQuery)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan | FRESHLAUNDRY</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        body {
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
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin: 7px 0;
        }

        .subtitle {
            color: #899087;
            font-size: 13px;
        }

        .section-card {
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 16px;
            padding: 24px;
            margin-top: 28px;
        }

        .btn-sage {
            display: inline-block;
            padding: 11px 16px;
            background: #405B46;
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 12px;
            text-decoration: none;
        }

        .btn-sage:hover {
            background: #66836B;
            color: white;
        }

        .table {
            font-size: 12px;
            vertical-align: middle;
        }

        .table thead th {
            padding: 14px 12px;
            color: #778174;
            background: #F6F6F0;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 12px;
        }

        .foto-baju {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #EAE9E0;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 10px;
            white-space: nowrap;
        }

        .Menunggu {
            background: #F8EDCF;
            color: #896D2F;
        }

        .Diproses {
            background: #E2EAF6;
            color: #456895;
        }

        .Selesai {
            background: #E2EFE2;
            color: #47714B;
        }

        .btn-edit,
        .btn-delete,
        .btn-nota {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 11px;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-edit {
            color: #405B46;
            background: #E6EEE4;
        }

        .btn-nota {
            color: #456895;
            background: #E2EAF6;
        }

        .btn-delete {
            color: #A44747;
            background: #F9E6E3;
        }

        .empty {
            text-align: center;
            padding: 40px !important;
            color: #899087;
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

            .section-card {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">✳ FRESHLAUNDRY</div>
    <div class="brand-sub">Laundry Management System</div>

    <div class="menu-label">MENU UTAMA</div>
    <a href="index.php" class="menu-link">▦ &nbsp; Dashboard</a>
    <a href="pesanan.php" class="menu-link active">▤ &nbsp; Data Pesanan</a>

    <div class="menu-label">INFORMASI</div>
    <a href="layanan.php" class="menu-link">♧ &nbsp; Layanan Laundry</a>
</aside>

<main class="main">

    <div class="eyebrow">MANAJEMEN LAUNDRY</div>
    <h1>Data Pesanan</h1>
    <p class="subtitle">Kelola semua transaksi dan cucian pelanggan.</p>

    <section class="section-card">

        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
            <div>
                <h5 class="fw-bold mb-1">Daftar Pesanan</h5>
                <div class="subtitle">
                    Total <?= $totalPesanan ?> pesanan terdaftar
                </div>
            </div>

            <a href="tambah.php" class="btn-sage">+ Tambah Pesanan</a>
        </div>

        
<form method="GET" action="pesanan.php" class="row g-2 mb-4">
    <div class="col-12 col-md-5">
        <input
            type="text"
            name="cari"
            class="form-control"
            placeholder="Cari nama pelanggan..."
            value="<?= htmlspecialchars($cari) ?>"
        >
    </div>

    <div class="col-12 col-md-4">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="Menunggu" <?= $statusFilter === 'Menunggu' ? 'selected' : '' ?>>
                Menunggu
            </option>
            <option value="Diproses" <?= $statusFilter === 'Diproses' ? 'selected' : '' ?>>
                Diproses
            </option>
            <option value="Selesai" <?= $statusFilter === 'Selesai' ? 'selected' : '' ?>>
                Selesai
            </option>
        </select>
    </div>

    <div class="col-6 col-md-1">
        <button type="submit" class="btn-sage w-100">Cari</button>
    </div>

    <div class="col-6 col-md-2">
        <a href="pesanan.php"
           class="btn-sage w-100 text-center d-block"
           style="background:#E6EEE4;color:#405B46;text-decoration:none;">
            Reset
        </a>
    </div>
</form>
        
        <div class="table-responsive">
            <table class="table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pelanggan</th>
                        <th>Foto Baju</th>
                        <th>No. HP</th>
                        <th>Layanan</th>
                        <th>Berat</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (mysqli_num_rows($pesanan) > 0): ?>

                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= htmlspecialchars($row['nama_pelanggan']) ?>
                        </td>

                        <td>
                            <?php
                            $foto = $row['foto_baju'] ?? '';
                            $lokasiFoto = "uploads/" . basename($foto);
                            ?>

                            <?php if (
                                !empty($foto) &&
                                is_file(__DIR__ . "/" . $lokasiFoto)
                            ): ?>
                                <a href="<?= htmlspecialchars($lokasiFoto) ?>" target="_blank">
                                    <img
                                        src="<?= htmlspecialchars($lokasiFoto) ?>"
                                        alt="Foto baju pelanggan"
                                        class="foto-baju"
                                    >
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ada foto</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($row['no_hp']) ?></td>

                        <td><?= htmlspecialchars($row['layanan']) ?></td>

                        <td>
                            <?= number_format((float)$row['berat'], 2, ',', '.') ?> kg
                        </td>

                        <td>
                            Rp <?= number_format((float)$row['total_harga'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <span class="status <?= htmlspecialchars($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                        </td>

                        <td>
                            <div class="d-flex gap-1 flex-wrap">

                                <a
                                    href="nota.php?id=<?= (int)$row['id'] ?>"
                                    class="btn-nota"
                                >Nota</a>

                                <a
                                    href="edit.php?id=<?= (int)$row['id'] ?>"
                                    class="btn-edit"
                                >Edit</a>

                                <form
                                    action="hapus.php"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$row['id'] ?>"
                                    >

                                    <button type="submit" class="btn-delete">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="10" class="empty">
                            Belum ada pesanan laundry.<br>
                            Klik tombol Tambah Pesanan untuk memulai.
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