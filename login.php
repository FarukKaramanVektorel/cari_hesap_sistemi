<?php
session_start();
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php?page=dashboard");
    exit;
}
$error_mesaji = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_mesaji = "E-posta veya şifre hatalı!";
    } elseif ($_GET['error'] == 2) {
        $error_mesaji = "Lütfen tüm alanları doldurun.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Cari Takip Sistemi</title>
    <link href="https://cdn.jsdelivr.npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <h2 class="text-center mb-4">Cari Hesap Takip</h2>

            <div class="card shadow">
                <div class="card-body p-4">
                    <h5 class="card-title text-center mb-3">Yönetici Girişi</h5>

                    <?php if (!empty($error_mesaji)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error_mesaji) // XSS Koruması ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_action.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Giriş Yap</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>