<?php
// Veritabanı bilgileri
$host = 'localhost';          // Genellikle 'localhost'
$dbname = 'cari_hesap'; // Oluşturduğunuz veritabanının adı
$username = 'root';           // Veritabanı kullanıcı adınız
$password = '1234';               // Veritabanı şifreniz

// Hata gösterimini aç (Geliştirme aşaması için)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // PDO (PHP Data Objects) ile güvenli bağlantı
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // Hata modunu 'exception' olarak ayarla
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Bağlantı hatası olursa, işlemi durdur ve hatayı göster
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}