<?php
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'hareket_eklendi') {
        echo '<div class="alert alert-success">Yeni hareket başarıyla eklendi.</div>';
    } elseif ($_GET['success'] == 'hareket_silindi') {
        echo '<div class="alert alert-warning">Hareket başarıyla silindi.</div>';
    } elseif ($_GET['success'] == 'hareket_guncellendi') {
        echo '<div class="alert alert-info">Hareket başarıyla güncellendi.</div>';
    }
}
if (isset($_GET['error'])) {
    $error_mesaji = "Bilinmeyen bir hata oluştu."; // Varsayılan
    if ($_GET['error'] == 'hareket_bos') {
        $error_mesaji = "Hata: Miktar, İşlem Tipi ve Tarih alanları zorunludur.";
    } elseif ($_GET['error'] == 'gecersiz_tarih') {
        $error_mesaji = "Hata: Seçtiğiniz tarih formatı geçersiz.";
    } elseif ($_GET['error'] == 'hareket_kayit') {
        $error_mesaji = "Hata: Hareket veritabanına kaydedilemedi.";
    }
    echo '<div class="alert alert-danger">' . htmlspecialchars($error_mesaji) . '</div>';
}
?>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <?= htmlspecialchars($musteri['ad']) ?> <?= htmlspecialchars($musteri['soyad']) ?>
        </h3>
        <a href="index.php?page=musteriler" class="btn btn-secondary">← Müşteri Listesine Dön</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Telefon:</strong> <?= htmlspecialchars($musteri['telefon']) ?></p>
                <p><strong>Adres:</strong> <?= nl2br(htmlspecialchars($musteri['adres'])) ?></p>

                <p><strong>Müşteri Notu:</strong></p>
                <div class="alert alert-info">
                    <?= nl2br(htmlspecialchars($musteri['note'])) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-header">
                        Güncel Bakiye
                    </div>
                    <div class="card-body">
                        <?php
                        $bakiye_class = ($bakiye > 0) ? 'text-danger' : (($bakiye < 0) ? 'text-success' : '');
                        ?>
                        <h2 class="card-title <?= $bakiye_class ?>">
                            <?= htmlspecialchars(number_format($bakiye, 2, ',', '.')) ?> TL
                        </h2>
                        <?php if ($bakiye > 0): ?>
                            <p class="text-danger">(Müşteri Borçlu)</p>
                        <?php elseif ($bakiye < 0): ?>
                            <p class="text-success">(Müşteri Alacaklı)</p>
                        <?php else: ?>
                            <p>(Bakiye Nötr)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <h4>Yeni Hareket Ekle (Borç / Tahsilat)</h4>
    </div>
    <div class="card-body">
        <form action="index.php?page=hareket_kaydet" method="POST" class="row g-3">

            <input type="hidden" name="musteri_id" value="<?= (int)$musteri['id'] ?>">

            <div class="col-md-3">
                <label for="miktar" class="form-label">Miktar (TL) *</label>
                <input type="number" step="0.01" class="form-control" id="miktar" name="miktar" required>
            </div>

            <div class="col-md-3">
                <label for="hareket_tipi" class="form-label">İşlem Tipi *</label>
                <select id="hareket_tipi" name="hareket_tipi" class="form-select" required>
                    <option value="borc">Borç (Satış / Hizmet)</option>
                    <option value="tahsilat">Tahsilat (Ödeme Alındı)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="tarih" class="form-label">İşlem Tarihi *</label>
                <input type="datetime-local" class="form-control" id="tarih" name="tarih"
                       value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>

            <div class="col-md-3">
                <label for="aciklama" class="form-label">Açıklama (Opsiyonel)</label>
                <input type="text" class="form-control" id="aciklama" name="aciklama" placeholder="Örn: Nakit Ödeme">
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Yeni Hareketi Kaydet</button>
            </div>
        </form>
    </div>
</div>


<div class="card shadow mb-4">
    <div class="card-header">
        <h4>Hareket Dökümü (Ekstre)</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th scope="col">Tarih</th>
                    <th scope="col">Tip</th>
                    <th scope="col">Açıklama</th>
                    <th scope="col" class="text-end">Tutar (TL)</th>
                    <th scope="col" class="text-center" width="150">İşlemler</th> </tr>
                </thead>
                <tbody>
                <?php if (empty($hareketler)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Bu müşteriye ait hiç hareket bulunamadı.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hareketler as $hareket): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($hareket['tarih']))) ?></td>
                            <td>
                                <?php if ($hareket['hareket_tipi'] == 'borc'): ?>
                                    <span class="badge bg-danger">BORÇ</span>
                                <?php else: ?>
                                    <span class="badge bg-success">TAHSİLAT</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($hareket['aciklama']) ?></td>
                            <td class="text-end">
                                <?= htmlspecialchars(number_format($hareket['miktar'], 2, ',', '.')) ?>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary btn-duzenle"
                                        data-id="<?= $hareket['id'] ?>"
                                        data-miktar="<?= $hareket['miktar'] ?>"
                                        data-tip="<?= $hareket['hareket_tipi'] ?>"
                                        data-tarih="<?= date('Y-m-d\TH:i', strtotime($hareket['tarih'])) ?>"
                                        data-aciklama="<?= htmlspecialchars($hareket['aciklama']) ?>">
                                    Düzenle
                                </button>

                                <a href="index.php?page=hareket_sil&id=<?= $hareket['id'] ?>&musteri_id=<?= $musteri['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu hareketi silmek istediğinize emin misiniz? Bakiye değişecektir.');">
                                    Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hareketi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=hareket_guncelle" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="hareket_id" id="modal_hareket_id">
                    <input type="hidden" name="musteri_id" value="<?= (int)$musteri['id'] ?>">

                    <div class="mb-3">
                        <label for="modal_miktar" class="form-label">Miktar (TL)</label>
                        <input type="number" step="0.01" class="form-control" id="modal_miktar" name="miktar" required>
                    </div>

                    <div class="mb-3">
                        <label for="modal_hareket_tipi" class="form-label">İşlem Tipi</label>
                        <select id="modal_hareket_tipi" name="hareket_tipi" class="form-select" required>
                            <option value="borc">Borç (Satış / Hizmet)</option>
                            <option value="tahsilat">Tahsilat (Ödeme Alındı)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modal_tarih" class="form-label">İşlem Tarihi</label>
                        <input type="datetime-local" class="form-control" id="modal_tarih" name="tarih" required>
                    </div>

                    <div class="mb-3">
                        <label for="modal_aciklama" class="form-label">Açıklama</label>
                        <input type="text" class="form-control" id="modal_aciklama" name="aciklama">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Sayfa yüklendiğinde çalışacak kodlar
    document.addEventListener('DOMContentLoaded', function () {

        // Tüm düzenle butonlarını seç
        var editButtons = document.querySelectorAll('.btn-duzenle');

        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Butonun üzerindeki data verilerini al
                var id = this.getAttribute('data-id');
                var miktar = this.getAttribute('data-miktar');
                var tip = this.getAttribute('data-tip');
                var tarih = this.getAttribute('data-tarih'); // Y-m-d\TH:i formatında gelmeli
                var aciklama = this.getAttribute('data-aciklama');

                // Modal içindeki inputları doldur
                document.getElementById('modal_hareket_id').value = id;
                document.getElementById('modal_miktar').value = miktar;
                document.getElementById('modal_hareket_tipi').value = tip;
                document.getElementById('modal_tarih').value = tarih;
                document.getElementById('modal_aciklama').value = aciklama;

                // Modalı aç (Bootstrap 5 Yöntemi)
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
            });
        });
    });
</script>