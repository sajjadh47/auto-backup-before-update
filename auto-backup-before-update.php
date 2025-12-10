<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Auto_Backup_Before_Update
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Auto Backup Before Update
 * Plugin URI:        https://wordpress.org/plugins/auto-backup-before-update/
 * Description:       Automatically creates a backup of each plugin and theme before updates — so you can easily roll back to any previous version if something breaks.
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       auto-backup-before-update
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_VERSION', '1.0.1' );

/**
 * Define Plugin Folders Path
 */
define( 'ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-auto-backup-before-update-activator.php
 *
 * @since    1.0.0
 */
function abbu_on_activate_auto_backup_before_update() {
	require_once ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH . 'includes/class-auto-backup-before-update-activator.php';

	Auto_Backup_Before_Update_Activator::on_activate();
}

register_activation_hook( __FILE__, 'abbu_on_activate_auto_backup_before_update' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-auto-backup-before-update-deactivator.php
 *
 * @since    1.0.0
 */
function abbu_on_deactivate_auto_backup_before_update() {
	require_once ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH . 'includes/class-auto-backup-before-update-deactivator.php';

	Auto_Backup_Before_Update_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'abbu_on_deactivate_auto_backup_before_update' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    1.0.0
 */
require ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH . 'includes/class-auto-backup-before-update.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function abbu_run_auto_backup_before_update() {
	$plugin = new Auto_Backup_Before_Update();

	$plugin->run();
}

abbu_run_auto_backup_before_update();
