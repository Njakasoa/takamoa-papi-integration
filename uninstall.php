<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://nexa.takamoa.com/
 * @since      0.0.1
 *
 * @package    Takamoa
 */

// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
    delete_option('takamoa_papi_api_key');
    delete_option('takamoa_papi_success_url');
    delete_option('takamoa_papi_failure_url');
    delete_option('takamoa_papi_notification_url');
    delete_option('takamoa_papi_valid_duration');
    delete_option('takamoa_papi_providers');
    delete_option('takamoa_papi_optional_fields');
    delete_option('takamoa_papi_test_mode');
    delete_option('takamoa_papi_test_reason');
}
