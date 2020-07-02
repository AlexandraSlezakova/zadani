$(document).ready(function() {
    $.nette.init();

    $('#snippet--tableSnippet').DataTable({
        "pagingType": "full_numbers",
        "searching": false,
        "ordering": false,
        "sDom": 'Rfrtlip',
    } );
});