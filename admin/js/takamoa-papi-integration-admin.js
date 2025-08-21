/**
* Admin scripts for ticket and design management.
*
* @since 0.0.3
*/
jQuery(document).ready(function ($) {
        function openModal(id) {
                $('#' + id).addClass('show');
        }
        function closeModal(id) {
                $('#' + id).removeClass('show');
        }

        $(document).on('click', '[data-close]', function () {
                closeModal($(this).data('close'));
        });

        $('.tk-modal').on('click', function (e) {
                if ($(e.target).is('.tk-modal')) {
                        closeModal($(this).attr('id'));
                }
        });

        if ($('#takamoa-payments-table').length) {
		var table = $('#takamoa-payments-table').DataTable({
			pageLength: 10,
			lengthMenu: [5, 10, 25, 50],
			pagingType: 'full_numbers',
			dom: '<"datatable-header d-flex justify-content-between align-items-center mb-3"lf>rt<"datatable-footer d-flex justify-content-between align-items-center mt-3"ip>',
			language: {
				lengthMenu: '_MENU_',
				search: '',
				searchPlaceholder: 'Search…',
			},
		});
		
		var wrapper = $('#takamoa-payments-table_wrapper');
		wrapper
		.find('.dataTables_length select')
		.addClass('form-select form-select-sm');
		wrapper
		.find('.dataTables_filter input')
		.addClass('form-control form-control-sm')
		.attr('placeholder', 'Search…');
		wrapper
		.find('.dataTables_length label, .dataTables_filter label')
		.addClass('d-flex align-items-center gap-2 mb-0');
		
		$('#takamoa-payments-table').on('click', '.takamoa-details', function (e) {
			e.stopPropagation();
			var row = $(this).closest('tr');
			$('#modal-reference').text(row.data('reference'));
			$('#modal-name').text(row.data('client'));
			$('#modal-email').text(row.data('email') || '—');
			$('#modal-phone').text(row.data('phone') || '—');
			$('#modal-amount').text(row.data('amount'));
			$('#modal-status').text(row.data('status'));
			$('#modal-method').text(row.data('method'));
			$('#modal-date').text(row.data('date'));
			$('#modal-description').text(row.data('description') || '—');
			$('#modal-id').text(row.data('id'));
			$('#modal-provider').text(row.data('provider') || '—');
			$('#modal-success-url').text(row.data('successUrl') || '—');
			$('#modal-failure-url').text(row.data('failureUrl') || '—');
			$('#modal-notification-url').text(row.data('notificationUrl') || '—');
			$('#modal-link-creation').text(row.data('linkCreation') || '—');
			$('#modal-link-expiration').text(row.data('linkExpiration') || '—');
			$('#modal-payment-link').text(row.data('paymentLink') || '—');
			$('#modal-currency').text(row.data('currency') || '—');
			$('#modal-fee').text(row.data('fee') || '—');
                $('#modal-notification-token').text(
                        row.data('notificationToken') || '—'
                );
		$('#modal-test-mode').text(row.data('isTestMode') ? 'Yes' : 'No');
		$('#modal-test-reason').text(row.data('testReason') || '—');
		$('#modal-raw-request').text(row.data('rawRequest') || '—');
		$('#modal-raw-response').text(row.data('rawResponse') || '—');
		$('#modal-raw-notification').text(row.data('rawNotification') || '—');
		$('#modal-updated-at').text(row.data('updatedAt') || '—');
		
		$('#modal-extra-info').addClass('d-none');
		$('#toggle-more-info').text('Show more');
		
                openModal('paymentModal');
});

$('#toggle-more-info').on('click', function () {
	$('#modal-extra-info').toggleClass('d-none');
        $(this).text(
        $('#modal-extra-info').hasClass('d-none') ? 'Show more' : 'Show less'
);
});

$('#takamoa-payments-table').on('click', '.takamoa-notify', function (e) {
	e.stopPropagation();
	var btn = $(this);
	var row = btn.closest('tr');
	btn.prop('disabled', true);
	$.post(takamoaAjax.ajaxurl, {
		action: 'takamoa_resend_payment_email',
		nonce: takamoaAjax.nonce,
		reference: row.data('reference'),
	})
	.done(function (res) {
		alert(
		res.data && res.data.message
		? res.data.message
		: 'Notification envoyée',
	);
})
.fail(function () {
	alert("Erreur lors de l'envoi de la notification");
})
.always(function () {
	btn.prop('disabled', false);
});
});

$('#takamoa-payments-table').on('click', '.takamoa-regenerate-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var btn = $(this);
        var row = btn.closest('tr');
        var reference = btn.data('reference') || (row.length ? row.data('reference') : '');
        if (!reference) {
                return;
        }
        btn.prop('disabled', true);
        $.post(takamoaAjax.ajaxurl, {
                action: 'takamoa_regenerate_payment_link',
                nonce: takamoaAjax.nonce,
                reference: reference,
        })
                .done(function (res) {
                        if (res.success && res.data) {
                                alert('Lien régénéré');
                                row.data('paymentLink', res.data.payment_link).attr('data-payment-link', res.data.payment_link);
                                row.data('linkCreation', res.data.link_creation).attr('data-link-creation', res.data.link_creation);
                                row.data('linkExpiration', res.data.link_expiration).attr('data-link-expiration', res.data.link_expiration);
                                row.data('notificationToken', res.data.notification_token).attr('data-notification-token', res.data.notification_token);
                                row.data('rawRequest', res.data.raw_request).attr('data-raw-request', res.data.raw_request);
                                row.data('rawResponse', res.data.raw_response).attr('data-raw-response', res.data.raw_response);
                                row.data('updatedAt', res.data.updated_at).attr('data-updated-at', res.data.updated_at);
                                row.data('status', res.data.status).attr('data-status', res.data.status);
                                row.data('method', res.data.payment_method).attr('data-method', res.data.payment_method);
                                row.find('td').eq(5).text(res.data.status);
                                row.find('td').eq(6).text(res.data.payment_method);
                        } else {
                                alert(
                                        res.data && res.data.message
                                                ? res.data.message
                                                : 'Erreur lors de la régénération'
                                );
                        }
                })
                .fail(function () {
                        alert('Erreur lors de la régénération');
                })
                .always(function () {
                        btn.prop('disabled', false);
                });
});

var currentRef = '';
$('#takamoa-payments-table').on('click', '.takamoa-generate-ticket', function (e) {
	e.stopPropagation();
	currentRef = $(this).closest('tr').data('reference');
        openModal('ticketModal');
});

$('#generate-ticket-btn').on('click', function () {
	var btn = $(this);
	var design = $('#ticket-design').val();
	if (!design || !currentRef) {
		return;
	}

	function generate() {
		btn.prop('disabled', true);
		$.post(takamoaAjax.ajaxurl, {
			action: 'takamoa_generate_ticket',
			nonce: takamoaAjax.nonce,
			reference: currentRef,
			design_id: design,
		})
		.done(function (res) {
			if (res.success && res.data.url) {
				if (confirm('Billet généré. Voulez-vous télécharger le billet ?')) {
					window.open(res.data.url, '_blank');
				} else {
					window.location.href = takamoaAjax.ticketsPage;
				}
			} else {
				alert(
					res.data && res.data.message
					? res.data.message
					: 'Erreur lors de la génération',
				);
			}
		})
		.fail(function () {
			alert('Erreur lors de la génération');
		})
		.always(function () {
                        btn.prop('disabled', false);
                        closeModal('ticketModal');
                });
        }

	$.post(takamoaAjax.ajaxurl, {
		action: 'takamoa_ticket_exists',
		nonce: takamoaAjax.nonce,
		reference: currentRef,
	})
	.done(function (res) {
		if (res.success && res.data.exists) {
			if (confirm('Un billet existe déjà pour cette référence. Voulez-vous le remplacer ?')) {
				generate();
			}
		} else {
			generate();
		}
	})
	.fail(function () {
		alert('Erreur lors de la vérification du billet');
	});
});
}

if ($('#takamoa-tickets-table').length) {
	var ttable = $('#takamoa-tickets-table').DataTable({
		pageLength: 10,
		lengthMenu: [5, 10, 25, 50],
		pagingType: 'full_numbers',
		dom: '<"datatable-header d-flex justify-content-between align-items-center mb-3"lf>rt<"datatable-footer d-flex justify-content-between align-items-center mt-3"ip>',
		language: {
			lengthMenu: '_MENU_',
			search: '',
			searchPlaceholder: 'Search…',
		},
	});
	
	var twrapper = $('#takamoa-tickets-table_wrapper');
	twrapper
	.find('.dataTables_length select')
	.addClass('form-select form-select-sm');
	twrapper
	.find('.dataTables_filter input')
	.addClass('form-control form-control-sm')
	.attr('placeholder', 'Search…');
	twrapper
	.find('.dataTables_length label, .dataTables_filter label')
	.addClass('d-flex align-items-center gap-2 mb-0');
	
	$('#takamoa-tickets-table').on('click', '.takamoa-send-ticket-email', function (e) {
	e.stopPropagation();
	var btn = $(this);
	var row = btn.closest('tr');
	btn.prop('disabled', true);
	$.post(takamoaAjax.ajaxurl, {
	action: 'takamoa_send_ticket_email',
	nonce: takamoaAjax.nonce,
	reference: row.data('reference'),
	})
		.done(function (res) {
	alert(
	res.data && res.data.message
	? res.data.message
	: 'Billet envoyé'
	);
	})
	.fail(function () {
	alert("Erreur lors de l'envoi du billet");
	})
	.always(function () {
	btn.prop('disabled', false);
	});
	});
}

if ($('.takamoa-set-default').length) {
        $('.takamoa-set-default').on('click', function () {
                var btn = $(this);
                var id = btn.data('id');
                $.post(takamoaAjax.ajaxurl, {
                        action: 'takamoa_set_default_design',
                        nonce: takamoaAjax.nonce,
                        design_id: id,
                })
                        .done(function (res) {
                                if (res.success) {
                                        $('.takamoa-set-default i').removeClass('fa-star text-warning').addClass('fa-star-o');
                                        btn.find('i').removeClass('fa-star-o').addClass('fa-star text-warning');
                                } else {
                                        alert(res.data && res.data.message ? res.data.message : 'Erreur lors de la mise à jour');
                                }
                        })
                        .fail(function () {
                                alert('Erreur lors de la mise à jour');
                        });
        });
}

if ($('.takamoa-delete-design').length) {
        $('.takamoa-delete-design').on('click', function (e) {
                if (!confirm('Supprimer ce design ?')) {
                        e.preventDefault();
                }
        });
}
if ($('#qr-reader').length) {
	// QR code scanning feature @since 0.0.5
	var scanResult = $('#scan-result');
	var rescanBtn = $('#rescan-btn');
	var scanner = new Html5Qrcode('qr-reader');
	var processing = false;
	
	function startScanner() {
		processing = false;
		scanner.start(
		{ facingMode: 'environment' },
		{ fps: 10, qrbox: 250 },
		onScan
	);
}

function onScan(decodedText) {
	if (processing) return;
	processing = true;
	scanner.stop().then(function () {
		$('#qr-reader').hide();
	}).catch(function () {});
	$.post(takamoaAjax.ajaxurl, {
		action: 'takamoa_scan_ticket',
		nonce: takamoaAjax.nonce,
		reference: decodedText
	}).done(function (res) {
		if (res.success) {
			var html = '<div class="card"><div class="card-body"><h5>' + res.data.name + '</h5><p>Email: ' + (res.data.email || '—') + '<br>Téléphone: ' + (res.data.phone || '—') + '<br>Entreprise: ' + (res.data.description || '—') + '<br>Status: <span class="ticket-status">' + res.data.status + '</span></p>';
			if (res.data.status === 'GENERATED') {
				html += '<button id="validate-ticket" data-ref="' + decodedText + '" class="button button-primary mt-2">Valider le billet</button>';
			}
			html += '</div></div>';
			scanResult.html(html);
		} else {
			scanResult.html('<div class="alert alert-danger">' + (res.data && res.data.message ? res.data.message : 'Billet introuvable') + '</div>');
		}
		rescanBtn.show();
	}).fail(function () {
		scanResult.html('<div class="alert alert-danger">Erreur de connexion</div>');
		rescanBtn.show();
	});
}

startScanner();

rescanBtn.on('click', function () {
	scanResult.empty();
	$(this).hide();
	$('#qr-reader').show();
	startScanner();
});

scanResult.on('click', '#validate-ticket', function () {
	var btn = $(this);
	btn.prop('disabled', true);
	$.post(takamoaAjax.ajaxurl, {
		action: 'takamoa_validate_ticket',
		nonce: takamoaAjax.nonce,
		reference: btn.data('ref')
	}).done(function (res) {
		if (res.success) {
			scanResult.find('.ticket-status').text(res.data.status);
			btn.remove();
		} else {
			alert(res.data && res.data.message ? res.data.message : 'Erreur lors de la validation');
			btn.prop('disabled', false);
		}
	}).fail(function () {
		alert('Erreur de connexion');
		btn.prop('disabled', false);
	});
});
}
});
