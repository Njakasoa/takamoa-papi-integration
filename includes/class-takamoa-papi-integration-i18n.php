<?php
	/**
	* Define the internationalization functionality.
	*
	* Loads and defines the internationalization files for this plugin
	* so that it is ready for translation.
	*
	* @since      0.0.1
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	* @author     Nexa by Takamoa <nexa.takamoa@gmail.com
	*/
	class Takamoa_Papi_Integration_i18n
	{
		/**
		* Load the plugin text domain for translation.
		*
		* @since    0.0.1
		*/
		public function load_plugin_textdomain()
		{
			load_plugin_textdomain(
				'takamoa-papi-integration.pot',
				false,
				dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
			);
		}



	}
