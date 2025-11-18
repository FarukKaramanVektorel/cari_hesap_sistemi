<?php
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
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h4>Müşteri Listesi</h4>
        <div>
            <a href="excel_aktar.php" target="_blank" id="btnExcel" class="btn btn-success btn-sm me-2">
                Excele Aktar
            </a>
            <a href="index.php?page=musteri_ekle" class="btn btn-primary btn-sm">
                Yeni Müşteri Ekle
            </a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card p-2 bg-light">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="borcFilter">
                <label class="form-check-label fw-bold" for="borcFilter">Sadece Borcu Olanları (Alacaklı Olduklarımızı) Listele</label>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="musteri-tablosu" class="table table-striped table-bordered" style="width:100%">
        <thead class="table-dark">
        <tr>
            <th>Adı Soyadı</th>
            <th>Telefon</th>
            <th class="text-end">Bakiye (Durum)</th>
            <th class="text-center">Son İşlem Tarihi</th>
            <th class="text-center" width="120px">İşlemler</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>