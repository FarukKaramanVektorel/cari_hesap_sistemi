<?php
// GÜVENLİK NOTU: Bu dosya index.php tarafından çağrılır.

// Müşteri ekleme/silme sonrası gelen başarı mesajları
if (isset($_GET['success']) && $_GET['success'] == 'eklendi') {
    echo '<div class="alert alert-success">Yeni müşteri başarıyla eklendi.</div>';
}
// (Silme eklendiğinde buraya 'success=silindi' de eklenebilir)
?>

<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h2>Müşteri Listesi</h2>
        <a href="index.php?page=musteri_ekle" class="btn btn-primary">Yeni Müşteri Ekle</a>
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

<script>
    // Sayfa yüklendiğinde çalış
    $(document).ready(function() {

        // 'musteri-tablosu' ID'li tablomuzu DataTables olarak başlat
        $('#musteri-tablosu').DataTable({
            "processing": true,  // "İşleniyor..." mesajı göster
            "serverSide": true,  // Sunucu taraflı çalış
            "ajax": {
                "url": "ajax_musteriler.php", // Veri kaynağı
                "type": "POST"                // İstek metodu
            },
            "language": { // Arayüzü Türkçeleştirme
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
            },
            "columns": [ // Sütunları tanımla (AJAX'taki sırayla eşleşmeli)
                null, // Adı Soyadı (Sütun 0)
                null, // Telefon (Sütun 1)
                { "className": "text-end" }, // Bakiye (Sütun 2) - Sağa yasla
                { "className": "text-center" }, // Son İşlem (Sütun 3) - Ortala

                // İşlemler (Detay Butonu) sütunu:
                {
                    "className": "text-center", // Ortala
                    "orderable": false,  // Bu sütuna göre sıralama yapılamasın
                    "searchable": false  // Bu sütunda arama yapılmasın
                }
            ]
        });
    });
</script>