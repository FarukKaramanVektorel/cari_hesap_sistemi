<?php

class Musteri {
    public static function getToplamMusteri($db) {
        try {
            $stmt = $db->query("SELECT COUNT(id) FROM musteriler");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    public static function listelePaging($db, $arama_terimi = '', $offset = 0, $limit = 10, $sadece_borclular = false) {
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
        $arama_params = [];

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


        $sql_group = " 
            GROUP BY 
                m.id, m.ad, m.soyad, m.telefon, m.adres, m.note, m.created_at 
        ";

        $sql_having = "";
        if ($sadece_borclular) {
            $sql_having = " HAVING bakiye > 0.001 ";
        }
        $sql_order = " ORDER BY m.ad ASC, m.soyad ASC ";
        $sql_limit = " LIMIT ? OFFSET ? ";
        $sql_final = $sql_base . $sql_where . $sql_group . $sql_having . $sql_order . $sql_limit;
        $limit_params = [(int) $limit, (int) $offset];
        $final_params = array_merge($arama_params, $limit_params);

        try {
            $stmt = $db->prepare($sql_final);
            $param_count = count($final_params);
            $arama_param_sayisi = $param_count - 2;
            for ($i = 0; $i < $param_count; $i++) {
                if ($i < $arama_param_sayisi) {
                    $stmt->bindValue($i + 1, $final_params[$i], PDO::PARAM_STR);
                } else {
                    $stmt->bindValue($i + 1, (int) $final_params[$i], PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            return [];
        }
    }

    public static function getToplamMusteriFiltreli($db, $arama_terimi = '', $sadece_borclular = false) {


        if (!$sadece_borclular) {
            $sql = "SELECT COUNT(id) FROM musteriler m";
            $params = [];

            if (!empty(trim($arama_terimi))) {
                $kelimeler = explode(' ', $arama_terimi);
                $where_kosullari = [];

                foreach ($kelimeler as $kelime) {
                    $kelime = trim($kelime);
                    if (empty($kelime)) continue;

                    $where_kosullari[] = "(m.ad LIKE ? OR m.soyad LIKE ? OR m.telefon LIKE ? OR m.note LIKE ?)";
                    $arama_param = "%" . $kelime . "%";
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                }

                if (!empty($where_kosullari)) {
                    $sql .= " WHERE " . implode(' AND ', $where_kosullari);
                }
            }

            try {
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchColumn();
            } catch (PDOException $e) {
                return 0;
            }
        }


        else {
            $sql = "
                SELECT COUNT(*) FROM (
                    SELECT m.id
                    FROM musteriler m
                    LEFT JOIN hareketler h ON m.id = h.musteri_id
            ";


            $params = [];
            $where_str = "";

            if (!empty(trim($arama_terimi))) {
                $kelimeler = explode(' ', $arama_terimi);
                $where_kosullari = [];

                foreach ($kelimeler as $kelime) {
                    $kelime = trim($kelime);
                    if (empty($kelime)) continue;

                    $where_kosullari[] = "(m.ad LIKE ? OR m.soyad LIKE ? OR m.telefon LIKE ? OR m.note LIKE ?)";
                    $arama_param = "%" . $kelime . "%";
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                    $params[] = $arama_param;
                }

                if (!empty($where_kosullari)) {
                    $where_str = " WHERE " . implode(' AND ', $where_kosullari);
                }
            }

            $sql .= $where_str;


            $sql .= "
                    GROUP BY m.id
                    HAVING (
                        SUM(CASE WHEN h.hareket_tipi = 'borc' THEN h.miktar ELSE 0 END) - 
                        SUM(CASE WHEN h.hareket_tipi = 'tahsilat' THEN h.miktar ELSE 0 END)
                    ) > 0.001
                ) as borclular_tablosu
            ";

            try {
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchColumn();
            } catch (PDOException $e) {
                return 0;
            }
        }
    }

    public static function getBakiye($db, $musteri_id) {
        try {
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
            return 0;
        }
    }

    public static function getSonIslemTarihi($db, $musteri_id) {
        try {
            $stmt = $db->prepare(
                "SELECT tarih FROM hareketler 
                 WHERE musteri_id = ? 
                 ORDER BY tarih DESC 
                 LIMIT 1"
            );
            $stmt->execute([$musteri_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return null;
        }
    }


    public static function ekle($db, $ad, $soyad, $telefon, $adres, $note) {
        $sql = "INSERT INTO musteriler (ad, soyad, telefon, adres, note) 
                VALUES (:ad, :soyad, :telefon, :adres, :note)";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':ad', $ad);
            $stmt->bindParam(':soyad', $soyad);
            $stmt->bindParam(':telefon', $telefon);
            $stmt->bindParam(':adres', $adres);
            $stmt->bindParam(':note', $note);
            $stmt->execute();
            return $db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getir($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM musteriler WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}