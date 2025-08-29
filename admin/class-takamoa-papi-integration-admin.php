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
            $this->version     = $version;
            $this->functions   = $functions;
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
                        'ajaxurl'     => admin_url('admin-ajax.php'),
                        'nonce'       => wp_create_nonce('takamoa_papi_nonce'),
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
                    $value   = esc_attr(get_option('takamoa_papi_success_url', $default));
                    echo '<input type="url" name="takamoa_papi_success_url" value="' . $value . '" style="width: 400px;">';
                    echo '<p class="description">Par défaut : <code>' . $default . '</code></p>';
                }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

            add_settings_field('takamoa_papi_failure_url', 'URL après échec', function () {
                    $default = home_url('/paiementechoue');
                    $value   = esc_attr(get_option('takamoa_papi_failure_url', $default));
                    echo '<input type="url" name="takamoa_papi_failure_url" value="' . $value . '" style="width: 400px;">';
                    echo '<p class="description">Par défaut : <code>' . $default . '</code></p>';
                }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

            // Durée de validité
            add_settings_field('takamoa_papi_valid_duration', 'Durée de validité du lien (en minutes)', function () {
                    echo '<input type="number" name="takamoa_papi_valid_duration" value="' . esc_attr(get_option('takamoa_papi_valid_duration', 60)) . '" min="1">';
                }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

            // Méthodes de paiement
            add_settings_field('takamoa_papi_providers', 'Méthodes de paiement disponibles', function () {
                    $active    = (array) get_option('takamoa_papi_providers', []);
                    $providers = ['MVOLA' => 'MVOLA', 'ORANGE_MONEY' => 'Orange Money', 'AIRTEL_MONEY' => 'Airtel Money', 'BRED' => 'BRED'];
                    foreach ($providers as $key => $label) {
                        $checked = in_array($key, $active) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="takamoa_papi_providers[]" value="' . $key . '" ' . $checked . '> ' . $label . '</label><br>';
                    }
                }, $this->plugin_name . '-settings', 'takamoa_papi_extra_section');

            // Champs visibles dans le formulaire
            add_settings_field('takamoa_papi_optional_fields', 'Champs à afficher dans le formulaire', function () {
                    $fields   = ['payerEmail' => 'Email client', 'payerPhone' => 'Téléphone client'];
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
            require plugin_dir_path(__FILE__) . 'partials/admin-page.php';
        }

        public function init_admin_route()
        {
            // Optionnel : peut être utilisé plus tard pour des routes personnalisées
        }

        public function display_payments_page()
        {
            global $wpdb;
            $table          = $wpdb->prefix . 'takamoa_papi_payments';
            $results        = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC LIMIT 100');
            $design_table   = $wpdb->prefix . 'takamoa_papi_designs';
            $designs        = $wpdb->get_results('SELECT id, title FROM ' . $design_table . ' ORDER BY created_at DESC');
            $default_design = intval(get_option('takamoa_papi_default_design'));

            include plugin_dir_path(__FILE__) . 'partials/payments-page.php';
        }

        /**
        * Export all payments as CSV with contact information.
        *
        * @since 0.0.8
        */
        public function handle_export_payments_csv()
        {
            if (!current_user_can('manage_options')) {
                wp_die('Unauthorized', 403);
            }

            check_admin_referer('takamoa_export_payments');

            global $wpdb;
            $table = $wpdb->prefix . 'takamoa_papi_payments';
            $rows  = $wpdb->get_results("SELECT reference, client_name, payer_email, payer_phone, amount, payment_status, payment_method, created_at FROM $table ORDER BY created_at DESC", ARRAY_A);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=takamoa-payments.csv');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['Reference', 'Client Name', 'Email', 'Phone', 'Amount', 'Status', 'Method', 'Date']);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
            exit;
        }

        /**
        * Display the tickets management page.
        *
        * @since 0.0.3
        */
        public function display_tickets_page()
        {
            global $wpdb;
            $table   = $wpdb->prefix . 'takamoa_papi_tickets';
            $results = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC LIMIT 100');

            include plugin_dir_path(__FILE__) . 'partials/tickets-page.php';
        }

        /**
        * Display the ticket designs management page.
        *
        * @since 0.0.3
        */
        public function display_designs_page()
        {
            global $wpdb;
            $table          = $wpdb->prefix . 'takamoa_papi_designs';
            $designs        = $wpdb->get_results('SELECT * FROM ' . $table . ' ORDER BY created_at DESC');
            $default_design = intval(get_option('takamoa_papi_default_design'));

            include plugin_dir_path(__FILE__) . 'partials/designs-page.php';
        }

        /**
        * Display the ticket scanner page.
        *
        * @since 0.0.5
        */
        public function display_scanner_page()
        {
            include plugin_dir_path(__FILE__) . 'partials/scanner-page.php';
        }

        /**
        * Handle saving a ticket design.
        *
        * @since 0.0.3
        */
        public function handle_save_design()
        {
            check_admin_referer('takamoa_save_design');

            $title  = sanitize_text_field($_POST['design_title'] ?? '');
            $image  = esc_url_raw($_POST['design_image'] ?? '');
            $width  = intval($_POST['ticket_width'] ?? 0);
            $height = intval($_POST['ticket_height'] ?? 0);
            $qrsize = intval($_POST['qrcode_size'] ?? 0);
            $qtop   = intval($_POST['qrcode_top'] ?? 0);
            $qleft  = intval($_POST['qrcode_left'] ?? 0);

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
                    'title'         => $title,
                    'image_url'     => $image,
                    'ticket_width'  => $width,
                    'ticket_height' => $height,
                    'qrcode_size'   => $qrsize,
                    'qrcode_top'    => $qtop,
                    'qrcode_left'   => $qleft,
                    'created_at'    => current_time('mysql'),
            ]);

            wp_redirect(add_query_arg('success', '1', wp_get_referer()));
            exit;
        }

        /**
        * Handle deleting a ticket design.
        *
        * @since 0.0.7
        */
        public function handle_delete_design()
        {
            $design_id = intval($_GET['design_id'] ?? 0);
            check_admin_referer('takamoa_delete_design_' . $design_id);

            if (!$design_id) {
                wp_redirect(add_query_arg('error', 'missing', wp_get_referer()));
                exit;
            }

            $default = intval(get_option('takamoa_papi_default_design'));
            if ($default === $design_id) {
                wp_redirect(add_query_arg('error', 'default', admin_url('admin.php?page=' . $this->plugin_name . '-design')));
                exit;
            }

            global $wpdb;
            $table          = $wpdb->prefix . 'takamoa_papi_designs';
            $payments_table = $wpdb->prefix . 'takamoa_papi_payments';

            if ($default) {
                $wpdb->update($payments_table, ['design_id' => $default], ['design_id' => $design_id], ['%d'], ['%d']);
            } else {
                $wpdb->query($wpdb->prepare("UPDATE $payments_table SET design_id = NULL WHERE design_id = %d", $design_id));
            }

            $wpdb->delete($table, ['id' => $design_id], ['%d']);

            wp_redirect(add_query_arg('deleted', '1', admin_url('admin.php?page=' . $this->plugin_name . '-design')));
            exit;
        }

        /**
        * AJAX handler to set a ticket design as default.
        *
        * @since 0.0.7
        */
        public function handle_set_default_design_ajax()
        {
            check_ajax_referer('takamoa_papi_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Unauthorized'], 403);
            }

            $design_id = intval($_POST['design_id'] ?? 0);
            if (!$design_id) {
                wp_send_json_error(['message' => 'ID manquant.']);
            }

            global $wpdb;
            $table  = $wpdb->prefix . 'takamoa_papi_designs';
            $exists = $wpdb->get_var(
                $wpdb->prepare('SELECT id FROM ' . $table . ' WHERE id = %d', $design_id)
            );
            if (!$exists) {
                wp_send_json_error(['message' => 'Design introuvable.']);
            }

            update_option('takamoa_papi_default_design', $design_id);
            wp_send_json_success();
        }

        public function display_options_page()
        {
            include plugin_dir_path(__FILE__) . 'partials/settings-page.php';
        }
    }
