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

                        $deps = array('jquery', 'datatables-script', 'bootstrap-js');
                        if (isset($_GET['page']) && $_GET['page'] === $this->plugin_name . '-scanner') {
                                wp_enqueue_script('html5-qrcode', 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js', array(), null, true);
                                $deps[] = 'html5-qrcode';
                        }

                        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/takamoa-papi-integration-admin.js', $deps, $this->version, true);
                        wp_localize_script($this->plugin_name, 'takamoaAjax', array(
                                        'ajaxurl' => admin_url('admin-ajax.php'),
                                        'nonce'   => wp_create_nonce('takamoa_papi_nonce'),
                                        'ticketsPage' => admin_url('admin.php?page=' . $this->plugin_name . '-tickets'),
                                ));
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
					'Billets',
					'Billets',
					'manage_options',
					$this->plugin_name . '-tickets',
					array($this, 'display_tickets_page')
				);

				add_submenu_page(
					$this->plugin_name,
					'Design des billets',
					'Design',
					'manage_options',
					$this->plugin_name . '-design',
					array($this, 'display_designs_page')
				);
                                       add_submenu_page(
                                       $this->plugin_name,
                                       'Scanner billets',
                                       'Scanner billets',
                                       'manage_options',
                                       $this->plugin_name . '-scanner',
                                       array($this, 'display_scanner_page')
                                       ); // @since 0.0.5


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
					echo '<input type="url" name="takamoa_papi_success_url" value="' . $value . '" style="width: 400px;">';
					echo '<p class="description">Par défaut : <code>' . $default . '</code></p>';
		}, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

		add_settings_field('takamoa_papi_failure_url', 'URL après échec', function () {
			$default = home_url('/paiementechoue');
			$value = esc_attr(get_option('takamoa_papi_failure_url', $default));
					echo '<input type="url" name="takamoa_papi_failure_url" value="' . $value . '" style="width: 400px;">';
					echo '<p class="description">Par défaut : <code>' . $default . '</code></p>';
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
						echo '<label><input type="checkbox" name="takamoa_papi_providers[]" value="' . $key . '" ' . $checked . '> ' . $label . '</label><br>';
			}
		}, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

		// Champs visibles dans le formulaire
		add_settings_field('takamoa_papi_optional_fields', 'Champs à afficher dans le formulaire', function () {
			$fields = ['payerEmail' => 'Email client', 'payerPhone' => 'Téléphone client'];
			$selected = (array) get_option('takamoa_papi_optional_fields', []);
			foreach ($fields as $key => $label) {
				$checked = in_array($key, $selected) ? 'checked' : '';
						echo '<label><input type="checkbox" name="takamoa_papi_optional_fields[]" value="' . $key . '" ' . $checked . '> ' . $label . '</label><br>';
			}
		}, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

		// Test mode
		add_settings_field('takamoa_papi_test_mode', 'Mode test (transactions réelles)', function () {
			$checked = checked(get_option('takamoa_papi_test_mode', false), true, false);
					echo '<input type="checkbox" name="takamoa_papi_test_mode" value="1" ' . $checked . '> Activer le test mode';
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
		<div class="wrap container-fluid">
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

	public function init_admin_route()
	{
		// Optionnel : peut être utilisé plus tard pour des routes personnalisées
	}

	public function display_payments_page()
	{
			global $wpdb;
			$table = $wpdb->prefix . 'takamoa_papi_payments';
				$results = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC LIMIT 100');
                        $design_table = $wpdb->prefix . 'takamoa_papi_designs';
                                $designs = $wpdb->get_results('SELECT id, title FROM ' . $design_table . ' ORDER BY created_at DESC');
		?>
		<div class="wrap container-fluid">
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
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
			<?php foreach ($results as $row) : ?>
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
					<td>
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-secondary dropdown-toggle takamoa-action-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-cog"></i>
							</button>
                                                       <ul class="dropdown-menu">
                                                               <li><button type="button" class="dropdown-item takamoa-notify">Notifier</button></li>
                                                               <li><button type="button" class="dropdown-item takamoa-regenerate-link" data-reference="<?= esc_attr($row->reference) ?>">Regénérer le lien de paiement</button></li>
                                                               <li><button type="button" class="dropdown-item takamoa-generate-ticket">Générer un billet</button></li>
                                                               <li><button type="button" class="dropdown-item takamoa-details">Détails</button></li>
                                                       </ul>
                                               </div>
                                       </td>
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
		<div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Générer un billet</h5>
						<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
                                                <select id="ticket-design" class="form-select">
                                                        <?php foreach ($designs as $d) : ?>
                                                                <option value="<?= esc_attr($d->id) ?>"><?= esc_html($d->title) ?></option>
                                                        <?php endforeach; ?>
                                                </select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
						<button type="button" id="generate-ticket-btn" class="btn btn-primary">Générer</button>
					</div>
				</div>
			</div>
		</div>
</div>
<?php
	}

	/**
	* Display the tickets management page.
	*
	* @since 0.0.3
	*/
	public function display_tickets_page()
	{
			global $wpdb;
			$table = $wpdb->prefix . 'takamoa_papi_tickets';
				$results = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC LIMIT 100');
		?>
				<div class="wrap container-fluid">
						<h1>Billets</h1>
						<table id="takamoa-tickets-table" class="widefat striped">
								<thead>
										<tr>
												<th>Référence</th>
												<th>Description</th>
												<th>QR Code</th>
												<th>Date création</th>
												<th>Date mise à jour</th>
	<th>Status</th>
	<th>Dernière notification</th>
	<th>Action</th>
											</tr>
									</thead>
									<tbody>
	<?php foreach ($results as $row) : ?>
	<tr data-reference="<?= esc_attr($row->reference) ?>">
	<td><?= esc_html($row->reference) ?></td>
	<td><?= esc_html($row->description ?: '—') ?></td>
	<td><?= $row->qrcode_link ? '<a href="' . esc_url($row->qrcode_link) . '" target="_blank">Voir</a>' : '—'; ?></td>
	<td><?= esc_html($row->created_at) ?></td>
	<td><?= esc_html($row->updated_at ?: '—') ?></td>
	<td><?= esc_html($row->status) ?></td>
	<td><?= esc_html($row->last_notification ?: '—') ?></td>
	<td>
	<div class="btn-group">
		<button type="button" class="btn btn-sm btn-secondary dropdown-toggle takamoa-action-toggle" data-bs-toggle="dropdown" aria-expanded="false">
		<i class="fa fa-cog"></i>
		</button>
		<ul class="dropdown-menu">
		<li><button type="button" class="dropdown-item takamoa-send-ticket-email">Envoyer le billet par email</button></li>
		</ul>
		</div>
		</td>
	</tr>
	<?php endforeach; ?>
								</tbody>
						</table>
				</div>
				<?php
	}

	/**
	* Display the ticket designs management page.
	*
	* @since 0.0.3
	*/
	public function display_designs_page()
	{
			global $wpdb;
			$table = $wpdb->prefix . 'takamoa_papi_designs';
				$designs = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC');
		?>
				<div class="wrap container-fluid">
						<h1>Designs de billets</h1>
                                                <form id="takamoa-add-design" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                                                <?php wp_nonce_field('takamoa_save_design'); ?>
                                                                <input type="hidden" name="action" value="takamoa_save_design">
                                                                <p>
                                                                                <label for="design_title">Titre du design</label><br>
                                                                                <input type="text" id="design_title" name="design_title" class="form-control" />
                                                                </p>
                                                                <p>
                                                                                <label for="design_image">Image du billet</label><br>
                                                                                <input type="text" id="design_image" name="design_image" class="form-control" />
                                                                                <button type="button" class="button btn btn-outline-secondary" id="select_design_image">Choisir une image</button>
                                                                </p>
                                                                <p class="flex-fields">
                                                                                <label>Largeur (px)</label>
                                                                                <input type="number" id="ticket_width" name="ticket_width" class="form-control small-input" min="1">
                                                                                <label>Hauteur (px)</label>
                                                                                <input type="number" id="ticket_height" name="ticket_height" class="form-control small-input" min="1">
                                                                </p>
                                                                <p class="flex-fields">
                                                                                <label>Taille QR Code (px)</label>
                                                                                <input type="number" id="qrcode_size" name="qrcode_size" class="form-control small-input" min="1">
                                                                                <label>Position top (px)</label>
                                                                                <input type="number" id="qrcode_top" name="qrcode_top" class="form-control small-input" min="0">
                                                                                <label>Position left (px)</label>
                                                                                <input type="number" id="qrcode_left" name="qrcode_left" class="form-control small-input" min="0">
                                                                </p>
								<?php submit_button('Ajouter'); ?>
						</form>
						<hr>
						<table class="widefat striped table table-striped align-middle">
								<thead>
                                                                                <tr>
                                                                                                <th>ID</th>
                                                                                                <th>Titre</th>
                                                                                                <th>Image</th>
                                                                                                <th>Billet (px)</th>
                                                                                                <th>Taille QR</th>
                                                                                                <th>Position QR</th>
                                                                                                <th>Date</th>
                                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                        <?php foreach ($designs as $d) : ?>
                                                                                <tr>
                                                                                                <td><?= esc_html($d->id) ?></td>
                                                                                                <td><?= esc_html($d->title) ?></td>
                                                                                                <td><?= $d->image_url ? '<img src="' . esc_url($d->image_url) . '" style="max-width:150px;height:auto;" />' : '—'; ?></td>
                                                                                                <td><?= esc_html($d->ticket_width . '×' . $d->ticket_height) ?></td>
                                                                                                <td><?= esc_html($d->qrcode_size) ?></td>
                                                                                                <td><?= esc_html($d->qrcode_left . ',' . $d->qrcode_top) ?></td>
                                                                                                <td><?= esc_html($d->created_at) ?></td>
                                                                                </tr>
                                                        <?php endforeach; ?>
                                                                </tbody>
                                                </table>
				</div>
				<?php
	}

       /**
       * Display the ticket scanner page.
       *
       * @since 0.0.5
       */
       public function display_scanner_page()
       {
                        ?>
                        <style>
			#wpadminbar,
			#adminmenumain,
			#adminmenuback,
			#adminmenuwrap {
				display: none;
			}
			.notice {
					display: none;
			}
			#wpcontent,
			#wpfooter {
				margin-left: 0;
			}
			#takamoa-scanner-home {
				position: fixed;
				top: 20px;
				left: 20px;
				z-index: 999;
			}
                        </style>
                        <div class="wrap text-center">
                                        <a href="<?= esc_url(admin_url()); ?>" class="button button-secondary" id="takamoa-scanner-home">Home</a>
                                        <h1>Scanner billets</h1>
					<div id="qr-reader"></div>
					<div id="scan-result" class="mt-3"></div>
					<button id="rescan-btn" class="button button-primary mt-3">Re-scan</button>
				</div>
<?php
}

	/**
	* Handle saving a ticket design.
	*
	* @since 0.0.3
	*/
	public function handle_save_design()
	{
			check_admin_referer('takamoa_save_design');

                        $title        = sanitize_text_field($_POST['design_title'] ?? '');
                        $image        = esc_url_raw($_POST['design_image'] ?? '');
                        $width        = intval($_POST['ticket_width'] ?? 0);
                        $height       = intval($_POST['ticket_height'] ?? 0);
                        $qrsize       = intval($_POST['qrcode_size'] ?? 0);
                        $qtop         = intval($_POST['qrcode_top'] ?? 0);
                        $qleft        = intval($_POST['qrcode_left'] ?? 0);

                        if (!$title || !$image || !$width || !$height || !$qrsize) {
                                wp_redirect(add_query_arg('error', 'missing', wp_get_referer()));
                                exit;
                        }

			$attachment_id = attachment_url_to_postid($image);
			if ($attachment_id) {
				$meta = wp_get_attachment_metadata($attachment_id);
				if ($meta && ((int) $meta['width'] !== $width || (int) $meta['height'] !== $height)) {
					wp_redirect(add_query_arg('error', 'size', wp_get_referer()));
					exit;
				}
			}

			global $wpdb;
			$table = $wpdb->prefix . 'takamoa_papi_designs';
                        $wpdb->insert($table, [
                                'title'        => $title,
                                'image_url'    => $image,
                                'ticket_width' => $width,
                                'ticket_height'=> $height,
                                'qrcode_size'  => $qrsize,
                                'qrcode_top'   => $qtop,
                                'qrcode_left'  => $qleft,
                                'created_at'   => current_time('mysql')
                        ]);

			wp_redirect(add_query_arg('success', '1', wp_get_referer()));
			exit;
	}

	public function display_options_page()
	{
		?>
				<div class="wrap container-fluid">
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
