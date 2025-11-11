<?php
// Güçlü bir şifre belirleyin (örn: CokGuv_123)
$hashlenmis_sifre = password_hash("1234", PASSWORD_DEFAULT);
echo $hashlenmis_sifre;
?>