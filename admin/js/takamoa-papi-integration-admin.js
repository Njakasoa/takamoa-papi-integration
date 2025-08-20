/**
* Admin scripts for ticket and design management.
*
* @since 0.0.3
*/
jQuery(document).ready(function ($) {
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
				row.data('notificationToken') || '—',
			);
			$('#modal-test-mode').text(row.data('isTestMode') ? 'Yes' : 'No');
			$('#modal-test-reason').text(row.data('testReason') || '—');
			$('#modal-raw-request').text(row.data('rawRequest') || '—');
			$('#modal-raw-response').text(row.data('rawResponse') || '—');
			$('#modal-raw-notification').text(row.data('rawNotification') || '—');
			$('#modal-updated-at').text(row.data('updatedAt') || '—');

			$('#modal-extra-info').addClass('d-none');
			$('#toggle-more-info').text('Show more');

			var modal = new bootstrap.Modal(
				document.getElementById('paymentModal'),
			);
			modal.show();
		});

		$('#toggle-more-info').on('click', function () {
			$('#modal-extra-info').toggleClass('d-none');
			$(this).text(
				$('#modal-extra-info').hasClass('d-none') ? 'Show more' : 'Show less',
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

		var currentRef = '';
		$('#takamoa-payments-table').on('click', '.takamoa-generate-ticket', function (e) {
			e.stopPropagation();
			currentRef = $(this).closest('tr').data('reference');
			var modal = new bootstrap.Modal(document.getElementById('ticketModal'));
			modal.show();
		});

		$('#generate-ticket-btn').on('click', function () {
			var btn = $(this);
			var design = $('#ticket-design').val();
			if (!design || !currentRef) {
				return;
			}
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
					bootstrap.Modal.getInstance(
						document.getElementById('ticketModal'),
					).hide();
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
	}

	if ($('#select_design_image').length) {
		var frame;
		$('#select_design_image').on('click', function (e) {
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Choisir une image',
				button: { text: 'Utiliser cette image' },
				multiple: false,
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				$('#design_image').val(att.url);
				$('#ticket_width').val(att.width);
				$('#ticket_height').val(att.height);
			});
			frame.open();
		});
	}
	if ($('#qr-reader').length) {
		var scanResult = $('#scan-result');
		var scanner = new Html5Qrcode('qr-reader');
		var processing = false;
               scanner.start(
                       { facingMode: 'environment' },
                       { fps: 10, qrbox: 250 },
                       function (decodedText) {
                               if (processing) return;
                               processing = true;
                               scanner
                                       .stop()
                                       .then(function () {
                                               $('#qr-reader').hide();
                                               scanResult.html(
                                                       '<div class="card"><div class="card-body">' +
                                                               decodedText +
                                                               '</div></div>',
                                               );
                                       })
                                       .catch(function () {});
                       }
               );
	}
});
