<?php
// Bu dosya, parasal hareketlerle ilgili veritabanı işlerini yapar
class Hareket {

    public static function getToplam($db, $tip) {
        try {
            $stmt = $db->prepare("SELECT SUM(miktar) FROM hareketler WHERE hareket_tipi = ?");
            $stmt->execute([$tip]);
            return $stmt->fetchColumn() ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
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
            return [];
        }
    }


    public static function ekle($db, $musteri_id, $tarih, $miktar, $hareket_tipi, $aciklama) {

        $sql = "INSERT INTO hareketler (musteri_id, tarih, miktar, hareket_tipi, aciklama) 
                VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $musteri_id,
                $tarih,
                $miktar,
                $hareket_tipi,
                $aciklama
            ]);
            return true;

        } catch (PDOException $e) {
             return false;
        }
    }
}