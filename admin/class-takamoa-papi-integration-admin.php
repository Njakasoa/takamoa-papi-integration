<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://nexa.takamoa.com/
 * @since      0.0.1
 * @package    Takamoa
 * @subpackage takamoa-papi-integration/admin
 */

class Takamoa_Papi_Integration_Admin
{
	private $plugin_name;
	private $version;
	private $functions;

	public function __construct($plugin_name, $version, $functions)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = $functions;
	}

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue_styles()
	{
		if (strpos(get_current_screen()->id, $this->plugin_name) !== false) {
			wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
			wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/takamoa-papi-integration-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts()
	{
		if (strpos(get_current_screen()->id, $this->plugin_name) !== false) {
			wp_enqueue_script('datatables-script', 'https://cdn.datatables.net/2.0.8/js/dataTables.min.js', array('jquery'), null, true);
			wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/takamoa-papi-integration-admin.js', array('jquery', 'datatables-script'), $this->version, true);
			wp_localize_script($this->plugin_name, 'takamoaAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
			wp_enqueue_media();
		}
	}

	/**
	 * Add admin menu.
	 */
	public function add_menu()
	{
        add_menu_page(
            'Takamoa x Papi Intégration',
            'Takamoa Papi',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_admin_page'),
            'dashicons-admin-generic',
            6
        );
    
        add_submenu_page(
            $this->plugin_name,
            'Historique des paiements',
            'Paiements',
            'manage_options',
            $this->plugin_name . '-payments',
            array($this, 'display_payments_page')
        );
    
        add_submenu_page(
            $this->plugin_name,
            'Options avancées',
            'Options',
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_options_page')
        );
	}

	/**
	 * Register API key setting.
	 */
	public function register_settings()
	{
		register_setting('takamoa_papi_key_group', 'takamoa_papi_api_key');
                
        // URLs
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_success_url');
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_failure_url');

        // Valid duration
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_valid_duration');

        // Providers
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_providers');

        // Champs visibles dans le formulaire
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_optional_fields');

        // Mode test
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_test_mode');
        register_setting('takamoa_papi_settings_group', 'takamoa_papi_test_reason');

		add_settings_section(
			'takamoa_papi_main_section',
			'Clé API Papi.mg',
			function () {
				echo '<p>Collez ici votre clé API Papi.mg (retrouvable dans votre espace boutique).</p>';
			},
			$this->plugin_name
		);

		add_settings_field(
			'takamoa_papi_api_key',
			'Clé API',
			function () {
				$value = esc_attr(get_option('takamoa_papi_api_key', ''));
				echo '<input type="text" name="takamoa_papi_api_key" value="' . $value . '" style="width: 400px;" />';
			},
			$this->plugin_name,
			'takamoa_papi_main_section'
		);

        add_settings_section(
            'takamoa_papi_extra_section',
            'Options supplémentaires',
            null,
            $this->plugin_name . '-settings'
        );
        
        // Champs de redirection
        add_settings_field('takamoa_papi_success_url', 'URL après succès', function () {
            $default = home_url('/paiementreussi');
            $value = esc_attr(get_option('takamoa_papi_success_url', $default));
            echo "<input type='url' name='takamoa_papi_success_url' value='$value' style='width: 400px;'>";
            echo "<p class='description'>Par défaut : <code>$default</code></p>";
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        add_settings_field('takamoa_papi_failure_url', 'URL après échec', function () {
            $default = home_url('/paiementechoue');
            $value = esc_attr(get_option('takamoa_papi_failure_url', $default));
            echo "<input type='url' name='takamoa_papi_failure_url' value='$value' style='width: 400px;'>";
            echo "<p class='description'>Par défaut : <code>$default</code></p>";
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        // Durée de validité
        add_settings_field('takamoa_papi_valid_duration', 'Durée de validité du lien (en minutes)', function () {
            echo '<input type="number" name="takamoa_papi_valid_duration" value="' . esc_attr(get_option('takamoa_papi_valid_duration', 60)) . '" min="1">';
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        // Méthodes de paiement
        add_settings_field('takamoa_papi_providers', 'Méthodes de paiement disponibles', function () {
            $active = (array) get_option('takamoa_papi_providers', []);
            $providers = ['MVOLA' => 'MVOLA', 'ORANGE_MONEY' => 'Orange Money', 'AIRTEL_MONEY' => 'Airtel Money', 'BRED' => 'BRED'];
            foreach ($providers as $key => $label) {
                $checked = in_array($key, $active) ? 'checked' : '';
                echo "<label><input type='checkbox' name='takamoa_papi_providers[]' value='$key' $checked> $label</label><br>";
            }
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        // Champs visibles dans le formulaire
        add_settings_field('takamoa_papi_optional_fields', 'Champs à afficher dans le formulaire', function () {
            $fields = ['payerEmail' => 'Email client', 'payerPhone' => 'Téléphone client'];
            $selected = (array) get_option('takamoa_papi_optional_fields', []);
            foreach ($fields as $key => $label) {
                $checked = in_array($key, $selected) ? 'checked' : '';
                echo "<label><input type='checkbox' name='takamoa_papi_optional_fields[]' value='$key' $checked> $label</label><br>";
            }
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        // Test mode
        add_settings_field('takamoa_papi_test_mode', 'Mode test (transactions réelles)', function () {
            $checked = checked(get_option('takamoa_papi_test_mode', false), true, false);
            echo "<input type='checkbox' name='takamoa_papi_test_mode' value='1' $checked> Activer le test mode";
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

        add_settings_field('takamoa_papi_test_reason', 'Raison du test', function () {
            echo '<input type="text" name="takamoa_papi_test_reason" value="' . esc_attr(get_option('takamoa_papi_test_reason')) . '" style="width: 400px;">';
        }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');
        
	}

	/**
	 * Display admin config page.
	 */
	public function display_admin_page()
	{
		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form method="post" action="options.php">
                <?php
                settings_fields('takamoa_papi_key_group');
                do_settings_sections($this->plugin_name); // clé API
                submit_button();
                ?>
            </form>
		</div>
		<?php
	}

	public function init_admin_route() {
		// Optionnel : peut être utilisé plus tard pour des routes personnalisées
	}

    public function display_payments_page()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'takamoa_papi_payments';
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1>Historique des paiements</h1>
            <table id="takamoa-payments-table" class="widefat striped">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Montant</th>
                        <th>Status</th>
                        <th>Méthode</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $row): ?>
                    <tr class="payment-row"
                        data-reference="<?= esc_attr($row->reference) ?>"
                        data-client="<?= esc_attr($row->client_name) ?>"
                        data-email="<?= esc_attr($row->payer_email) ?>"
                        data-phone="<?= esc_attr($row->payer_phone) ?>"
                        data-amount="<?= esc_attr(number_format($row->amount, 0, '', ' ') . ' MGA') ?>"
                        data-status="<?= esc_attr($row->payment_status) ?>"
                        data-method="<?= esc_attr($row->payment_method ?: '—') ?>"
                        data-date="<?= esc_attr($row->created_at) ?>"
                        data-description="<?= esc_attr($row->description) ?>"
                        data-provider="<?= esc_attr($row->provider) ?>"
                        data-success-url="<?= esc_attr($row->success_url) ?>"
                        data-failure-url="<?= esc_attr($row->failure_url) ?>"
                        data-notification-url="<?= esc_attr($row->notification_url) ?>"
                        data-link-creation="<?= esc_attr($row->link_creation) ?>"
                        data-link-expiration="<?= esc_attr($row->link_expiration) ?>"
                        data-payment-link="<?= esc_attr($row->payment_link) ?>"
                        data-currency="<?= esc_attr($row->currency) ?>"
                        data-fee="<?= esc_attr($row->fee) ?>"
                        data-notification-token="<?= esc_attr($row->notification_token) ?>"
                        data-is-test-mode="<?= esc_attr($row->is_test_mode) ?>"
                        data-test-reason="<?= esc_attr($row->test_reason) ?>"
                        data-raw-request="<?= esc_attr($row->raw_request) ?>"
                        data-raw-response="<?= esc_attr($row->raw_response) ?>"
                        data-raw-notification="<?= esc_attr($row->raw_notification) ?>"
                        data-updated-at="<?= esc_attr($row->updated_at) ?>"
                        data-id="<?= esc_attr($row->id) ?>">
                        <td><?= esc_html($row->reference) ?></td>
                        <td><?= esc_html($row->client_name) ?></td>
                        <td><?= esc_html($row->payer_email ?: '—') ?></td>
                        <td><?= esc_html($row->payer_phone ?: '—') ?></td>
                        <td><?= number_format($row->amount, 0, '', ' ') ?> MGA</td>
                        <td><?= esc_html($row->payment_status) ?></td>
                        <td><?= esc_html($row->payment_method ?: '—') ?></td>
                        <td><?= esc_html($row->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Détails du paiement</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody id="modal-basic-info">
                                        <tr><th>ID</th><td id="modal-id"></td></tr>
                                        <tr><th>Référence</th><td id="modal-reference"></td></tr>
                                        <tr><th>Nom client</th><td id="modal-name"></td></tr>
                                        <tr><th>Email</th><td id="modal-email"></td></tr>
                                        <tr><th>Téléphone</th><td id="modal-phone"></td></tr>
                                        <tr><th>Montant</th><td id="modal-amount"></td></tr>
                                        <tr><th>Status</th><td id="modal-status"></td></tr>
                                        <tr><th>Méthode</th><td id="modal-method"></td></tr>
                                        <tr><th>Date</th><td id="modal-date"></td></tr>
                                        <tr><th>Description</th><td id="modal-description"></td></tr>
                                    </tbody>
                                    <tbody id="modal-extra-info" class="d-none">
                                        <tr><th>Provider</th><td id="modal-provider"></td></tr>
                                        <tr><th>Success URL</th><td id="modal-success-url"></td></tr>
                                        <tr><th>Failure URL</th><td id="modal-failure-url"></td></tr>
                                        <tr><th>Notification URL</th><td id="modal-notification-url"></td></tr>
                                        <tr><th>Link creation</th><td id="modal-link-creation"></td></tr>
                                        <tr><th>Link expiration</th><td id="modal-link-expiration"></td></tr>
                                        <tr><th>Payment link</th><td id="modal-payment-link"></td></tr>
                                        <tr><th>Currency</th><td id="modal-currency"></td></tr>
                                        <tr><th>Fee</th><td id="modal-fee"></td></tr>
                                        <tr><th>Notification token</th><td id="modal-notification-token"></td></tr>
                                        <tr><th>Test mode</th><td id="modal-test-mode"></td></tr>
                                        <tr><th>Test reason</th><td id="modal-test-reason"></td></tr>
                                        <tr><th>Raw request</th><td><pre id="modal-raw-request" class="mb-0"></pre></td></tr>
                                        <tr><th>Raw response</th><td><pre id="modal-raw-response" class="mb-0"></pre></td></tr>
                                        <tr><th>Raw notification</th><td><pre id="modal-raw-notification" class="mb-0"></pre></td></tr>
                                        <tr><th>Updated at</th><td id="modal-updated-at"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" id="toggle-more-info">Show more</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function display_options_page()
    {
        ?>
        <div class="wrap">
            <h1>Options avancées</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('takamoa_papi_settings_group');
                do_settings_sections($this->plugin_name . '-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }


}
