<?php
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
            $stmt->execute([$musteri_id, $tarih, $miktar, $hareket_tipi, $aciklama]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function sil($db, $id) {
        try {
            $stmt = $db->prepare("DELETE FROM hareketler WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }


    public static function guncelle($db, $id, $tarih, $miktar, $hareket_tipi, $aciklama) {
        $sql = "UPDATE hareketler SET tarih = ?, miktar = ?, hareket_tipi = ?, aciklama = ? WHERE id = ?";
        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([$tarih, $miktar, $hareket_tipi, $aciklama, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getir($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM hareketler WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}