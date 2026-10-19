<?php
require 'cek_login.php';
require "config.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: pesanan.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM pesanan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: pesanan.php");
    exit;
}

$error = "";

$hargaLayanan = [
    "Cuci Reguler" => 7000,
    "Cuci Express" => 12000,
    "Cuci Setrika" => 10000
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama_pelanggan"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $layanan = $_POST["layanan"] ?? "";
    $beratInput = $_POST["berat"] ?? "";
    $status = $_POST["status"] ?? "";

    $statusValid = ["Menunggu", "Diproses", "Selesai"];

    if (
        $nama === "" ||
        strlen($nama) > 100 ||
        $no_hp === "" ||
        strlen($no_hp) > 20 ||
        !isset($hargaLayanan[$layanan]) ||
        !is_numeric($beratInput) ||
        (float)$beratInput <= 0 ||
        (float)$beratInput > 9999.99 ||
        !in_array($status, $statusValid, true)
    ) {
        $error = "Periksa kembali data yang kamu masukkan.";
    } else {
        $berat = (float)$beratInput;
        $total = (int) round($hargaLayanan[$layanan] * $berat);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pesanan
             SET nama_pelanggan = ?,
                 no_hp = ?,
                 layanan = ?,
                 berat = ?,
                 total_harga = ?,
                 status = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssdisi",
            $nama,
            $no_hp,
            $layanan,
            $berat,
            $total,
            $status,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: pesanan.php");
            exit;
        } else {
            $error = "Data gagal diperbarui.";
            mysqli_stmt_close($stmt);
        }
    }

    $data["nama_pelanggan"] = $nama;
    $data["no_hp"] = $no_hp;
    $data["layanan"] = $layanan;
    $data["berat"] = $beratInput;
    $data["status"] = $status;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan | FRESHLAUNDRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #F7F5EE;
            color: #303B31;
            font-family: 'DM Sans', sans-serif;
        }

        .navbar {
            background: #405B46;
            padding: 17px 0;
        }

        .navbar-brand {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            font-weight: 700;
        }

        .navbar-brand:hover { color: white; }

        .page {
            max-width: 850px;
            margin: 38px auto;
            padding: 0 18px;
        }

        .back {
            color: #506F54;
            text-decoration: none;
            font-size: 13px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-top: 18px;
        }

        .subtitle {
            color: #899087;
            font-size: 13px;
        }

        .form-card {
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 18px;
            padding: 30px;
            margin-top: 25px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        .form-control, .form-select {
            padding: 12px 14px;
            border-color: #E3E5DC;
            border-radius: 10px;
            font-size: 13px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #66836B;
            box-shadow: 0 0 0 3px #66836B20;
        }

        .price-box {
            background: #EDF3E9;
            border-radius: 13px;
            padding: 20px;
            margin-top: 22px;
        }

        .price-label {
            color: #6D7E6D;
            font-size: 12px;
        }

        .price-value {
            color: #405B46;
            font-size: 27px;
            font-weight: 700;
            margin-top: 5px;
        }

        .btn-save {
            background: #405B46;
            color: white;
            border: 0;
            border-radius: 10px;
            padding: 12px 22px;
            font-size: 13px;
        }

        .btn-save:hover {
            background: #66836B;
            color: white;
        }

        .btn-cancel {
            background: #F0F0E9;
            color: #4C584C;
            border-radius: 10px;
            padding: 12px 22px;
            font-size: 13px;
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="navbar-brand">✳ FRESHLAUNDRY</a>
    </div>
</nav>

<main class="page">
    <a href="pesanan.php" class="back">← Kembali ke Data Pesanan</a>

    <h1>Edit Pesanan</h1>
    <p class="subtitle">Perbarui informasi pelanggan dan status cucian.</p>

    <section class="form-card">
        <h5 class="fw-bold mb-1">Informasi Pesanan</h5>
        <p class="subtitle mb-4">Perubahan harga dihitung otomatis.</p>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan"
                    class="form-control" maxlength="100" required
                    value="<?= htmlspecialchars($data['nama_pelanggan']) ?>">
            </div>

            <div class="mb-4">
                <label class="form-label">Nomor HP</label>
                <input type="tel" name="no_hp"
                    class="form-control" maxlength="20" required
                    value="<?= htmlspecialchars($data['no_hp']) ?>">
            </div>

            <hr style="border-color:#EAE9E0;">

            <div class="mb-3 mt-4">
                <label class="form-label">Layanan Laundry</label>
                <select name="layanan" id="layanan" class="form-select" required>
                    <option value="Cuci Reguler"
                        <?= $data['layanan'] === 'Cuci Reguler' ? 'selected' : '' ?>>
                        Cuci Reguler — Rp7.000/kg
                    </option>
                    <option value="Cuci Express"
                        <?= $data['layanan'] === 'Cuci Express' ? 'selected' : '' ?>>
                        Cuci Express — Rp12.000/kg
                    </option>
                    <option value="Cuci Setrika"
                        <?= $data['layanan'] === 'Cuci Setrika' ? 'selected' : '' ?>>
                        Cuci Setrika — Rp10.000/kg
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Berat Cucian (kg)</label>
                <input type="number" name="berat" id="berat"
                    class="form-control" min="0.01" max="9999.99"
                    step="0.01" required
                    value="<?= htmlspecialchars((string)$data['berat']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Status Laundry</label>
                <select name="status" class="form-select" required>
                    <?php foreach (["Menunggu", "Diproses", "Selesai"] as $pilihan): ?>
                        <option value="<?= $pilihan ?>"
                            <?= $data['status'] === $pilihan ? 'selected' : '' ?>>
                            <?= $pilihan ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="price-box">
                <div class="price-label">Total Pembayaran</div>
                <div class="price-value" id="totalHarga">Rp 0</div>
                <div class="price-label mt-1">Dihitung dari layanan dan berat cucian.</div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                <a href="pesanan.php" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">Simpan Perubahan →</button>
            </div>
        </form>
    </section>
</main>

<script>
    const layanan = document.getElementById('layanan');
    const berat = document.getElementById('berat');
    const totalHarga = document.getElementById('totalHarga');

    function hitungHarga() {
        const pilihan = layanan.options[layanan.selectedIndex];
        const harga = {
            'Cuci Reguler': 7000,
            'Cuci Express': 12000,
            'Cuci Setrika': 10000
        };

        const tarif = harga[pilihan.value] || 0;
        const jumlahBerat = Number(berat.value || 0);
        const total = tarif > 0 && jumlahBerat > 0
            ? Math.round(tarif * jumlahBerat)
            : 0;

        totalHarga.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    layanan.addEventListener('change', hitungHarga);
    berat.addEventListener('input', hitungHarga);
    hitungHarga();
</script>

</body>
</html>
