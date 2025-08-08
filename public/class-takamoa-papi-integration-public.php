<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://nexa.takamoa.com/
 * @since      0.0.1
 *
 * @package    Takamoa
 * @subpackage Wp-plugni-takamoa-papi-integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Takamoa
 * @subpackage takamoa-papi-integration/public
 * @author     Nexa by Takamoa <nexa.takamoa@gmail.com>
 */
class Takamoa_Papi_Integration_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;	

	private $functions;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */	
	public function __construct( $plugin_name, $version, $functions ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = $functions;

		add_shortcode('takamoa_papi_form', [$this, 'render_vue_form_shortcode']);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */	
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/takamoa-papi-integration-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/takamoa-papi-integration-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script('vuejs', 'https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js', [], null, true);
		wp_enqueue_script(
			$this->plugin_name . '-form',
			plugin_dir_url(__FILE__) . 'js/takamoa-papi-form.js',
			['vuejs'],
			$this->version,
			true
		);
		wp_localize_script($this->plugin_name . '-form', 'TakamoaPapiVars', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'api_nonce' => wp_create_nonce('takamoa_papi_nonce'),
			'isTestMode' => (bool) get_option('takamoa_papi_test_mode'),
			'testReason' => get_option('takamoa_papi_test_reason', ''),
			'validDuration' => (int) get_option('takamoa_papi_valid_duration', 60),
			'providers' => get_option('takamoa_papi_providers', ['MVOLA']),
			'optionalFields' => get_option('takamoa_papi_optional_fields', ['payerPhone', 'description']),
			'successUrl' => get_option('takamoa_papi_success_url', home_url('/paiementreussi')),
			'failureUrl' => get_option('takamoa_papi_failure_url', home_url('/paiementechoue')),
			'notificationUrl' => home_url('/papi-notify'),
		]);
	}

	/**
	 * Shortcode qui affiche le conteneur Vue.js
	 */
	public function render_vue_form_shortcode($atts) {
		$atts = shortcode_atts([
			'amount' => '',
			'reference' => ''
		], $atts);
	
		$timestamp = time();
	
		if (empty($atts['reference'])) {
			$atts['reference'] = 'takamoa-papi-' . $timestamp;
		} else {
			if (str_contains($atts['reference'], '{{TIMESTAMP}}')) {
				$atts['reference'] = str_replace('{{TIMESTAMP}}', $timestamp, $atts['reference']);
			} elseif (!preg_match('/\d{10}$/', $atts['reference'])) {
				$atts['reference'] .= '-' . $timestamp;
			}
		}
	
		// Générer un ID HTML unique
		$uid = 'takamoa-papi-app-' . uniqid();
	
		ob_start();
		?>
		<div id="<?php echo esc_attr($uid); ?>"
			 class="takamoa-papi-app"
			 data-amount="<?php echo esc_attr($atts['amount']); ?>"
			 data-reference="<?php echo esc_attr($atts['reference']); ?>">
		</div>
		<?php
		return ob_get_clean();
	}
	
	
}