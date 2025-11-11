<?php
// 1. Oturumu başlat
session_start();

// 2. Veritabanı bağlantısını dahil et
require_once __DIR__ . '/config/db.php';

// 3. Sadece POST isteği ile gelindiyse işlem yap (CSRF'e karşı basit bir önlem)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 4. Formdan gelen verileri al
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // 5. Basit doğrulama (Boş mu?)
    if (empty($email) || empty($password)) {
        // Alanlar boşsa, hata kodu 2 ile login'e geri yönlendir
        header("Location: login.php?error=2");
        exit;
    }

    try {
        // 6. GÜVENLİ SORGULAMA (Hazırlanmış İfadeler - Prepared Statements)
        // E-postaya göre kullanıcıyı bul
        $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);

        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        // 7. KULLANICI KONTROLÜ VE ŞİFRE DOĞRULAMASI
        // $kullanici bulundu MU? VE
        // Veritabanındaki hash'lenmiş şifre ($kullanici['password'])
        // formdan gelen şifre ($password) ile eşleşiyor MU?

        if ($kullanici && password_verify($password, $kullanici['password'])) {

            // BAŞARILI GİRİŞ!

            // 8. Oturum (Session) değişkenlerini ayarla
            // Bu, kullanıcının artık giriş yaptığını belirler
            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_email'] = $kullanici['email'];

            // 9. Ana sayfaya (dashboard) yönlendir
            header("Location: index.php?page=dashboard");
            exit;

        } else {
            // BAŞARISIZ GİRİŞ
            // E-posta veya şifre yanlıştır.
            // Hata kodu 1 ile login'e geri yönlendir
            // Güvenlik gereği "E-posta bulunamadı" veya "Şifre yanlıştı" demeyiz.
            // Sadece "E-posta veya şifre hatalı!" deriz.
            header("Location: login.php?error=1");
            exit;
        }

    } catch (PDOException $e) {
        // Veritabanı hatası olursa
        die("Giriş işlemi sırasında bir veritabanı hatası oluştu: " . $e->getMessage());
    }

} else {
    // POST dışındaki bir yöntemle (örn: doğrudan URL yazarak) gelindiyse
    // ana sayfaya yönlendir (veya login'e)
    header("Location: login.php");
    exit;
}
?>