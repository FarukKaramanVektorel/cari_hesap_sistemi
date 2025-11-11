<?php
// GÜVENLİK: Oturum kontrolü ve Veritabanı bağlantısı
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    // Giriş yapılmamışsa, JSON hatası döndür ve çık
    http_response_code(401); // Yetkisiz
    echo json_encode(['error' => 'Yetkisiz erişim. Lütfen tekrar giriş yapın.']);
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Musteri.php';
// Not: Hareket modeline şu an ihtiyacımız yok, ancak gerekirse eklenebilir.

// 1. DataTables'in gönderdiği parametreleri al
$draw = $_POST['draw'] ?? 1;             // İstek sırası (güvenlik için)
$start = $_POST['start'] ?? 0;            // Başlangıç kaydı (Bizim $offset)
$length = $_POST['length'] ?? 10;         // Sayfa başına kayıt (Bizim $limit)
$search_value = $_POST['search']['value'] ?? ''; // Arama terimi

// 2. Modellerimizi kullanarak veriyi çek
// (Zaten yazdığımız o güçlü fonksiyonları burada kullanıyoruz)

// DataTables'in parametrelerini bizim fonksiyonlarımıza eşitle
$limit = $length;
$offset = $start;
$arama_terimi = $search_value;

// Veriyi çek
$musteriler = Musteri::listelePaging($db, $arama_terimi, $offset, $limit);

// Toplam kayıt sayılarını çek
$toplam_kayit_filtreli = Musteri::getToplamMusteriFiltreli($db, $arama_terimi);
$toplam_kayit = Musteri::getToplamMusteriFiltreli($db, ''); // Filtresiz toplam

// 3. DataTables'in istediği JSON formatına dönüştür
$data = []; // DataTables'e gönderilecek veri dizisi

foreach ($musteriler as $musteri) {
    // Her satır için verileri hazırla
    $row = [];

    // Sütun 0: Adı Soyadı
    $row[] = htmlspecialchars($musteri['ad']) . ' ' . htmlspecialchars($musteri['soyad']);

    // Sütun 1: Telefon
    $row[] = htmlspecialchars($musteri['telefon']);

    // Sütun 2: Bakiye (HTML ile)
    $bakiye = $musteri['bakiye'];
    $bakiye_class = ($bakiye > 0) ? 'text-danger' : (($bakiye < 0) ? 'text-success' : '');
    $row[] = '<strong class="' . $bakiye_class . '">' .
        htmlspecialchars(number_format($bakiye, 2, ',', '.')) . ' TL</strong>';

    // Sütun 3: Son İşlem Tarihi (HTML ile)
    if ($musteri['son_islem_tarihi']) {
        $row[] = htmlspecialchars(date('d.m.Y', strtotime($musteri['son_islem_tarihi'])));
    } else {
        $row[] = '<span class="text-muted">İşlem Yok</span>';
    }

    // Sütun 4: İşlemler (HTML Buton)
    $row[] = '<a href="index.php?page=musteri_detay&id=' . (int)$musteri['id'] . '"
                 class="btn btn-sm btn-info">
                 Detay
               </a>';

    // Hazırlanan satırı ana veri dizisine ekle
    $data[] = $row;
}

// 4. JSON Çıktısını Oluştur
$output = [
    "draw" => (int)$draw, // DataTables'e isteği geri gönder
    "recordsTotal" => (int)$toplam_kayit, // Toplam filtresiz kayıt
    "recordsFiltered" => (int)$toplam_kayit_filtreli, // Toplam filtrelenmiş kayıt
    "data" => $data // Verinin kendisi
];

// Çıktıyı JSON olarak bas ve işlemi bitir
header('Content-Type: application/json');
echo json_encode($output);
exit;