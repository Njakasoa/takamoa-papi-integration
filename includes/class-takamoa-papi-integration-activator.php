<?php
	/**
	* Fired during plugin activation.
	*
	* This class defines all code necessary to run during the plugin's activation.
	*
	* @since      0.0.1
	* @package    Takamoa
	* @subpackage takamoa-papi-integration/includes
	* @author     Nexa by Takamoa <nexa.takamoa@gmail.com>
	*/
	class Takamoa_Papi_Integration_Activator
	{
		/**
		* Short Description. (use period)
		*
		* Long Description.
		*
		* @since    0.0.1
		*/
		public static function activate()
		{
			global $wpdb;

			$table         = $wpdb->prefix . 'takamoa_papi_payments';
			$designs_table = $wpdb->prefix . 'takamoa_papi_designs';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table (
				id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				reference VARCHAR(100) NOT NULL,
				client_name VARCHAR(255) NOT NULL,
				amount DECIMAL(10,2) NOT NULL,
				description VARCHAR(255),
				payer_email VARCHAR(255),
				payer_phone VARCHAR(50),
				provider VARCHAR(50),
				success_url TEXT,
				failure_url TEXT,
				notification_url TEXT,
				link_creation DATETIME,
				link_expiration DATETIME,
				payment_link TEXT,
				payment_status ENUM('PENDING', 'SUCCESS', 'FAILED') DEFAULT 'PENDING',
				payment_method VARCHAR(50),
				currency VARCHAR(10),
				fee DECIMAL(10,2),
				notification_token VARCHAR(255),
				is_test_mode BOOLEAN DEFAULT FALSE,
				test_reason VARCHAR(255),
				raw_request LONGTEXT,
				raw_response LONGTEXT,
				raw_notification LONGTEXT,
				design_id BIGINT UNSIGNED,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
				updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY unique_reference (reference)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
			// Tickets table
			$tickets_table = $wpdb->prefix . 'takamoa_papi_tickets';

			$sql_tickets = "CREATE TABLE $tickets_table (
				id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				reference VARCHAR(100) NOT NULL,
				qrcode_link TEXT,
				description TEXT,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
				updated_at DATE DEFAULT NULL,
				status VARCHAR(50) DEFAULT 'PENDING',
				last_notification DATETIME NULL,
				UNIQUE KEY unique_reference (reference)
			) $charset_collate;";
			dbDelta($sql_tickets);
			// Designs table.
			// @since 0.0.3
			$sql_designs = "CREATE TABLE $designs_table (
				id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				title VARCHAR(255) NOT NULL,
				image_url TEXT NOT NULL,
				ticket_width INT NOT NULL,
				ticket_height INT NOT NULL,
				qrcode_size INT NOT NULL,
				qrcode_top INT NOT NULL,
				qrcode_left INT NOT NULL,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP
			) $charset_collate;";
			dbDelta($sql_designs);
			// Ensure payments table has design_id column and foreign key
			if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
				$column = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'design_id'");
				if (empty($column)) {
					$wpdb->query("ALTER TABLE $table ADD design_id BIGINT UNSIGNED NULL AFTER raw_notification");
				}

				$fk = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s AND REFERENCED_TABLE_NAME = %s",
						DB_NAME,
						$table,
						'design_id',
						$designs_table
					)
				);
				if (empty($fk)) {
					$wpdb->query("ALTER TABLE $table ADD CONSTRAINT fk_takamoa_papi_payments_design_id FOREIGN KEY (design_id) REFERENCES $designs_table(id)");
				}
			}
			// Options par défaut à créer
			add_option('takamoa_papi_api_key', '');

			add_option('takamoa_papi_success_url', home_url('/paiementreussi'));
			add_option('takamoa_papi_failure_url', home_url('/paiementechoue'));
			add_option('takamoa_papi_notification_url', home_url('/papi-notify'));

			add_option('takamoa_papi_valid_duration', 60);

			add_option('takamoa_papi_providers', ['MVOLA', 'ORANGE_MONEY', 'AIRTEL_MONEY', 'BRED']);

			add_option('takamoa_papi_optional_fields', ['payerEmail', 'payerPhone']);

			add_option('takamoa_papi_test_mode', false);
			add_option('takamoa_papi_test_reason', '');
			add_option('takamoa_papi_default_design', 0);
		}
	}
