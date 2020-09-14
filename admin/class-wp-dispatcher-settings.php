<?php

/**
 * Plugin settings.
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
class Wp_Dispatcher_Settings {

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
	 * This function introduces the theme options into the 'Appearance' menu and into a top-level
	 * 'WPPB Demo' menu.
	 */
	public function setup_plugin_options_menu() {

		//Add the menu to the Plugins set of menu items
		add_menu_page(
			'Library', 					// The title to be displayed in the browser window for this page.
			'WP Dispatcher',					// The text to be displayed for this menu item
			'manage_options',					// Which type of users can see this menu item
      'wp_dispatcher',			// The unique ID - that is, the slug - for this menu item
      array( $this, 'render_settings_page_content'),				// The name of the function to call when rendering this menu's page
			'dashicons-tide',
			15
		);

	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {

		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">
		<?php settings_errors(); ?>

			<h1 class="wp-heading-inline"><?php _e( 'WP Dispatcher', 'wp-dispatcher' ); ?></h1>
			<a href="<?php echo get_admin_url() . 'admin.php?page=wp_dispatcher_new' ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>

			<?php 
			
				if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'uploads' ) {
				$active_tab = 'uploads';
			} else if( $active_tab == 'links' ) {
				$active_tab = 'links';
			} else if( $active_tab == 'settings' ) {
				$active_tab = 'settings';
			} else {
				$active_tab = 'uploads';
			} // end if/else?>


			<h2 class="nav-tab-wrapper">
				<a href="?page=wp_dispatcher&tab=uploads" class="nav-tab <?php echo $active_tab == 'uploads' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Uploads', 'wp-dispatcher' ); ?></a>
				<a href="?page=wp_dispatcher&tab=links" class="nav-tab <?php echo $active_tab == 'links' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Links', 'wp-dispatcher' ); ?></a>
				<a  href="?page=wp_dispatcher&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'wp-dispatcher' ); ?></a>
			</h2>

			<?php

			if( $active_tab == 'uploads' )		
			{
				$uploads_list_table = new Uploads_List_Table;
				$uploads_list_table->prepare_items();
				?>
				<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
				<form id="movies-filter" method="get">
				<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					<?php $uploads_list_table->display();	?>
				</form>

			

<?php
			}
			else if ( $active_tab == 'links' ) {
				$links_list_table = new Links_List_Table;
				$links_list_table->prepare_items();
				?>
				<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
				<form id="movies-filter" method="get">
					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					<?php $links_list_table->display();	?>
				</form>
				
			<?php
			}
			else if ( $active_tab == 'settings' ) {
				?>
				<div class="notice notice-info ">
        	<p><?php _e( 'Settings are not available yet.', 'wp-dispatcher' ); ?></p>
				</div>
				
				<p>Plugin is still in development. Next versions will include settings. If you would like to contribute to plugin development please contact via plugin site.</p>

				<?php
				
			}
			else {
				echo "none";
			}

?>      		
	</div><!-- /.wrap -->
	<?php
	}
}