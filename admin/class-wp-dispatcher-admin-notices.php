<?php

/**
 * Generate WP Dispatcher admin notices
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
class Wp_Dispatcher_Admin_Notices
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Successful Upoad Notice
	 *
	 * @since    1.0.0
	 */
	public function wp_dispatcher_admin_notices()
	{

		global $pagenow;
		if ($pagenow == 'admin.php') {
			if (isset($_GET['upload']) && $_GET['upload'] == 'success' && $_GET['page'] == 'wp_dispatcher') {
?>
				<div class="notice notice-success is-dismissible">
					<p><?php _e('Success! The file has been uploaded!', 'wp-dispatch'); ?></p>
				</div>
			<?php
			} else if (isset($_GET['upload']) && $_GET['upload'] == 'empty' && $_GET['page'] == 'wp_dispatcher_new') {
			?>
				<div class="notice notice-error">
					<p><?php _e('Error! Please select a file to upload!', 'wp-dispatch'); ?></p>
				</div>
			<?php
			} else if (isset($_GET['upload']) && $_GET['upload'] == 'duplicate' && $_GET['page'] == 'wp_dispatcher_new') {
			?>
				<div class="notice notice-warning">
					<p><?php _e('Warning! You have already uploaded a file with this name. Please rename your file before upload!', 'wp-dispatch'); ?></p>
				</div>
<?php
			}
		}
	}
}
