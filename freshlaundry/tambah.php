<?php
require 'cek_login.php';
require "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama_pelanggan"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $layanan = $_POST["layanan"] ?? "";
    $beratInput = $_POST["berat"] ?? "";

    $hargaLayanan = [
        "Cuci Reguler" => 7000,
        "Cuci Express" => 12000,
        "Cuci Setrika" => 10000
    ];

    if (
        $nama === "" ||
        $no_hp === "" ||
        !isset($hargaLayanan[$layanan]) ||
        !is_numeric($beratInput) ||
        (float)$beratInput <= 0 ||
        (float)$beratInput > 9999.99
    ) {
        $error = "Periksa kembali semua data pesanan.";
    } else {
        $berat = (float)$beratInput;
        $total = (int) round($hargaLayanan[$layanan] * $berat);
        $namaFoto = null;

        // Validasi foto jika pelanggan mengunggahnya
        if (
            isset($_FILES["foto_baju"]) &&
            $_FILES["foto_baju"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            $foto = $_FILES["foto_baju"];

            if ($foto["error"] !== UPLOAD_ERR_OK) {
                $error = "Foto gagal diunggah. Coba pilih foto lain.";
            } elseif ($foto["size"] > 2 * 1024 * 1024) {
                $error = "Ukuran foto maksimal 2 MB.";
            } else {
                $infoFoto = @getimagesize($foto["tmp_name"]);
                $tipeFoto = $infoFoto["mime"] ?? "";

                $formatFoto = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (
                    $infoFoto === false ||
                    !isset($formatFoto[$tipeFoto])
                ) {
                    $error = "Foto harus berformat JPG, PNG, atau WEBP.";
                } else {
                    $folderUpload = __DIR__ . "/uploads/";

                    if (!is_dir($folderUpload)) {
                        mkdir($folderUpload, 0755, true);
                    }

                    $namaFoto = bin2hex(random_bytes(16))
                        . "." . $formatFoto[$tipeFoto];

                    if (!move_uploaded_file(
                        $foto["tmp_name"],
                        $folderUpload . $namaFoto
                    )) {
                        $error = "Foto gagal disimpan. Periksa folder uploads.";
                        $namaFoto = null;
                    }
                }
            }
        }

        if ($error === "") {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO pesanan
                (nama_pelanggan, no_hp, layanan, berat, total_harga, foto_baju)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssdis",
                $nama,
                $no_hp,
                $layanan,
                $berat,
                $total,
                $namaFoto
            );

            // Tipe parameter: string, string, string, double, integer, string
            // Hapus spasi pada tipe parameter agar sesuai format mysqli.
            // Dibuat sebagai variabel supaya mudah dibaca.
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO pesanan
                (nama_pelanggan, no_hp, layanan, berat, total_harga, foto_baju)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssdis",
                $nama,
                $no_hp,
                $layanan,
                $berat,
                $total,
                $namaFoto
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: pesanan.php");
                exit;
            } else {
                if ($namaFoto !== null) {
                    $pathFoto = __DIR__ . "/uploads/" . $namaFoto;
                    if (is_file($pathFoto)) {
                        unlink($pathFoto);
                    }
                }

                $error = "Pesanan gagal disimpan. Silakan coba lagi.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan | FRESHLAUNDRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #F7F5EE;
            color: #303B31;
            font-family: 'DM Sans', sans-serif;
        }
        .navbar { background: #405B46; padding: 17px 0; }
        .navbar-brand {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            font-weight: 700;
        }
        .navbar-brand:hover { color: white; }
        .page { max-width: 850px; margin: 38px auto; padding: 0 18px; }
        .back { color: #506F54; text-decoration: none; font-size: 13px; }
        h1 { font-family: 'Playfair Display', serif; font-size: 32px; margin-top: 18px; }
        .subtitle { color: #899087; font-size: 13px; }
        .form-card {
            background: white;
            border: 1px solid #EAE9E0;
            border-radius: 18px;
            padding: 30px;
            margin-top: 25px;
        }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 9px; }
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
        .price-box { background: #EDF3E9; border-radius: 13px; padding: 20px; margin-top: 22px; }
        .price-label { color: #6D7E6D; font-size: 12px; }
        .price-value { color: #405B46; font-size: 27px; font-weight: 700; margin-top: 5px; }
        .btn-save {
            background: #405B46; color: white; border: 0;
            border-radius: 10px; padding: 12px 22px; font-size: 13px;
        }
        .btn-save:hover { background: #66836B; color: white; }
        .btn-cancel {
            background: #F0F0E9; color: #4C584C; border-radius: 10px;
            padding: 12px 22px; font-size: 13px; text-decoration: none;
        }
        #previewFoto {
            display: none;
            max-width: 100%;
            max-height: 250px;
            object-fit: contain;
            border-radius: 12px;
            margin-top: 12px;
            border: 1px solid #EAE9E0;
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
    <a href="index.php" class="back">← Kembali ke Dashboard</a>
    <h1>Tambah Pesanan</h1>
    <p class="subtitle">Masukkan informasi pelanggan dan detail cucian.</p>

    <section class="form-card">
        <div class="mb-4">
            <h5 class="fw-bold">Informasi Pelanggan</h5>
            <p class="subtitle">Lengkapi data pelanggan dengan benar.</p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="tambah.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" class="form-control"
                    placeholder="Contoh: Nadia Putri" maxlength="100"
                    value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Nomor HP</label>
                <input type="tel" name="no_hp" class="form-control"
                    placeholder="Contoh: 081234567890" maxlength="20"
                    value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
            </div>

            <hr style="border-color:#EAE9E0;">

            <h5 class="fw-bold mt-4 mb-3">Detail Cucian</h5>

            <div class="mb-3">
                <label class="form-label">Pilih Layanan</label>
                <select name="layanan" id="layanan" class="form-select" required>
                    <option value="">-- Pilih layanan laundry --</option>
                    <option value="Cuci Reguler" data-harga="7000"
                        <?= ($_POST['layanan'] ?? '') === 'Cuci Reguler' ? 'selected' : '' ?>>
                        Cuci Reguler — Rp7.000/kg
                    </option>
                    <option value="Cuci Express" data-harga="12000"
                        <?= ($_POST['layanan'] ?? '') === 'Cuci Express' ? 'selected' : '' ?>>
                        Cuci Express — Rp12.000/kg
                    </option>
                    <option value="Cuci Setrika" data-harga="10000"
                        <?= ($_POST['layanan'] ?? '') === 'Cuci Setrika' ? 'selected' : '' ?>>
                        Cuci Setrika — Rp10.000/kg
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Berat Cucian (kg)</label>
                <input type="number" name="berat" id="berat" class="form-control"
                    min="0.01" max="9999.99" step="0.01" placeholder="Contoh: 3.5"
                    value="<?= htmlspecialchars($_POST['berat'] ?? '') ?>" required>
            </div>

            <div class="mb-4">
                <label for="foto_baju" class="form-label">Foto Baju Laundry (opsional)</label>
                <input type="file" name="foto_baju" id="foto_baju"
                    class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <small class="text-muted">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                <img id="previewFoto" alt="Preview foto baju laundry">
            </div>

            <div class="price-box">
                <div class="price-label">Estimasi Total Pembayaran</div>
                <div class="price-value" id="totalHarga">Rp 0</div>
                <div class="price-label mt-1">Harga dihitung otomatis berdasarkan layanan dan berat.</div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                <a href="index.php" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">Simpan Pesanan →</button>
            </div>
        </form>
    </section>
</main>

<script>
    const layanan = document.getElementById('layanan');
    const berat = document.getElementById('berat');
    const totalHarga = document.getElementById('totalHarga');
    const inputFoto = document.getElementById('foto_baju');
    const previewFoto = document.getElementById('previewFoto');

    function hitungHarga() {
        const pilihan = layanan.options[layanan.selectedIndex];
        const harga = Number(pilihan.dataset.harga || 0);
        const jumlahBerat = Number(berat.value || 0);
        const total = harga > 0 && jumlahBerat > 0
            ? Math.round(harga * jumlahBerat)
            : 0;

        totalHarga.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    layanan.addEventListener('change', hitungHarga);
    berat.addEventListener('input', hitungHarga);

    inputFoto.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            previewFoto.style.display = 'none';
            previewFoto.removeAttribute('src');
            return;
        }

        const tipeDiizinkan = ['image/jpeg', 'image/png', 'image/webp'];

        if (!tipeDiizinkan.includes(file.type) || file.size > 2 * 1024 * 1024) {
            alert('Pilih foto JPG, PNG, atau WEBP dengan ukuran maksimal 2 MB.');
            this.value = '';
            previewFoto.style.display = 'none';
            previewFoto.removeAttribute('src');
            return;
        }

        previewFoto.src = URL.createObjectURL(file);
        previewFoto.style.display = 'block';
    });

    hitungHarga();
</script>

</body>
</html>