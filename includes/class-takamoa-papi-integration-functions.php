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
class Takamoa_Papi_Integration_Functions {

	public function register_endpoints() {
		add_rewrite_endpoint('paiementreussi', EP_ROOT);
		add_rewrite_endpoint('paiementechoue', EP_ROOT);
		add_rewrite_endpoint('papi-notify', EP_ROOT);
	}

	public function register_query_vars($vars) {
		$vars[] = 'paiementreussi';
		$vars[] = 'paiementechoue';
		$vars[] = 'papi-notify';
		return $vars;
	}

	public function handle_endpoints() {
		global $wp_query;

		if (isset($wp_query->query_vars['paiementreussi'])) {
			wp_die('<h1>Paiement réussi</h1><p>Merci pour votre transaction.</p>', 'Paiement validé');
		}

		if (isset($wp_query->query_vars['paiementechoue'])) {
			wp_die('<h1>Paiement échoué</h1><p>Le paiement a été annulé ou échoué.</p>', 'Paiement échoué');
		}

		if (isset($wp_query->query_vars['papi-notify']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->handle_notification();
			exit;
		}
	}

	public function handle_notification() {
		$body = json_decode(file_get_contents('php://input'), true);

		if (!$body || !isset($body['paymentReference'], $body['notificationToken'])) {
			status_header(400);
			echo json_encode(['error' => 'Requête invalide']);
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'takamoa_papi_payments';

		$reference = sanitize_text_field($body['merchantPaymentReference']);
		$token = sanitize_text_field($body['notificationToken']);

		$payment = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table WHERE reference = %s AND notification_token = %s LIMIT 1",
			$reference, $token
		));

		if (!$payment) {
			status_header(404);
			echo json_encode(['error' => 'Paiement introuvable']);
			return;
		}

		$wpdb->update($table, [
			'payment_status'   => sanitize_text_field($body['paymentStatus']),
			'payment_method'   => sanitize_text_field($body['paymentMethod']),
			'currency'         => sanitize_text_field($body['currency']),
			'fee'              => floatval($body['fee']),
			'raw_notification' => json_encode($body),
			'updated_at'       => current_time('mysql')
		], ['id' => $payment->id]);

		status_header(200);
		echo json_encode(['success' => true]);
	}

    public function handle_create_payment_ajax() {
        check_ajax_referer('takamoa_papi_nonce');
    
        // Vérifie les données
        $clientName  = sanitize_text_field($_POST['clientName'] ?? '');
        $amount      = floatval($_POST['amount'] ?? 0);
        $reference   = sanitize_text_field($_POST['reference'] ?? '');
        $payerEmail  = sanitize_email($_POST['payerEmail'] ?? '');
        $payerPhone  = sanitize_text_field($_POST['payerPhone'] ?? '');
        $description = sanitize_text_field($_POST['description'] ?? '');
        $provider    = sanitize_text_field($_POST['provider'] ?? '');

        if ($amount < 300 || !$clientName || !$reference) {
            wp_send_json_error(['message' => 'Champs requis manquants ou invalides.']);
        }

        // Récupère les options admin
        $api_key        = get_option('takamoa_papi_api_key');
        $successUrl     = get_option('takamoa_papi_success_url', home_url('/paiementreussi'));
        $failureUrl     = get_option('takamoa_papi_failure_url', home_url('/paiementechoue'));
        $notificationUrl = home_url('/papi-notify');
        $validDuration  = intval(get_option('takamoa_papi_valid_duration', 60));
        $isTestMode     = (bool) get_option('takamoa_papi_test_mode', false);
        $testReason     = sanitize_text_field(get_option('takamoa_papi_test_reason', ''));

        // Construction du corps de la requête
        $request = [
            'clientName'      => $clientName,
            'amount'          => $amount,
            'reference'       => $reference,
            'description'     => $description ?: 'Paiement via Papi',
            'payerEmail'      => $payerEmail,
            'payerPhone'      => $payerPhone,
            'notificationUrl' => $notificationUrl,
            'validDuration'   => $validDuration,
            'isTestMode'      => $isTestMode,
            'testReason'      => $isTestMode ? $testReason : ''
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

        $response = wp_remote_post('https://app.papi.mg/dashboard/api/payment-links', [
            'headers' => [
                'Token' => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($request)
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Erreur de connexion à Papi.']);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($body['data']['paymentLink'])) {
            wp_send_json_error(['message' => $body['error']['message'] ?? 'Erreur inconnue.']);
        }

        $link = esc_url($body['data']['paymentLink']);

        // Sauvegarde dans la base
        global $wpdb;
        $table = $wpdb->prefix . 'takamoa_papi_payments';
        $payment_method = !empty($provider) ? $provider : '—';
        $wpdb->insert($table, [
            'reference'          => $reference,
            'client_name'        => $clientName,
            'amount'             => $amount,
            'description'        => $request['description'],
            'payer_email'        => $payerEmail,
            'payer_phone'        => $payerPhone,
            'provider'           => $provider,
            'success_url'        => $successUrl,
            'failure_url'        => $failureUrl,
            'notification_url'   => $notificationUrl,
            'link_creation'      => current_time('mysql'),
            'payment_link'       => $link,
            'payment_status'     => 'PENDING',
			'payment_method'     => $payment_method,
            'notification_token' => $body['data']['notificationToken'] ?? '',
            'is_test_mode'       => $isTestMode,
            'test_reason'        => $testReason,
            'raw_request'        => json_encode($request),
            'raw_response'       => json_encode($body),
        ]);
    
        wp_send_json_success(['link' => $link]);
    }

    public function handle_check_payment_status_ajax() {
        check_ajax_referer('takamoa_papi_nonce');
    
        $reference = sanitize_text_field($_POST['reference'] ?? '');
    
        if (!$reference) {
            wp_send_json_error(['message' => 'Référence manquante.']);
        }
    
        global $wpdb;
        $table = $wpdb->prefix . 'takamoa_papi_payments';
    
        $status = $wpdb->get_var($wpdb->prepare("SELECT payment_status FROM $table WHERE reference = %s LIMIT 1", $reference));
    
        if (!$status) {
            wp_send_json_error(['message' => 'Paiement introuvable.']);
        }
    
        wp_send_json_success(['status' => $status]);
    }
    
}
