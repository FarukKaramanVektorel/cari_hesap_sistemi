<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    die("Yetkisiz erişim.");
}


require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Musteri.php';

$dosya_adi = date('d-m-Y') . '-musteri-listesi.xls';
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$dosya_adi\"");
$musteriler = Musteri::listelePaging($db, '', 0, 999999);

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
echo '<table border="1">';
echo '<thead>';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>Adı</th>';
echo '<th>Soyadı</th>';
echo '<th>Telefon</th>';
echo '<th>Bakiye (TL)</th>';
echo '<th>Son İşlem Tarihi</th>';
echo '<th>Adres</th>';
echo '<th>Müşteri Notu</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';


foreach ($musteriler as $musteri) {
    $ad = htmlspecialchars($musteri['ad']);
    $soyad = htmlspecialchars($musteri['soyad']);
    $telefon = htmlspecialchars($musteri['telefon']);
    $adres = htmlspecialchars($musteri['adres']);
    $note = htmlspecialchars($musteri['note']);


    $bakiye = $musteri['bakiye'];
    $son_islem = $musteri['son_islem_tarihi']
        ? date('d.m.Y', strtotime($musteri['son_islem_tarihi']))
        : 'İşlem Yok';

    echo '<tr>';
    echo '<td>' . (int)$musteri['id'] . '</td>';
    echo '<td>' . $ad . '</td>';
    echo '<td>' . $soyad . '</td>';
    echo '<td>' . $telefon . '</td>';
    echo '<td>' . $bakiye . '</td>';
    echo '<td>' . $son_islem . '</td>';
    echo '<td>' . $adres . '</td>';
    echo '<td>' . $note . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</body></html>';

exit;