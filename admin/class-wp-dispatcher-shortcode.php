<?php

/**
 * Generate WP Dispatcher shortcodes
 *
 * @link       ekn.dev
 * @since      1.0.0
 *
 * @package    Wp_Dispatcher_Shortcode
 * @subpackage Wp_Dispatcher_Shortcode/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Wp_Dispatcher_Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

  /**
	 * Generate Shortcode
	 *
	 * @since    1.0.0
	 */
  public function generate_download_link($atts){
    
    extract(shortcode_atts(array(
        'id' => null,
		), $atts));
		
		if($id != null) {

			//	1. Find upload in database
			global $wpdb;

			$table_name = $wpdb->prefix . 'dispatcher_uploads';
			$upload = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$id}" );

			if(null === $upload) {
				return "Error";
			}

			// 2. prepare inserts
			$hash = hash('sha256', current_time( 'mysql' ).$upload->filename);

			$options = get_option( 'wp_dispatcher_options' );
			$expiration_hours = $options['expires_after'];
			
			$created = current_time( 'mysql' );
			$expiration_time = $expiration_hours * 60 * 60; // 24hours
			$expires = date( 'Y-m-d H:i:s', strtotime($created) + $expiration_time );

			//	3. insert into database
			$table_name2 = $wpdb->prefix . 'dispatcher_links';

			global $wpdb;
			$wpdb->insert( 
				$table_name2, 
				array( 
						'created' => current_time( 'mysql' ), 
						'expires' => $expires,
						'upload_id' => $upload->id,
						'hash_id' => $hash
					)
				);

				return get_site_url() . "?resolve=" . $hash;
			
		}
    else {
			return "<pre>Error: Invalid input. Download-Link could not be generated. Please contact administrator.</pre>";
		}
  
	}
	

}