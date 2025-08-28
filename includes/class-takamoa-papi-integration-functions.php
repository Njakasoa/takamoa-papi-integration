<?php

/**
	* Fired during plugin activation
	*
	* @link       https://nexa.takamoa.com/
	* @since      0.0.1
	*
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	*/

/**
	* Fired during plugin activation. https://regex101.com/r/TvKR9I/1
	*
	* This class defines all code necessary to run during the plugin"s activation.
	*
	* @since      0.0.1
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	* @author     Nexa by Takamoa <nexa.takamoa@gmail.com>
	*/
class Takamoa_Papi_Integration_Functions
{
	private function send_registration_email($email, $name, $link)
	{
		if (empty($email)) {
			return;
		}

		$subject = 'Confirmation d\'inscription et modalités de paiement';

		$message = '<p>Bonjour ' . esc_html($name) . ',</p>';
		$message .=
			'<p>Nous vous confirmons que votre inscription a bien été enregistrée.</p>';
		$message .=
			'<p>Pour réserver définitivement votre place et finaliser votre paiement, veuillez cliquer sur le bouton ci-dessous :</p>';
		$message .=
			'<p><a href="' .
			esc_url($link) .
			'" style="display:inline-block;padding:10px 20px;background:#0073aa;color:#fff;text-decoration:none;">Réserver et payer</a></p>';
		$message .=
			'<p>Pour toute question ou précision, notre équipe logistique se tient à votre disposition au 034 04 105 06.</p>';
		$message .= '<p>Bien cordialement,<br>L’équipe logistique</p>';
		$logo = get_site_icon_url();
		if ($logo) {
			$logo = set_url_scheme($logo, 'https');
			$message .=
				'<p><img src="' .
				esc_url($logo) .
				'" alt="Logo" style="max-width:150px;height:auto;"></p>';
		}

		$headers = ['Content-Type: text/html; charset=UTF-8'];
wp_mail($email, $subject, $message, $headers);
}

	private function send_ticket_email($email, $name, $file)
	{
	if (empty($email) || !file_exists($file)) {
	return;
	}
	
	$subject = 'Votre billet';
	
	$message = '<p>Bonjour ' . esc_html($name) . ',</p>';
	$message .= '<p>Veuillez trouver votre billet en pièce jointe.</p>';
	$message .= '<p>Pour toute question ou précision, notre équipe logistique se tient à votre disposition au 034 04 105 06.</p>';
	$message .= '<p>Bien cordialement,<br>L’équipe logistique</p>';
	$logo = get_site_icon_url();
	if ($logo) {
	$logo = set_url_scheme($logo, 'https');
	$message .= '<p><img src="' . esc_url($logo) . '" alt="Logo" style="max-width:150px;height:auto;"></p>';
	}
	
	$headers = ['Content-Type: text/html; charset=UTF-8'];
	wp_mail($email, $subject, $message, $headers, [$file]);
	}

        private function send_payment_success_email($email, $name)
        {
                if (empty($email)) {
                        return;
                }

                $subject = 'Confirmation de paiement';

                $message = '<p>Bonjour ' . esc_html($name) . ',</p>';
                $message .=
                        '<p>Nous vous confirmons que votre paiement a bien été reçu. Merci pour votre inscription.</p>';
                $message .=
                        '<p>Pour toute question ou précision, notre équipe logistique se tient à votre disposition au 034 04 105 06.</p>';
                $message .= '<p>Bien cordialement,<br>L’équipe logistique</p>';
                $logo = get_site_icon_url();
                if ($logo) {
                        $logo = set_url_scheme($logo, 'https');
                        $message .=
                                '<p><img src="' .
                                esc_url($logo) .
                                '" alt="Logo" style="max-width:150px;height:auto;"></p>';
                }

                $headers = ['Content-Type: text/html; charset=UTF-8'];
                wp_mail($email, $subject, $message, $headers);
        }

