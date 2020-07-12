$(function () {
    $.nette.init();
});

$(document).ready(function() {
    $('#snippet--tableSnippet').DataTable({
        "pagingType": "full_numbers",
        "searching": false,
        "ordering": false,
        "sDom": 'Rfrtlip',
    });

    $('input').on('input', function() {
        $("#frm-filterForm").submit();
    });

    $("#frm-filterForm").on("submit", function() {
        let form = $(this);

        $.nette.ajax({
            url: form.attr("action"),
            method: "POST",
            data: form.serialize(),
            complete: function () {
                let table = $('#snippet--tableSnippet');
                table.DataTable().destroy();
                table.DataTable({
                    "pagingType": "full_numbers",
                    "searching": false,
                    "ordering": false,
                    "sDom": 'Rfrtlip',
                } )
            }
        });

        return false;
    });
});