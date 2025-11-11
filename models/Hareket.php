<?php
// Bu dosya, parasal hareketlerle ilgili veritabanı işlerini yapar
class Hareket {

    /**
     * Belirtilen tipteki hareketlerin (borc veya tahsilat) toplam miktarını getirir.
     * @param PDO $db Veritabanı bağlantısı
     * @param string $tip 'borc' veya 'tahsilat'
     * @return float Toplam miktar
     */
    public static function getToplam($db, $tip) {
        try {
            // SQL Injection'a karşı '?' kullanarak güvenli sorgu
            $stmt = $db->prepare("SELECT SUM(miktar) FROM hareketler WHERE hareket_tipi = ?");
            $stmt->execute([$tip]);

            // Eğer hiç kayıt yoksa SUM() NULL dönebilir, ?? 0 ile bunu 0'a çeviriyoruz.
            return $stmt->fetchColumn() ?? 0;

        } catch (PDOException $e) {
            return 0;
        }
    }
    /**
     * YENİ: Belirli bir müşteriye ait tüm hareketleri tarihe göre (en yeni üste)
     * listeler.
     * @param PDO $db Veritabanı bağlantısı
     * @param int $musteri_id
     * @return array Hareket listesi
     */
    public static function listeleByMusteri($db, $musteri_id) {
        try {
            $stmt = $db->prepare(
                "SELECT * FROM hareketler 
                 WHERE musteri_id = ? 
                 ORDER BY tarih DESC, id DESC"
            );
            $stmt->execute([$musteri_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return []; // Hata olursa boş dizi
        }
    }


    /**
     * YENİ: Veritabanına yeni bir müşteri hareketi (borç/tahsilat) kaydeder.
     * SQL Injection'a karşı PREPARED STATEMENTS kullanır.
     * (HATA AYIKLAMA MODUNDA - die() içerir)
     * @param PDO $db Veritabanı bağlantısı
     * @param int $musteri_id
     * @param string $tarih (Y-m-d H:i:s formatında)
     * @param float $miktar
     * @param string $hareket_tipi ('borc' veya 'tahsilat')
     * @param string $aciklama
     * @return bool Başarılıysa true, değilse false
     */
    public static function ekle($db, $musteri_id, $tarih, $miktar, $hareket_tipi, $aciklama) {

        $sql = "INSERT INTO hareketler (musteri_id, tarih, miktar, hareket_tipi, aciklama) 
                VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $db->prepare($sql);

            // Değerleri '?' sırasına göre bir dizi olarak gönder
            $stmt->execute([
                $musteri_id,
                $tarih,
                $miktar,
                $hareket_tipi,
                $aciklama
            ]);

            // Başarılıysa true döndür
            return true;

        } catch (PDOException $e) {
            // HATA YAKALAMA
            // 'return false;' yerine, hatayı ekrana bas ve işlemi durdur.
            /*die("<h1>Veritabanı Hareket Kayıt Hatası!</h1>
                 <p>Sorgu çalıştırılamadı. Hata mesajı aşağıdadır:</p>
                 <pre style='background:#fee; border:1px solid red; padding:10px; font-weight:bold;'>
                 " . htmlspecialchars($e->getMessage()) . "
                 </pre>
                 <hr>
                 <p><b>Çalıştırılmaya Çalışılan SQL Sorgusu:</b></p>
                 <pre style='background:#eee; border:1px solid #ccc; padding:10px;'>
                 " . htmlspecialchars($sql) . "
                 </pre>");*/

             return false; // Hata ayıklama bitince bu satırı aç, die() satırını sil.
        }
    }
}