<?php
/**
 * This file contains the definition of the Auto_Backup_Before_Update_Activator class, which
 * is used during plugin activation.
 *
 * @package       Auto_Backup_Before_Update
 * @subpackage    Auto_Backup_Before_Update/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin activation.
 *
 * @since    1.0.0
 */
class Auto_Backup_Before_Update_Activator {
	/**
	 * Activation hook.
	 *
	 * This function is called when the plugin is activated. It can be used to
	 * perform tasks such as creating database tables, setting up default options,
	 * or scheduling cron jobs.
	 *
	 * @since     1.0.0
	 * @static
	 * @access    public
	 */
	public static function on_activate() {
		// Check if backup storage directory is writable.
		if ( Auto_Backup_Before_Update_Admin::is_backup_storage_dir_writable() ) {
			// Create backup storage if not already there.
			Auto_Backup_Before_Update_Admin::create_backup_storage_dir();
		}
	}
}
