<?php
/**
 * This file contains the definition of the Auto_Backup_Before_Update_Deactivator class, which
 * is used during plugin deactivation.
 *
 * @package       Auto_Backup_Before_Update
 * @subpackage    Auto_Backup_Before_Update/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin deactivation.
 *
 * @since    1.0.0
 */
class Auto_Backup_Before_Update_Deactivator {
	/**
	 * Deactivation hook.
	 *
	 * This function is called when the plugin is deactivated. It can be used to
	 * perform tasks such as cleaning up temporary data, unscheduling cron jobs,
	 * or removing options.
	 *
	 * @since     1.0.0
	 * @static
	 * @access    public
	 */
	public static function on_deactivate() {}
}