        private function generate_ticket_pdf($reference, $design_id)
        {
                global $wpdb;

                $design_table = $wpdb->prefix . 'takamoa_papi_designs';
                $design = $wpdb->get_row(
                        $wpdb->prepare(
                                'SELECT * FROM ' . $design_table . ' WHERE id = %d',
                                $design_id,
                        ),
                );

                if (!$design) {
                        return false;
                }

                $upload = wp_upload_dir();
                $dir = trailingslashit($upload['basedir']) . 'takamoa';
                if (!file_exists($dir)) {
                        wp_mkdir_p($dir);
                }

                require_once plugin_dir_path(__FILE__) . 'lib/fpdf.php';

                $qr_px = (int) $design->qrcode_size;
                $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $qr_px . 'x' . $qr_px . '&data=' . urlencode($reference);
               $response = wp_remote_get($qr_url, [
                       'timeout' => 30,
               ]);
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                        return false;
                }
                $qr_path = $dir . '/qr-' . $reference . '.png';
                file_put_contents($qr_path, wp_remote_retrieve_body($response));

                $design_path = str_replace($upload['baseurl'], $upload['basedir'], $design->image_url);
                $file = $dir . '/billet-' . $reference . '.pdf';

                $px_to_mm = static function($px, $dpi = 300) {
                        return ($px * 25.4) / $dpi;
                };
                $dpi = 300;
                $w_px = (int) $design->ticket_width;
                $h_px = (int) $design->ticket_height;
                $top_px = (int) $design->qrcode_top;
                $left_px = (int) $design->qrcode_left;

                $w_mm = $px_to_mm($w_px, $dpi);
                $h_mm = $px_to_mm($h_px, $dpi);
                $qr_mm = $px_to_mm($qr_px, $dpi);
                $top_mm = $px_to_mm($top_px, $dpi);
                $left_mm = $px_to_mm($left_px, $dpi);

                $orientation = ($w_mm > $h_mm) ? 'L' : 'P';
                $pdf = new \FPDF($orientation, 'mm', [$w_mm, $h_mm]);
                $pdf->SetAutoPageBreak(false);
                $pdf->AddPage($orientation, [$w_mm, $h_mm]);
                $pdf->Image($design_path, 0, 0, $w_mm, $h_mm);
                $pdf->Image($qr_path, $left_mm, $top_mm, $qr_mm, $qr_mm);
                $pdf->Output('F', $file);
                @unlink($qr_path);

                $url = trailingslashit($upload['baseurl']) . 'takamoa/billet-' . $reference . '.pdf';

                $tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';
                $ticket = $wpdb->get_row(
                        $wpdb->prepare(
                                'SELECT id FROM ' . $tickets_table . ' WHERE reference = %s',
                                $reference,
                        ),
                );
                if ($ticket) {
                        $wpdb->update(
                                $tickets_table,
                                [
                                        'qrcode_link' => $url,
                                        'status' => 'GENERATED',
                                        'updated_at' => current_time('mysql'),
                                ],
                                ['id' => $ticket->id],
                        );
                } else {
                        $wpdb->insert($tickets_table, [
                                'reference' => $reference,
                                'qrcode_link' => $url,
                                'status' => 'GENERATED',
                                'created_at' => current_time('mysql'),
                                'updated_at' => current_time('mysql'),
                        ]);
                }

