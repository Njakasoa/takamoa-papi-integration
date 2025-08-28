<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://nexa.takamoa.com/
 * @since      0.0.1
 *
 * @package    Takamoa
 * @subpackage takamoa-papi-integration/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.0.1
 * @package    Takamoa
 * @subpackage takamoa-papi-integration/includes
 * @author     Nexa by Takamoa <nexa.takamoa@gmail.com>
 */
class Takamoa_Papi_Integration_Deactivator
{

/**
 * Short Description. (use period)
 *
 * Long Description.
 *
 * @since    0.0.1
 */
    public static function deactivate()
    {
        var_dump('deactivate');
    }
}
