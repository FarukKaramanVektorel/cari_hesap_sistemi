<?php
// Bu dosya, müşterilerle ilgili tüm veritabanı işlerini yapar
class Musteri {

    /**
     * Sistemdeki toplam müşteri sayısını getirir.
     * @param PDO $db Veritabanı bağlantısı
     * @return int Müşteri sayısı
     */
    public static function getToplamMusteri($db) {
        try {
            $stmt = $db->query("SELECT COUNT(id) FROM musteriler");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Hata durumunda loglama yapılabilir
            return 0;
        }
    }

    /**
     * Tüm müşterileri veritabanından çeker. (GÜNCELLENMİŞ VERSİYON)
     * ARAMA (kelime bazlı) ve SAYFALAMA özelliklerini içerir.
     * @param PDO $db Veritabanı bağlantısı
     * @param string $arama_terimi Arama yapılacak kelime (Ad, Soyad, Telefon veya Not)
     * @param int $offset Kayıtların başlangıç noktası (LIMIT offset)
     * @param int $limit Sayfa başına kayıt sayısı (LIMIT limit)
     * @return array Müşteri listesi
     */
    public static function listelePaging($db, $arama_terimi = '', $offset = 0, $limit = 10) {

        // Temel SQL sorgusu
        $sql_base = "
            SELECT
                m.*, 
                (
                    SUM(CASE WHEN h.hareket_tipi = 'borc' THEN h.miktar ELSE 0 END) - 
                    SUM(CASE WHEN h.hareket_tipi = 'tahsilat' THEN h.miktar ELSE 0 END)
                ) as bakiye,
                MAX(h.tarih) as son_islem_tarihi
            FROM 
                musteriler m
            LEFT JOIN 
                hareketler h ON m.id = h.musteri_id
        ";

        $sql_where = "";
        $arama_params = []; // Sadece arama parametreleri

        // ARAMA MANTIĞI (Kelimelere böl)
        if (!empty(trim($arama_terimi))) {
            $kelimeler = explode(' ', $arama_terimi);
            $where_kosullari = [];

            foreach ($kelimeler as $kelime) {
                $kelime = trim($kelime);
                if (empty($kelime)) continue;

                $where_kosullari[] = "(m.ad LIKE ? OR m.soyad LIKE ? OR m.telefon LIKE ? OR m.note LIKE ?)";

                $arama_param = "%" . $kelime . "%";
                $arama_params[] = $arama_param;
                $arama_params[] = $arama_param;
                $arama_params[] = $arama_param;
                $arama_params[] = $arama_param;
            }

            if (!empty($where_kosullari)) {
                $sql_where = " WHERE " . implode(' AND ', $where_kosullari);
            }
        }

        // Sorgunun geri kalanı
        $sql_group_order = "
            GROUP BY 
                m.id, m.ad, m.soyad, m.telefon, m.adres, m.note, m.created_at
            ORDER BY 
                m.ad ASC, m.soyad ASC
        ";

        $sql_limit = " LIMIT ? OFFSET ?";
        // Limit ve offset parametrelerini diziye ekle
        $limit_params = [(int) $limit, (int) $offset];

        // Tüm sorgu parçalarını birleştir
        $sql_final = $sql_base . $sql_where . $sql_group_order . $sql_limit;

        // Arama parametrelerini ve Limit parametrelerini birleştir
        $final_params = array_merge($arama_params, $limit_params);

        try {
            $stmt = $db->prepare($sql_final);

            // Parametreleri PDO'ya güvenli bir şekilde bağla
            $param_count = count($final_params);
            // Son iki parametre (limit, offset) INT, kalanlar STR olmalı
            $arama_param_sayisi = $param_count - 2;

            for ($i = 0; $i < $param_count; $i++) {
                if ($i < $arama_param_sayisi) {
                    // Bunlar arama parametreleri (STRING)
                    $stmt->bindValue($i + 1, $final_params[$i], PDO::PARAM_STR);
                } else {
                    // Bunlar LIMIT ve OFFSET (INTEGER)
                    $stmt->bindValue($i + 1, (int) $final_params[$i], PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Liste (Paging) sorgusunda hata: " . $e->getMessage();
            return [];
        }
    }

    public static function getToplamMusteriFiltreli($db, $arama_terimi = '') {
        $sql = "SELECT COUNT(id) FROM musteriler m";
        $params = [];

        if (!empty(trim($arama_terimi))) {
            // Arama terimini boşluklara göre kelimelere ayır
            $kelimeler = explode(' ', $arama_terimi);
            $where_kosullari = [];

            foreach ($kelimeler as $kelime) {
                $kelime = trim($kelime);
                if (empty($kelime)) continue; // Boş kelimeleri atla

                // Her kelime için 4 sütunu da kontrol eden bir OR grubu ekle
                $where_kosullari[] = "(m.ad LIKE ? OR m.soyad LIKE ? OR m.telefon LIKE ? OR m.note LIKE ?)";

                $arama_param = "%" . $kelime . "%";
                // Her '?' için parametreleri ekle
                $params[] = $arama_param;
                $params[] = $arama_param;
                $params[] = $arama_param;
                $params[] = $arama_param;
            }

            if (!empty($where_kosullari)) {
                // Her kelime grubunu 'AND' ile birleştir
                $sql .= " WHERE " . implode(' AND ', $where_kosullari);
            }
        }

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Toplam Müşteri Sayısı (Filtreli) sorgusunda hata: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * Belirli bir müşterinin NET bakiyesini hesaplar.
     * (Toplam Borç - Toplam Tahsilat)
     * @param PDO $db Veritabanı bağlantısı
     * @param int $musteri_id Müşteri ID'si
     * @return float Net bakiye
     */
    public static function getBakiye($db, $musteri_id) {
        try {
            // SQL Injection'a karşı '?' kullanarak güvenli sorgu
            $stmt = $db->prepare(
                "SELECT 
                    (SELECT SUM(miktar) FROM hareketler WHERE musteri_id = ? AND hareket_tipi = 'borc') as toplam_borc,
                    (SELECT SUM(miktar) FROM hareketler WHERE musteri_id = ? AND hareket_tipi = 'tahsilat') as toplam_tahsilat"
            );
            $stmt->execute([$musteri_id, $musteri_id]);
            $sonuclar = $stmt->fetch(PDO::FETCH_ASSOC);

            $toplam_borc = $sonuclar['toplam_borc'] ?? 0;
            $toplam_tahsilat = $sonuclar['toplam_tahsilat'] ?? 0;

            return $toplam_borc - $toplam_tahsilat;

        } catch (PDOException $e) {
            return 0; // Hata olursa bakiye 0
        }
    }

    /**
     * Belirli bir müşterinin son işlem tarihini getirir.
     * @param PDO $db Veritabanı bağlantısı
     * @param int $musteri_id Müşteri ID'si
     * @return string|null Son işlem tarihi (veya yoksa null)
     */
    public static function getSonIslemTarihi($db, $musteri_id) {
        try {
            $stmt = $db->prepare(
                "SELECT tarih FROM hareketler 
                 WHERE musteri_id = ? 
                 ORDER BY tarih DESC 
                 LIMIT 1"
            );
            $stmt->execute([$musteri_id]);
            return $stmt->fetchColumn(); // Sadece ilk sütunu getirir (tarih)
        } catch (PDOException $e) {
            return null; // Hata olursa
        }
    }

    /**
     * Veritabanına yeni bir müşteri kaydeder.
     * SQL Injection'a karşı PREPARED STATEMENTS kullanır.
     * (HATA AYIKLAMA MODUNDA - die() içerir)
     * @param PDO $db Veritabanı bağlantısı
     * @param string $ad
     * @param string $soyad
     * @param string $telefon
     * @param string $adres
     * @param string $not
     * @return string|false Eklenen son kaydın ID'si veya hata durumunda false
     */
    public static function ekle($db, $ad, $soyad, $telefon, $adres, $note) {

        // Sorgumuzu bir değişkene alalım (Hata ayıklama için bu daha iyi)
        $sql = "INSERT INTO musteriler (ad, soyad, telefon, adres, note) 
                VALUES (:ad, :soyad, :telefon, :adres, :note)";

        try {
            $stmt = $db->prepare($sql);

            // Değerleri parametrelere bağla
            $stmt->bindParam(':ad', $ad);
            $stmt->bindParam(':soyad', $soyad);
            $stmt->bindParam(':telefon', $telefon);
            $stmt->bindParam(':adres', $adres);
            $stmt->bindParam(':note', $note);

            // Sorguyu çalıştır
            $stmt->execute();

            // Başarılıysa, eklenen kaydın ID'sini döndür
            return $db->lastInsertId();

        } catch (PDOException $e) {

            echo "Kayıt hatası: "; // Geliştirme için
            return false;
        }
    }
    /**
     * ID'ye göre tek bir müşteri kaydını getirir.
     * @param PDO $db Veritabanı bağlantısı
     * @param int $id Müşteri ID'si
     * @return array|false Müşteri bilgileri veya bulunamazsa false
     */
    public static function getir($db, $id) {
        try {
            // SQL Injection'a karşı '?' kullan
            $stmt = $db->prepare("SELECT * FROM musteriler WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}