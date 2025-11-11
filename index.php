<?php
require_once __DIR__ . '/views/includes/header.php';
require_once __DIR__ . '/models/Musteri.php';
require_once __DIR__ . '/models/Hareket.php';
$page = $_GET['page'] ?? 'dashboard';

switch ($page) {

    case 'dashboard':
        $toplamMusteri = Musteri::getToplamMusteri($db);
        $toplamTahsilat = Hareket::getToplam($db, 'tahsilat');
        $toplamBorc = Hareket::getToplam($db, 'borc');
        $netAlacak = $toplamBorc - $toplamTahsilat;
        require_once __DIR__ . '/views/dashboard.php';
        break;

    case 'musteriler':
        require_once __DIR__ . '/views/musteri_listesi.php';
        break;

    case 'musteri_ekle':
        require_once __DIR__ . '/views/musteri_form.php';
        break;

    case 'musteri_detay':
        $musteri_id = $_GET['id'] ?? 0;

        if (empty($musteri_id)) {
            header("Location: index.php?page=musteriler");
            exit;
        }
        $musteri = Musteri::getir($db, $musteri_id);
        if (!$musteri) {
            header("Location: index.php?page=musteriler");
            exit;
        }
        $hareketler = Hareket::listeleByMusteri($db, $musteri_id);
        $bakiye = Musteri::getBakiye($db, $musteri_id);
        require_once __DIR__ . '/views/musteri_detay.php';
        break;
    case 'hareket_kaydet':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $musteri_id = $_POST['musteri_id'] ?? 0;
            $miktar = $_POST['miktar'] ?? 0;
            $hareket_tipi = $_POST['hareket_tipi'] ?? null;
            $tarih_input = $_POST['tarih'] ?? null;
            $aciklama = $_POST['aciklama'] ?? null;
            if (empty($musteri_id) || empty($miktar) || empty($hareket_tipi) || empty($tarih_input)) {
                header("Location: index.php?page=musteri_detay&id=$musteri_id&error=hareket_bos");
                exit;
            }
            try {
                $tarih_formatli = date('Y-m-d H:i:s', strtotime($tarih_input));
            } catch (Exception $e) {
                header("Location: index.php?page=musteri_detay&id=$musteri_id&error=gecersiz_tarih");
                exit;
            }
            $basarili = Hareket::ekle(
                $db,
                $musteri_id,
                $tarih_formatli,
                $miktar,
                $hareket_tipi,
                $aciklama
            );
            if ($basarili) {
                header("Location: index.php?page=musteri_detay&id=$musteri_id&success=hareket_eklendi");
                exit;
            } else {
                header("Location: index.php?page=musteri_detay&id=$musteri_id&error=hareket_kayit");
                exit;
            }

        } else {
            header("Location: index.php?page=dashboard");
            exit;
        }
        break;
    case 'musteri_kaydet':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ad = $_POST['ad'] ?? null;
            $soyad = $_POST['soyad'] ?? null;
            $telefon = $_POST['telefon'] ?? null;
            $adres = $_POST['adres'] ?? null;
            $note = $_POST['note'] ?? null;
            if (empty($ad) || empty($soyad)) {
                header("Location: index.php?page=musteri_ekle&error=bos");
                exit;
            }
            $yeniMusteriId = Musteri::ekle($db, $ad, $soyad, $telefon, $adres, $note);

            if ($yeniMusteriId) {
                header("Location: index.php?page=musteriler&success=eklendi");
                exit;
            } else {

                header("Location: index.php?page=musteri_ekle&error=kayit");
                exit;
            }

        } else {
            header("Location: index.php?page=musteriler");
            exit;
        }
        break;
    default:
        echo "<div class='alert alert-danger'>Hata: Aradığınız sayfa ('" . htmlspecialchars($page) . "') bulunamadı.</div>";
        require_once __DIR__ . '/views/dashboard.php';
        break;
}
require_once __DIR__ . '/views/includes/footer.php';
?>