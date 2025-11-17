<?php
class Kullanici {

    public static function getir($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function sifreGuncelle($db, $id, $yeni_sifre_hash) {
        try {
            $stmt = $db->prepare("UPDATE kullanicilar SET password = ? WHERE id = ?");
            return $stmt->execute([$yeni_sifre_hash, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>