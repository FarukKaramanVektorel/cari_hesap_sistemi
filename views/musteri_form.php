<?php
$error_mesaji = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'bos') {
        $error_mesaji = "Ad ve Soyad alanları zorunludur. Lütfen boş bırakmayın.";
    } else {
        $error_mesaji = "Bilinmeyen bir hata oluştu.";
    }
}
?>

<?php if ($error_mesaji): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error_mesaji) ?>
    </div>
<?php endif; ?>

<div class="card shadow">
    <div class="card-header">
        <h3>Yeni Müşteri Ekle</h3>
        <p>Lütfen müşteri bilgilerini girin. (1000 karakter not alanı dahil)</p>
    </div>
    <div class="card-body">
        <form action="index.php?page=musteri_kaydet" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ad" class="form-label">Ad (*Zorunlu)</label>
                    <input type="text" class="form-control" id="ad" name="ad" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="soyad" class="form-label">Soyad (*Zorunlu)</label>
                    <input type="text" class="form-control" id="soyad" name="soyad" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="telefon" class="form-label">Telefon</label>
                <input type="tel" class="form-control" id="telefon" name="telefon" placeholder="05XX XXX XX XX">
            </div>

            <div class="mb-3">
                <label for="adres" class="form-label">Adres</label>
                <textarea class="form-control" id="adres" name="adres" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="not" class="form-label">Müşteri Notu (Max 1000 karakter)</label>
                <textarea class="form-control" id="not" name="not" rows="5" maxlength="1000"></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="index.php?page=musteriler" class="btn btn-secondary me-2">Vazgeç</a>
                <button type="submit" class="btn btn-primary">Müşteriyi Kaydet</button>
            </div>

        </form>
    </div>
</div>