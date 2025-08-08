jQuery(document).ready(function($) {
    var table = $('#takamoa-payments-table').DataTable();

    $('#takamoa-payments-table tbody').on('click', 'tr.payment-row', function() {
        var row = $(this);
        $('#modal-reference').text(row.data('reference'));
        $('#modal-name').text(row.data('client'));
        $('#modal-email').text(row.data('email') || '—');
        $('#modal-phone').text(row.data('phone') || '—');
        $('#modal-amount').text(row.data('amount'));
        $('#modal-status').text(row.data('status'));
        $('#modal-method').text(row.data('method'));
        $('#modal-date').text(row.data('date'));
        $('#modal-description').text(row.data('description') || '—');

        var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    });
});
