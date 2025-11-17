<?php
$host = 'localhost';
$dbname = 'cari_hesap'; // Oluşturduğunuz veritabanının adı
$username = 'root';           // Veritabanı kullanıcı adınız
$password = '1234';               // Veritabanı şifreniz


ini_set('display_errors', 0); // Hataları ekranda gösterme
ini_set('log_errors', 1); // Hataları dosyaya kaydet
error_reporting(E_ALL);

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Veritabanı bağlantı hatası oluştu. Lütfen yönetici ile iletişime geçin.");
}