</div> <footer class="text-center text-muted mt-5 mb-3">
    <small>&copy; <?= date('Y') ?> Cari Hesap Takip Sistemi</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" ...></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" ...></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
    $(document).ready(function() {
        var table = $('#musteri-tablosu').DataTable({ // Değişkene atadık
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax_musteriler.php",
                "type": "POST",
                "data": function(d) {
                    // Checkbox durumunu sunucuya gönderilecek verilere ekliyoruz
                    d.sadece_borclular = $('#borcFilter').is(':checked') ? 1 : 0;
                }
            },
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
            },
            "columns": [
                null,
                null,
                { "className": "text-end" },
                { "className": "text-center" },
                {
                    "className": "text-center",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });

        // Checkbox değiştiğinde tabloyu yenile
        $('#borcFilter').change(function() {
            table.ajax.reload();
        });
    });
</script>
</body>
</html>