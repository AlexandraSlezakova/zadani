$(document).ready(function() {
    $.nette.init();

    $('#channel-table').DataTable({
        "pagingType": "full_numbers",
        "searching": false,
        "ordering": false,
    } );
});