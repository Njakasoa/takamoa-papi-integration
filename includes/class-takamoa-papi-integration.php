<?php
/**
	* The file that defines the core plugin class
	*
	* A class definition that includes attributes and functions used across both the
	* public-facing side of the site and the admin area.
	*
	* @link       https://nexa.takamoa.com/
	* @since      0.0.1
	*
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	*/
/**
	* The core plugin class.
	*
	* This is used to define internationalization, admin-specific hooks, and
	* public-facing site hooks.
	*
	* Also maintains the unique identifier of this plugin as well as the current
	* version of the plugin.
	*
	* @since      0.0.1
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	* @author     Nexa by Takamoa <nexa.takamoa@gmail.com>
	*/
class Takamoa_Papi_Integration {
	/**
	* The loader that's responsible for maintaining and registering all hooks that power
	* the plugin.
	*
	* @since    0.0.1
	* @access   protected
	* @var      Takamoa_Papi_Integration_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $loader;

	/**
	* The unique identifier of this plugin.
	*
	* @since    0.0.1
	* @access   protected
	* @var      string    $plugin_name    The string used to uniquely identify this plugin.
	*/
	protected $plugin_name;

	/**
	* The current version of the plugin.
	*
	* @since    0.0.1
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/

	protected $version;

	/**
	* Functions of the plugin.
	*
	* @since    0.0.1
	* @access   protected
	* @var      Takamoa_Papi_Integration_Functions    $functions
	*/
	
	protected $functions;

	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    0.0.1
	*/

	public function __construct() {
		if ( defined( 'TAKAMOA_PAPI_INTEGRATION_VERSION' ) ) {
			$this->version = TAKAMOA_PAPI_INTEGRATION_VERSION;
		} else {
			$this->version = '0.0.7';
		}
		$this->plugin_name = 'takamoa-papi-integration';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	* Load the required dependencies for this plugin.
	*
	* Include the following files that make up the plugin:
	*
	* - Takamoa_Papi_Integration_Loader. Orchestrates the hooks of the plugin.
	* - Takamoa_Papi_Integration_i18n. Defines internationalization functionality.
	* - Takamoa_Papi_Integration_Router. Defines studio routes.
	* - Takamoa_Papi_Integration_Admin. Defines all hooks for the admin area.
	* - Takamoa_Papi_Integration_Public. Defines all hooks for the public side of the site.
	*
	* Create an instance of the loader which will be used to register the hooks
	* with WordPress.
	*
	* @since    0.0.1
	* @access   private
	*/

	private function load_dependencies() {
		/**
		* The class responsible for providing functions of the
		* core plugin.
		*/	
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-takamoa-papi-integration-functions.php';
		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-takamoa-papi-integration-loader.php';
		
		/**
		* The class responsible for defining internationalization functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-takamoa-papi-integration-i18n.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-takamoa-papi-integration-admin.php';

		/**
		* The class responsible for defining all actions that occur in the public-facing
		* side of the site.
		*/		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-takamoa-papi-integration-public.php';

		$this->loader = new Takamoa_Papi_Integration_Loader();
		$this->functions = new Takamoa_Papi_Integration_Functions();
	}

	/**
	* Define the locale for this plugin for internationalization.
	*
	* Uses the Takamoa_Papi_Integration_i18n class in order to set the domain and to register the hook
	* with WordPress.
	*
	* @since    0.0.1
	* @access   private
	*/
	private function set_locale() {

		$plugin_i18n = new Takamoa_Papi_Integration_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	* Register all of the hooks related to the admin area functionality
	* of the plugin.
	*
	* @since    0.0.1
	* @access   private
	*/

	private function define_admin_hooks() {

		$plugin_admin = new Takamoa_Papi_Integration_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_functions() );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_menu');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_post_takamoa_save_design', $plugin_admin, 'handle_save_design');
	$this->loader->add_action('wp_ajax_takamoa_resend_payment_email', $this->functions, 'handle_resend_payment_email_ajax');
	$this->loader->add_action('wp_ajax_takamoa_ticket_exists', $this->functions, 'handle_ticket_exists_ajax');
	$this->loader->add_action('wp_ajax_takamoa_generate_ticket', $this->functions, 'handle_generate_ticket_ajax');
	$this->loader->add_action('wp_ajax_takamoa_scan_ticket', $this->functions, 'handle_scan_ticket_ajax'); // @since 0.0.5
	$this->loader->add_action('wp_ajax_takamoa_validate_ticket', $this->functions, 'handle_validate_ticket_ajax'); // @since 0.0.6
	$this->loader->add_action('wp_ajax_takamoa_send_ticket_email', $this->functions, 'handle_send_ticket_email_ajax');
}

	/**
	* Register all of the hooks related to the public-facing functionality
	* of the plugin.
	*
	* @since    0.0.1
	* @access   private
	*/	
	private function define_public_hooks() {

		$plugin_public = new Takamoa_Papi_Integration_Public( $this->get_plugin_name(), $this->get_version(), $this->get_functions() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action('init', $this->functions, 'register_endpoints');
		$this->loader->add_filter('query_vars', $this->functions, 'register_query_vars');
		$this->loader->add_action('template_redirect', $this->functions, 'handle_endpoints');
		$this->loader->add_action('wp_ajax_takamoa_create_payment', $this->functions, 'handle_create_payment_ajax');
		$this->loader->add_action('wp_ajax_nopriv_takamoa_create_payment', $this->functions, 'handle_create_payment_ajax');
		$this->loader->add_action('wp_ajax_takamoa_check_payment_status', $this->functions, 'handle_check_payment_status_ajax');
		$this->loader->add_action('wp_ajax_nopriv_takamoa_check_payment_status', $this->functions, 'handle_check_payment_status_ajax');
	}

	/**
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @since    0.0.1
	*/
	public function run() {
		$this->loader->run();
		
	}
	

	/**
	* The name of the plugin used to uniquely identify it within the context of
	* WordPress and to define internationalization functionality.
	*
	* @since     0.0.1
	* @return    string    The name of the plugin.
	*/
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	* The reference to the class that orchestrates the hooks with the plugin.
	*
	* @since     0.0.1
	* @return    Takamoa_Papi_Integration_Loader    Orchestrates the hooks of the plugin.
	*/
	public function get_loader() {
		return $this->loader;
	}

	/**
	* Retrieve the version number of the plugin.
	*
	* @since     0.0.1
	* @return    string    The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}

	/**
	* Retrieve the functions of the plugin.
	*
	* @since     0.0.1
	* @return    object    The object where functions are stored.
	*/
	public function get_functions() {
		return $this->functions;
	}
}
?>
