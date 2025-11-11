<?php
// GÜVENLİK NOTU:
// Bu dosya, doğrudan tarayıcıdan açılamaz.
// Yalnızca index.php tarafından 'require' edilir.
// Bu yüzden $db, $toplamMusteri gibi değişkenlere erişimi vardır.
?>

<div class="row">
    <div class="col-12 mb-3">
        <h2>Ana Sayfa</h2>
        <p>Sisteminize hoş geldiniz. Aşağıda genel durum özetini görebilirsiniz.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-danger shadow">
            <div class="card-body">
                <h5 class="card-title">TOPLAM NET ALACAK</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars(number_format($netAlacak, 2, ',', '.')) ?> TL
                </p>
                <small>Müşterilerin toplam borcundan toplam tahsilat düşüldükten sonra kalan net bakiye.</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success shadow">
            <div class="card-body">
                <h5 class="card-title">TOPLAM TAHSİLAT</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars(number_format($toplamTahsilat, 2, ',', '.')) ?> TL
                </p>
                <small>Bugüne kadar müşterilerden aldığınız toplam ödeme miktarı.</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-dark bg-warning shadow">
            <div class="card-body">
                <h5 class="card-title">TOPLAM MÜŞTERİ</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars($toplamMusteri) ?>
                </p>
                <small>Sistemde kayıtlı toplam aktif müşteri sayısı.</small>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-12">
        <h4>Hızlı İşlemler</h4>
        <a href="index.php?page=musteriler" class="btn btn-primary">Müşterileri Görüntüle</a>
    </div>
</div>