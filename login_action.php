<?php
session_start();
require_once __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=2");
        exit;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($kullanici && password_verify($password, $kullanici['password'])) {
            session_regenerate_id(true);
            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_email'] = $kullanici['email'];
            header("Location: index.php?page=dashboard");
            exit;

        } else {
            header("Location: login.php?error=1");
            exit;
        }

    } catch (PDOException $e) {
        die("Giriş işlemi sırasında bir veritabanı hatası oluştu: " . $e->getMessage());
    }

} else {
    header("Location: login.php");
    exit;
}
?>