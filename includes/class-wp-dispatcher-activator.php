<?php

/**
 * Fired during plugin activation
 *
 * @link       ekn.dev
 * @since      1.0.0
 *
 * @package    Wp_Dispatcher
 * @subpackage Wp_Dispatcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Dispatcher
 * @subpackage Wp_Dispatcher/includes
 * @author     Ekin Tertemiz <hola@ekn.dev>
 */
class Wp_Dispatcher_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// TO DO: Protected secret name LEVEL 1
		$dir_level1 = 'wp-dispatcher';
    
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();

		$files = array(
			array(
				'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
				'file'    => '.htaccess',
				'content' =>  'Options -Indexes' . "\n"
							. 'deny from all'
			)
			, array(
				'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
				'file'    => 'index.php',
				'content' => '<?php ' . "\n"
							 . '// Silence is golden.'

			)
		);

		foreach ( $files as $file ) {

			if (   ( wp_mkdir_p( $file['base'] ) )                       
			 // Recursive directory creation based on full path.
				&& ( ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) )    
				// If file not exist
			) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}

		//	Create Table for Uploads
		global $wpdb;
		$table_name = $wpdb->prefix . 'dispatcher_uploads';
		
		$charset_collate = $wpdb->get_charset_collate();

		if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name) != $table_name) {
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				author tinytext NOT NULL,
				count smallint(5) NOT NULL,
				filename tinytext NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
		
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

		}
		add_option( 'wp_dispatcher_uploads_db_version', "1" );

		//	Create Table for Uploads
		$table_name2 = $wpdb->prefix . 'dispatcher_links';
		
		if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name2) != $table_name2) {

			$sql2 = "CREATE TABLE $table_name2 (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				expires datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				upload_id mediumint(9) NOT NULL,
				hash_id CHAR(64) NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
		
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql2 );

		}
		add_option( 'wp_dispatcher_links_db_version', "1" );

		//	Flush efficiently 
		//	https://andrezrv.com/2014/08/12/efficiently-flush-rewrite-rules-plugin-activation/
		if( ! get_option( 'prefix_do_flush_rewrite' ) ) {

			add_option( 'prefix_do_flush_rewrite', true );
	
		}

		//	Create pages
		//	download-has-expired page

		$has_expired_title = 'Download is not available';

		if(!post_exists( $has_expired_title )) 
		{
			$has_expired_page = array(
				'post_title' => $has_expired_title,
				'post_content' => 'Sorry! Your download has expired. Please resubmit the form to receive a new download link.',
				'post_status' => 'publish',
				'post_date' => date('Y-m-d H:i:s'),
				'post_author' => '',
				'post_type' => 'page',
				'post_category' => array(0)
			);
			wp_insert_post( $has_expired_page );
		}
	}
}
