<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    die("Yetkisiz erişim.");
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Musteri.php';

$sadece_borclular = isset($_GET['borclu']) && $_GET['borclu'] == '1';

$dosya_adi = date('d-m-Y') . ($sadece_borclular ? '-borclu-musteriler.csv' : '-tum-musteriler.csv');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $dosya_adi . '"');


$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));


fputcsv($output, ['ID', 'Adı', 'Soyadı', 'Telefon', 'Bakiye (TL)', 'Son İşlem Tarihi', 'Adres', 'Müşteri Notu'], ";");

$musteriler = Musteri::listelePaging($db, '', 0, 999999, $sadece_borclular);

foreach ($musteriler as $musteri) {

    $son_islem = $musteri['son_islem_tarihi']
        ? date('d.m.Y', strtotime($musteri['son_islem_tarihi']))
        : 'İşlem Yok';


    $bakiye = str_replace('.', ',', $musteri['bakiye']);


    fputcsv($output, [
        $musteri['id'],
        $musteri['ad'],
        $musteri['soyad'],
        $musteri['telefon'],
        $bakiye,
        $son_islem,
        str_replace(["\r", "\n"], " ", $musteri['adres']),
        str_replace(["\r", "\n"], " ", $musteri['note'])
    ], ";");
}

fclose($output);
exit;
?>