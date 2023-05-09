<?php

/**
 * Class to add new Upload.
 *
 * @link       ekn.dev
 * @since      1.0.0
 *
 * @package    Wp_Dispatcher
 * @subpackage Wp_Dispatcher/admin
 */


/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Wp_Dispatcher_Add_New_Upload
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
	 * This function introduces the theme options into the 'Appearance' menu and into a top-level
	 * 'WPPB Demo' menu.
	 */
	public function setup_plugin_sub_menu()
	{

		add_submenu_page(
			'wp_dispatcher',
			__('Add New', 'wp-dispatcher'),
			__('Add New', 'wp-dispatcher'),
			'manage_options',
			'wp_dispatcher_new',
			array($this, 'render_new_upload_page_content'),				// The name of the function to call when rendering this menu's page

		);
	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_new_upload_page_content()
	{

?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h2><?php _e('WP Dispatcher', 'wp-dispatcher'); ?></h2>
			<?php settings_errors(); ?>
			<h2><?php _e('Add new file', 'wp-dispatcher'); ?></h2>

			<!-- Form to handle the upload - The enctype value here is very important -->
			<form action="<?php admin_url('admin-post.php') ?>" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="process_upload">
				<input type='file' id='file_upload' name='file_upload'></input>
				<?php submit_button(__('Upload', 'wp-dispatcher')) ?>
			</form>

		</div><!-- /.wrap -->
<?php
	}


	public function wp_dispatcher_process_upload()
	{
		// First check if the file appears on the _FILES array
		if ($_FILES['file_upload']["error"] == 4) {
			wp_safe_redirect(get_admin_url() . 'admin.php?page=wp_dispatcher_new&upload=empty');
			exit();
		}

		if (isset($_FILES['file_upload'])) {

			$filename = sanitize_file_name($_FILES['file_upload']['name']);
			//$filename = $_FILES['file_upload']['name'];

			//$ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
			//$uuid = uniqid();

			$destination = WP_CONTENT_DIR . '/uploads/wp-dispatcher/' . $filename;

			// Check if file already exists
			if (file_exists($destination)) {
				wp_safe_redirect(get_admin_url() . 'admin.php?page=wp_dispatcher_new&upload=duplicate');
				exit();
			}


			$uploaded = move_uploaded_file($_FILES['file_upload']['tmp_name'], $destination);

			if ($uploaded) {
				// Insert to database
				global $wpdb;
				$table_name = $wpdb->prefix . 'dispatcher_uploads';

				$wpdb->insert(
					$table_name,
					array(
						'date' => current_time('mysql'),
						'count' => 0,
						'author' => wp_get_current_user()->user_login,
						'filename' => $filename,
					)
				);

				wp_safe_redirect(get_admin_url() . 'admin.php?page=wp_dispatcher&upload=success');
				exit();
			}

			echo __('Error uploading file.', 'wp-dispatcher');
		}
	}
}
