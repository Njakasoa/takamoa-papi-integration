jQuery(document).ready(function($) {
    var table = $('#takamoa-payments-table').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        pagingType: 'full_numbers',
        dom: '<"datatable-header d-flex justify-content-between align-items-center mb-3"lf>rt<"datatable-footer d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            lengthMenu: '_MENU_',
            search: '',
            searchPlaceholder: 'Search…'
        }
    });

    var wrapper = $('#takamoa-payments-table_wrapper');
    wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
    wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Search…');
    wrapper.find('.dataTables_length label, .dataTables_filter label').addClass('d-flex align-items-center gap-2 mb-0');

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
