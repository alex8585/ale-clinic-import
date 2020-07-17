<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Clinic_Import
 * @subpackage Ale_Clinic_Import/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ale_Clinic_Import
 * @subpackage Ale_Clinic_Import/includes
 * @author     Your Name <email@example.com>
 */
class Ale_Clinic_Import_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		$sqlClinics = "CREATE TABLE IF NOT EXISTS " . ALE_CLINICS_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			address varchar(255) NOT NULL,
			time_formated varchar(255) NOT NULL,
			station varchar(255) NOT NULL,
			UNIQUE KEY id (id)
		)ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sqlClinics);
	}

}