                return $url;
        }

        private function send_ticket_email_by_reference($reference)
        {
                global $wpdb;

                $tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';
                $payments_table = $wpdb->prefix . 'takamoa_papi_payments';

                $ticket = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT t.qrcode_link, p.client_name, p.payer_email FROM {$tickets_table} t JOIN {$payments_table} p ON t.reference = p.reference WHERE t.reference = %s LIMIT 1",
                                $reference,
                        ),
                );

                if (!$ticket || empty($ticket->payer_email) || empty($ticket->qrcode_link)) {
                        return false;
                }

                $upload = wp_upload_dir();
                $file = str_replace($upload['baseurl'], $upload['basedir'], $ticket->qrcode_link);
                if (!file_exists($file)) {
                        return false;
                }

                $this->send_ticket_email($ticket->payer_email, $ticket->client_name, $file);
                $wpdb->update($tickets_table, ['last_notification' => current_time('mysql')], ['reference' => $reference]);

                return true;
        }

	public function register_endpoints()
	{
		add_rewrite_endpoint('paiementreussi', EP_ROOT);
		add_rewrite_endpoint('paiementechoue', EP_ROOT);
		add_rewrite_endpoint('papi-notify', EP_ROOT);
	}

	public function register_query_vars($vars)
	{
		$vars[] = 'paiementreussi';
		$vars[] = 'paiementechoue';
		$vars[] = 'papi-notify';
		return $vars;
	}

	public function handle_endpoints()
	{
		global $wp_query;

		if (isset($wp_query->query_vars['paiementreussi'])) {
			wp_die(
				'<h1>Paiement réussi</h1><p>Merci pour votre transaction.</p>',
				'Paiement validé',
			);
		}

		if (isset($wp_query->query_vars['paiementechoue'])) {
			wp_die(
				'<h1>Paiement échoué</h1><p>Le paiement a été annulé ou échoué.</p>',
				'Paiement échoué',
			);
		}

		if (
			isset($wp_query->query_vars['papi-notify']) &&
			$_SERVER['REQUEST_METHOD'] === 'POST'
		) {
			$this->handle_notification();
			exit();
		}
	}

	public function handle_notification()
	{
		$body = json_decode(file_get_contents('php://input'), true);

		if (
			!$body ||
			!isset($body['merchantPaymentReference'], $body['notificationToken'])
		) {
			status_header(400);
			echo json_encode(['error' => 'Requête invalide']);
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'takamoa_papi_payments';

		$reference = sanitize_text_field($body['merchantPaymentReference']);
		$token = sanitize_text_field($body['notificationToken']);

		$payment = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . $table . ' WHERE reference = %s AND notification_token = %s LIMIT 1',
				$reference,
				$token,
			),
		);

		if (!$payment) {
			status_header(404);
			echo json_encode(['error' => 'Paiement introuvable']);
			return;
		}

		$status = sanitize_text_field($body['paymentStatus']);

		$wpdb->update(
			$table,
			[
				'payment_status' => $status,
				'payment_method' => sanitize_text_field($body['paymentMethod']),
				'currency' => sanitize_text_field($body['currency']),
				'fee' => floatval($body['fee']),
				'raw_notification' => json_encode($body),
				'updated_at' => current_time('mysql'),
			],
			['id' => $payment->id],
		);

                if ($status === 'SUCCESS') {
                        $this->send_payment_success_email(
                                $payment->payer_email,
                                $payment->client_name,
                        );

                        $default_design = intval(get_option('takamoa_papi_default_design'));
                        $design_id = !empty($payment->design_id) ? (int) $payment->design_id : $default_design;

                        if (!empty($design_id)) {
                                $url = $this->generate_ticket_pdf($payment->reference, $design_id);
                                if ($url) {
                                        $this->send_ticket_email_by_reference($payment->reference);
                                }
                        }
                }

		status_header(200);
		echo json_encode(['success' => true]);
	}

	public function handle_create_payment_ajax()
	{
                check_ajax_referer('takamoa_papi_nonce');

                global $wpdb;

                // Vérifie les données
               $clientName = sanitize_text_field(wp_unslash($_POST['clientName'] ?? ''));
               $amount = floatval($_POST['amount'] ?? 0);
               $reference = sanitize_text_field(wp_unslash($_POST['reference'] ?? ''));
               $payerEmail = sanitize_email(wp_unslash($_POST['payerEmail'] ?? ''));
               $payerPhone = sanitize_text_field(wp_unslash($_POST['payerPhone'] ?? ''));
               $description = sanitize_text_field(wp_unslash($_POST['description'] ?? ''));
               $provider = sanitize_text_field(wp_unslash($_POST['provider'] ?? ''));
                $design_id = intval($_POST['design_id'] ?? 0);
                $designs_table = $wpdb->prefix . 'takamoa_papi_designs';
                if ($design_id > 0) {
                        $exists = $wpdb->get_var(
                                $wpdb->prepare(
                                        'SELECT id FROM ' . $designs_table . ' WHERE id = %d',
                                        $design_id,
                                ),
                        );
                        $design_id = $exists ? $design_id : null;
                } else {
                        $default_design = intval(get_option('takamoa_papi_default_design'));
                        if ($default_design > 0) {
                                $exists = $wpdb->get_var(
                                        $wpdb->prepare(
                                                'SELECT id FROM ' . $designs_table . ' WHERE id = %d',
                                                $default_design,
                                        ),
                                );
                                $design_id = $exists ? $default_design : null;
                        } else {
                                $design_id = null;
                        }
                }

		if ($amount < 300 || !$clientName || !$reference) {
			wp_send_json_error([
				'message' => 'Champs requis manquants ou invalides.',
			]);
		}

		// Récupère les options admin
		$api_key = get_option('takamoa_papi_api_key');
		$successUrl = get_option(
			'takamoa_papi_success_url',
			home_url('/paiementreussi'),
		);
		$failureUrl = get_option(
			'takamoa_papi_failure_url',
			home_url('/paiementechoue'),
		);
		$notificationUrl = home_url('/papi-notify');
		$validDuration = intval(get_option('takamoa_papi_valid_duration', 60));
		$isTestMode = (bool) get_option('takamoa_papi_test_mode', false);
		$testReason = sanitize_text_field(
			get_option('takamoa_papi_test_reason', ''),
		);

		// Construction du corps de la requête
		$request = [
			'clientName' => $clientName,
			'amount' => $amount,
			'reference' => $reference,
			'description' => $description ?: 'Paiement via Papi',
			'payerEmail' => $payerEmail,
			'payerPhone' => $payerPhone,
			'notificationUrl' => $notificationUrl,
			'validDuration' => $validDuration,
			'isTestMode' => $isTestMode,
			'testReason' => $isTestMode ? $testReason : '',
		];

		if (!empty($provider)) {
			$request['provider'] = $provider;
		}
		if (!empty($successUrl)) {
			$request['successUrl'] = $successUrl;
		}
		if (!empty($failureUrl)) {
			$request['failureUrl'] = $failureUrl;
		}

               $response = wp_remote_post(
                       'https://app.papi.mg/dashboard/api/payment-links',
                       [
                               'headers' => [
                                       'Token' => $api_key,
                                       'Content-Type' => 'application/json',
                               ],
                               'body' => json_encode($request),
                               'timeout' => 30,
                       ],
               );

		if (is_wp_error($response)) {
			wp_send_json_error(['message' => 'Erreur de connexion à Papi.']);
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!isset($body['data']['paymentLink'])) {
			wp_send_json_error([
				'message' => $body['error']['message'] ?? 'Erreur inconnue.',
			]);
		}

		$link = esc_url($body['data']['paymentLink']);

                // Sauvegarde dans la base
                $table = $wpdb->prefix . 'takamoa_papi_payments';
		$payment_method = !empty($provider) ? $provider : '—';
                $wpdb->insert($table, [
                        'reference' => $reference,
                        'client_name' => $clientName,
                        'amount' => $amount,
                        'description' => $request['description'],
                        'payer_email' => $payerEmail,
                        'payer_phone' => $payerPhone,
                        'provider' => $provider,
                        'success_url' => $successUrl,
                        'failure_url' => $failureUrl,
                        'notification_url' => $notificationUrl,
                        'link_creation' => current_time('mysql'),
                        'payment_link' => $link,
                        'payment_status' => 'PENDING',
                        'payment_method' => $payment_method,
                        'notification_token' => $body['data']['notificationToken'] ?? '',
                        'is_test_mode' => $isTestMode,
                        'test_reason' => $testReason,
                        'raw_request' => json_encode($request),
                        'raw_response' => json_encode($body),
                        'design_id' => $design_id,
                ]);

		if ($payerEmail) {
			$this->send_registration_email($payerEmail, $clientName, $link);
		}

		wp_send_json_success(['link' => $link]);
	}

	public function handle_check_payment_status_ajax()
	{
		check_ajax_referer('takamoa_papi_nonce');

		$reference = sanitize_text_field($_POST['reference'] ?? '');

		if (!$reference) {
			wp_send_json_error(['message' => 'Référence manquante.']);
		}

		global $wpdb;
		$table = $wpdb->prefix . 'takamoa_papi_payments';

		$status = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT payment_status FROM ' . $table . ' WHERE reference = %s LIMIT 1',
				$reference,
			),
		);

		if (!$status) {
			wp_send_json_error(['message' => 'Paiement introuvable.']);
		}

		wp_send_json_success(['status' => $status]);
	}

	public function handle_resend_payment_email_ajax()
	{
		check_ajax_referer('takamoa_papi_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Unauthorized'], 403);
		}

		$reference = sanitize_text_field($_POST['reference'] ?? '');
		if (!$reference) {
			wp_send_json_error(['message' => 'Référence manquante.']);
		}

		global $wpdb;
		$table = $wpdb->prefix . 'takamoa_papi_payments';
		$payment = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT client_name, payer_email, payment_link FROM ' . $table . ' WHERE reference = %s LIMIT 1',
				$reference,
			),
		);

		if (!$payment || empty($payment->payer_email)) {
			wp_send_json_error(['message' => 'Paiement introuvable.']);
		}

		$this->send_registration_email(
			$payment->payer_email,
			$payment->client_name,
			$payment->payment_link,
		);

               wp_send_json_success(['message' => 'Notification envoyée.']);
       }

       public function handle_regenerate_payment_link_ajax()
       {
               check_ajax_referer('takamoa_papi_nonce', 'nonce');

               if (!current_user_can('manage_options')) {
                       wp_send_json_error(['message' => 'Unauthorized'], 403);
               }

               $reference = sanitize_text_field($_POST['reference'] ?? '');
               if (!$reference) {
                       wp_send_json_error(['message' => 'Référence manquante.']);
               }

               global $wpdb;
               $table = $wpdb->prefix . 'takamoa_papi_payments';
               $payment = $wpdb->get_row(
                       $wpdb->prepare(
                               'SELECT * FROM ' . $table . ' WHERE reference = %s LIMIT 1',
                               $reference,
                       ),
               );

               if (!$payment) {
                       wp_send_json_error(['message' => 'Paiement introuvable.']);
               }

               $api_key = get_option('takamoa_papi_api_key');
               $validDuration = intval(get_option('takamoa_papi_valid_duration', 60));

               $request = [
                       'clientName' => $payment->client_name,
                       'amount' => floatval($payment->amount),
                       'reference' => $payment->reference,
                       'description' => $payment->description ?: 'Paiement via Papi',
                       'payerEmail' => $payment->payer_email,
                       'payerPhone' => $payment->payer_phone,
                       'notificationUrl' => $payment->notification_url,
                       'validDuration' => $validDuration,
                       'isTestMode' => (bool) $payment->is_test_mode,
                       'testReason' => $payment->is_test_mode ? $payment->test_reason : '',
               ];

               if (!empty($payment->provider)) {
                       $request['provider'] = $payment->provider;
               }
               if (!empty($payment->success_url)) {
                       $request['successUrl'] = $payment->success_url;
               }
               if (!empty($payment->failure_url)) {
                       $request['failureUrl'] = $payment->failure_url;
               }

               $response = wp_remote_post(
                       'https://app.papi.mg/dashboard/api/payment-links',
                       [
                               'headers' => [
                                       'Token' => $api_key,
                                       'Content-Type' => 'application/json',
                               ],
                               'body' => json_encode($request),
                               'timeout' => 30,
                       ],
               );

               if (is_wp_error($response)) {
                       wp_send_json_error(['message' => 'Erreur de connexion à Papi.']);
               }

               $body = json_decode(wp_remote_retrieve_body($response), true);

               if (!isset($body['data']['paymentLink'])) {
                       wp_send_json_error([
                               'message' => $body['error']['message'] ?? 'Erreur inconnue.',
                       ]);
               }

               $link = esc_url($body['data']['paymentLink']);
               $now = current_time('mysql');
               $link_expiration = !empty($body['data']['linkExpiration']) ? date('Y-m-d H:i:s', strtotime($body['data']['linkExpiration'])) : null;
               $notification_token = $body['data']['notificationToken'] ?? '';
               $payment_method = !empty($payment->provider) ? $payment->provider : '—';

               $wpdb->update(
                       $table,
                       [
                               'payment_link' => $link,
                               'link_creation' => $now,
                               'link_expiration' => $link_expiration,
                               'notification_token' => $notification_token,
                               'payment_status' => 'PENDING',
                               'payment_method' => $payment_method,
                               'raw_request' => json_encode($request),
                               'raw_response' => json_encode($body),
                               'updated_at' => $now,
                       ],
                       ['id' => $payment->id],
               );

               wp_send_json_success([
                       'payment_link' => $link,
                       'link_creation' => $now,
                       'link_expiration' => $link_expiration,
                       'notification_token' => $notification_token,
                       'raw_request' => json_encode($request),
                       'raw_response' => json_encode($body),
                       'payment_method' => $payment_method,
                       'updated_at' => $now,
                       'status' => 'PENDING',
               ]);
       }

       /**
       * AJAX handler to check if a ticket already exists for a reference.
	*
	* @since 0.0.7
	*/
	public function handle_ticket_exists_ajax()
	{
		check_ajax_referer('takamoa_papi_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Unauthorized'], 403);
		}

		$reference = sanitize_text_field($_POST['reference'] ?? '');
		if (!$reference) {
			wp_send_json_error(['message' => 'Données manquantes.']);
		}

		global $wpdb;
		$tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';
		$exists = $wpdb->get_var(
			$wpdb->prepare(
			'SELECT id FROM ' . $tickets_table . ' WHERE reference = %s',
			$reference,
			),
		);

		wp_send_json_success(['exists' => (bool) $exists]);
	}

	/**
	* AJAX handler to generate a ticket PDF from a design.
	*
	* @since 0.0.3
	*/
        public function handle_generate_ticket_ajax()
        {
                check_ajax_referer('takamoa_papi_nonce', 'nonce');

                if (!current_user_can('manage_options')) {
                        wp_send_json_error(['message' => 'Unauthorized'], 403);
                }

                $reference = sanitize_text_field($_POST['reference'] ?? '');
                $design_id = intval($_POST['design_id'] ?? 0);
                if (!$design_id) {
                        $default_design = intval(get_option('takamoa_papi_default_design'));
                        $design_id = $default_design;
                }
                if (!$reference || !$design_id) {
                        wp_send_json_error(['message' => 'Données manquantes.']);
                }

                $url = $this->generate_ticket_pdf($reference, $design_id);
                if (!$url) {
                        wp_send_json_error(['message' => 'Erreur génération billet.']);
                }

                wp_send_json_success(['url' => $url]);
        }
	/**
	* AJAX handler to verify a ticket reference and return ticket info.
	*
	* @since 0.0.5
	*/
	public function handle_scan_ticket_ajax()
	{
			check_ajax_referer('takamoa_papi_nonce', 'nonce');

			$reference = sanitize_text_field($_POST['reference'] ?? '');
			if (!$reference) {
					wp_send_json_error(['message' => 'Référence manquante.']);
			}

			global $wpdb;
			$tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';
			$payments_table = $wpdb->prefix . 'takamoa_papi_payments';

		$ticket = $wpdb->get_row(
		$wpdb->prepare(
		"SELECT p.client_name, p.payer_email, p.payer_phone, p.description, t.status
		FROM {$tickets_table} t
		JOIN {$payments_table} p ON t.reference = p.reference
		WHERE t.reference = %s",
		$reference
		)
		);
		
		if ($ticket) {
		wp_send_json_success([
		'name' => $ticket->client_name,
		'email' => $ticket->payer_email,
		'phone' => $ticket->payer_phone,
		'description' => $ticket->description,
		'status' => $ticket->status,
		]);
		}
		
		wp_send_json_error(['message' => 'Billet introuvable.']);
		}
	
	/**
	* AJAX handler to validate a ticket.
	*
	* @since 0.0.6
	*/
	public function handle_validate_ticket_ajax()
	{
		check_ajax_referer('takamoa_papi_nonce', 'nonce');
		
		$reference = sanitize_text_field($_POST['reference'] ?? '');
		if (!$reference) {
			wp_send_json_error(['message' => 'Référence manquante.']);
		}
		
		global $wpdb;
		$tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';
		
		$ticket = $wpdb->get_row(
		$wpdb->prepare(
		"SELECT status FROM {$tickets_table} WHERE reference = %s",
		$reference
		)
		);
		
		if (!$ticket) {
			wp_send_json_error(['message' => 'Billet introuvable.']);
		}
		
		if ($ticket->status === 'VALIDATED') {
			wp_send_json_error(['message' => 'Billet déjà validé.']);
		}
		
		$updated = $wpdb->update($tickets_table, ['status' => 'VALIDATED'], ['reference' => $reference]);
		if ($updated !== false) {
			wp_send_json_success(['status' => 'VALIDATED']);
		}
		
	wp_send_json_error(['message' => 'Erreur lors de la mise à jour.']);
	}
	
        public function handle_send_ticket_email_ajax()
        {
                check_ajax_referer('takamoa_papi_nonce', 'nonce');

                if (!current_user_can('manage_options')) {
                        wp_send_json_error(['message' => 'Unauthorized'], 403);
                }

                $reference = sanitize_text_field($_POST['reference'] ?? '');
                if (!$reference) {
                        wp_send_json_error(['message' => 'Référence manquante.']);
                }

                if (!$this->send_ticket_email_by_reference($reference)) {
                        wp_send_json_error(['message' => 'Billet introuvable.']);
                }

                wp_send_json_success(['message' => 'Billet envoyé.']);
        }
}
