<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz erişim. Lütfen tekrar giriş yapın.']);
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Musteri.php';
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search_value = $_POST['search']['value'] ?? '';

$limit = $length;
$offset = $start;
$arama_terimi = $search_value;
$sadece_borclular = $_POST['sadece_borclular'] ?? 0;
$musteriler = Musteri::listelePaging($db, $arama_terimi, $offset, $limit, $sadece_borclular);
$toplam_kayit_filtreli = Musteri::getToplamMusteriFiltreli($db, $arama_terimi, $sadece_borclular);
$toplam_kayit = Musteri::getToplamMusteriFiltreli($db, '');
$data = [];
foreach ($musteriler as $musteri) {
    $row = [];
    $row[] = htmlspecialchars($musteri['ad']) . ' ' . htmlspecialchars($musteri['soyad']);
    $row[] = htmlspecialchars($musteri['telefon']);
    $bakiye = $musteri['bakiye'];
    $bakiye_class = ($bakiye > 0) ? 'text-danger' : (($bakiye < 0) ? 'text-success' : '');
    $row[] = '<strong class="' . $bakiye_class . '">' .
        htmlspecialchars(number_format($bakiye, 2, ',', '.')) . ' TL</strong>';
    if ($musteri['son_islem_tarihi']) {
        $son_islem_obj = date_create($musteri['son_islem_tarihi']);
        $bugun_obj = date_create('now');
        $fark = date_diff($bugun_obj, $son_islem_obj);
        $gecen_gun = $fark->days;
        $tarih_str = htmlspecialchars(date('d.m.Y', strtotime($musteri['son_islem_tarihi'])));
        $renk_class = ($gecen_gun > 30) ? 'text-danger' : 'text-success';
        $row[] = $tarih_str .
            '<br><small class="' . $renk_class . ' fw-bold">(' . $gecen_gun . ' gündür işlem yapılmıyor)</small>';

    } else {
        $row[] = '<span class="text-muted">İşlem Yok</span>';
    }
    $row[] = '<a href="index.php?page=musteri_detay&id=' . (int)$musteri['id'] . '"
                 class="btn btn-sm btn-info">
                 Detay
               </a>';
    $data[] = $row;
}
$output = [
    "draw" => (int)$draw,
    "recordsTotal" => (int)$toplam_kayit,
    "recordsFiltered" => (int)$toplam_kayit_filtreli,
    "data" => $data
];
header('Content-Type: application/json');
echo json_encode($output);
exit;