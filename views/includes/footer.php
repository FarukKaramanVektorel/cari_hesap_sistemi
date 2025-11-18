</div> <footer class="text-center text-muted mt-5 mb-3">
    <small>&copy; <?= date('Y') ?> Cari Hesap Takip Sistemi</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#musteri-tablosu').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax_musteriler.php",
                "type": "POST",
                "data": function(d) {
                    var filterBox = $('#borcFilter');
                    d.sadece_borclular = (filterBox.length > 0 && filterBox.is(':checked')) ? 1 : 0;
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


        $(document).on('change', '#borcFilter', function() {
            table.ajax.reload();


            var isChecked = $(this).is(':checked');
            var excelBtn = $('#btnExcel');

            if (excelBtn.length > 0) {
                if (isChecked) {
                    excelBtn.attr('href', 'excel_aktar.php?borclu=1');
                    excelBtn.html('Excele Aktar (Sadece Borçlular)');
                } else {
                    excelBtn.attr('href', 'excel_aktar.php');
                    excelBtn.html('Excele Aktar');
                }
            }
        });
    });
</script>
</body>
</html>