<?php
if (isset($_GET['success']) && $_GET['success'] == 'eklendi') {
    echo '<div class="alert alert-success">Yeni müşteri başarıyla eklendi.</div>';
}
?>

<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h2>Müşteri Listesi</h2>
        <div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="borcFilter">
            <label class="form-check-label fw-bold" for="borcFilter">Sadece Borcu Olanları (Alacaklı Olduklarımızı) Listele</label>
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
