<?php
/**
 *
 * @link              http://mzoo.org
 * @since             1.0.0
 * @package           Mobilize_America
 *
 * @wordpress-plugin
 * Plugin Name:       MZ Mobilize America Interface
 * Plugin URI:        https://github.com/MikeiLL/mz-mobilize-america/
 * Description:       Simple interface for Mobilize America API.
 * Version:           1.0.3
 * Author:            mZoo/Mike iLL
 * Author URI:        http://mzoo.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mz-mobilize-america
 * Domain Path:       /languages
 */

namespace MZ_Mobilize_America;
use MZ_Mobilize_America as NS;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'mz-mobilize-america' );

define( NS . 'PLUGIN_VERSION', '1.0.3' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'mz-mobilize-america' );

/**
 * Autoload Classes
 */
$wp_mobilize_america_autoload = NS\PLUGIN_NAME_DIR . '/vendor/autoload.php';
if (file_exists($wp_mobilize_america_autoload)) {
	require_once $wp_mobilize_america_autoload;
}

if (!class_exists('\MZ_Mobilize_America\Core\Plugin_Core')) {
	exit('MZ Mobilize America requires Composer autoloading, which is not configured');
}

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . '\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . '\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0.0
 */
class Mobilize_America {

	/**
	 * The instance of the plugin.
	 *
	 * @since    1.0.0
	 * @var      Init $init Instance of the plugin.
	 */
	private static $init;
	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null === self::$init ) {
			self::$init = new Core\Plugin_Core();
			self::$init->run();
		}

		return self::$init;
	}

}

/**
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 **/
function wp_plugin_name_init() {
		return Mobilize_America::init();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
		wp_plugin_name_init();
}
