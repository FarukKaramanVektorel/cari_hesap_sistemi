<?php
$mesaj = '';
$mesaj_tipi = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $mesaj = "Şifreniz başarıyla güncellendi.";
    $mesaj_tipi = "success";
} elseif (isset($_GET['error'])) {
    $error = $_GET['error'];
    if ($error == 'bos') {
        $mesaj = "Lütfen tüm alanları doldurun.";
    } elseif ($error == 'uyusmuyor') {
        $mesaj = "Yeni şifreler birbiriyle uyuşmuyor.";
    } elseif ($error == 'eski_sifre_yanlis') {
        $mesaj = "Girdiğiniz mevcut şifre yanlış.";
    } elseif ($error == 'guncelleme_hatasi') {
        $mesaj = "Veritabanı hatası: Şifre güncellenemedi.";
    } else {
        $mesaj = "Bilinmeyen bir hata oluştu.";
    }
    $mesaj_tipi = "danger";
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <h2>Şifre Değiştir</h2>
        <p>Güvenliğiniz için mevcut şifrenizi ve yeni şifrenizi girin.</p>

        <?php if ($mesaj): ?>
            <div class="alert alert-<?= htmlspecialchars($mesaj_tipi) ?>">
                <?= htmlspecialchars($mesaj) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form action="index.php?page=sifre_kaydet" method="POST">

                    <div class="mb-3">
                        <label for="eski_sifre" class="form-label">Mevcut Şifreniz</label>
                        <input type="password" class="form-control" id="eski_sifre" name="eski_sifre" required>
                    </div>

                    <div class="mb-3">
                        <label for="yeni_sifre" class="form-label">Yeni Şifre</label>
                        <input type="password" class="form-control" id="yeni_sifre" name="yeni_sifre" required>
                    </div>

                    <div class="mb-3">
                        <label for="yeni_sifre_onay" class="form-label">Yeni Şifre (Tekrar)</label>
                        <input type="password" class="form-control" id="yeni_sifre_onay" name="yeni_sifre_onay" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>