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
class Wp_Dispatcher_Settings
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
	public function setup_plugin_options_menu()
	{

		//Add the menu to the Plugins set of menu items
		add_menu_page(
			'Library', 					// The title to be displayed in the browser window for this page.
			'WP Dispatcher',					// The text to be displayed for this menu item
			'manage_options',					// Which type of users can see this menu item
			'wp_dispatcher',			// The unique ID - that is, the slug - for this menu item
			array($this, 'render_settings_page_content'),				// The name of the function to call when rendering this menu's page
			'dashicons-tide',
			15
		);
	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content($active_tab = '')
	{

?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">
			<?php settings_errors(); ?>

			<h1 class="wp-heading-inline"><?php _e('WP Dispatcher', 'wp-dispatcher'); ?></h1>
			<a href="<?php echo get_admin_url() . 'admin.php?page=wp_dispatcher_new' ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
			<hr class="wp-header-end">
			<?php

			if (isset($_GET['tab'])) {

				$active_tab = sanitize_text_field($_GET['tab']);
			} else if ($active_tab == 'uploads') {
				$active_tab = 'uploads';
			} else if ($active_tab == 'links') {
				$active_tab = 'links';
			} else if ($active_tab == 'settings') {
				$active_tab = 'settings';
			} else {
				$active_tab = 'uploads';
			} // end if/else
			?>


			<h2 class="nav-tab-wrapper">
				<a href="?page=wp_dispatcher&tab=uploads" class="nav-tab <?php echo $active_tab == 'uploads' ? 'nav-tab-active' : ''; ?>"><?php _e('Uploads', 'wp-dispatcher'); ?></a>
				<a href="?page=wp_dispatcher&tab=links" class="nav-tab <?php echo $active_tab == 'links' ? 'nav-tab-active' : ''; ?>"><?php _e('Links', 'wp-dispatcher'); ?></a>
				<a href="?page=wp_dispatcher&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'wp-dispatcher'); ?></a>
			</h2>

			<?php

			if ($active_tab == 'uploads') {
				$uploads_list_table = new Uploads_List_Table;
				$uploads_list_table->prepare_items();
			?>
				<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
				<form id="movies-filter" method="get">
					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
					<?php $uploads_list_table->display();	?>
				</form>



			<?php
			} else if ($active_tab == 'links') {
				$links_list_table = new Links_List_Table;
				$links_list_table->prepare_items();
			?>
				<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
				<form id="movies-filter" method="get">
					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
					<?php $links_list_table->display();	?>
				</form>

			<?php
			} else if ($active_tab == 'settings') {
			?>
				<form method="post" action="options.php">

					<?php

					settings_fields('wp_dispatcher_options');
					do_settings_sections('wp_dispatcher_options');
					submit_button();
					?>
				</form>
			<?php

			} else {
				echo "none";
			}

			?>
		</div><!-- /.wrap -->
<?php
	}

	/**
	 * This function provides a simple description for the General Options page.
	 *
	 * It's called from the 'wppb-demo_initialize_theme_options' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function general_options_callback()
	{
		$options = get_option('wp_dispatcher_options');
		//var_dump($options);
		echo '<p>' . __('Adjust settings for WP Dispatcher.', 'wp-dispatcher') . '</p>';
	} // end general_options_callback


	public function default_dispatcher_options()
	{
		$defaults = array(
			'expires_after'		=>	48,
			'url_resolver'		=>	'resolve'
		);

		return $defaults;
	}


	public function initialize_dispatcher_options()
	{
		// If the theme options don't exist, create them.
		if (false == get_option('wp_dispatcher_options')) {
			$default_array = $this->default_dispatcher_options();
			add_option('wp_dispatcher_options', $default_array);
		}

		add_settings_section(
			'general_settings_section',			            // ID used to identify this section and with which to register options
			__('Dispatcher Settings', 'wp-dispatcher'),		        // Title to be displayed on the administration page
			array($this, 'general_options_callback'),	    // Callback used to render the description of the section
			'wp_dispatcher_options'		                // Page on which to add this section of options
		);

		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(
			'expires_after',						        // ID used to identify the field throughout the theme
			__('Expires after', 'wp-dispatcher'),					// The label to the left of the option interface element
			array($this, 'expires_after_callback'),	// The name of the function responsible for rendering the option interface
			'wp_dispatcher_options',	            // The page on which this option will be displayed
			'general_settings_section',			        // The name of the section to which this field belongs
			array(								        // The array of arguments to pass to the callback. In this case, just a description.
				__('Sets how much time after generation a link is available. Does not affect already created links.', 'wp-dispatcher'),
			)
		);

		/* 		add_settings_field(
			'url_resolver',
			__( 'URL Resolver slug', 'wp-dispatcher' ),
			array( $this, 'url_resolver_callback'),
			'wp_dispatcher_options',
			'general_settings_section',
			array(
				__( 'Sets the name of the url resolver tag.', 'wp-dispatcher' ),
			)
		); */

		// Finally, we register the fields with WordPress
		register_setting(
			'wp_dispatcher_options',
			'wp_dispatcher_options',
			array($this, 'validate_input')
		);
	}

	public function expires_after_callback($args)
	{
		$options = get_option('wp_dispatcher_options');

		$html = '<input class="small-text"  type="number" min="1" max="999" id="expire_after" name="wp_dispatcher_options[expires_after]" value="' . $options['expires_after'] . '" /> hours';
		$html .= '<br><br><i>&nbsp;'  . $args[0] . '</i>';
		echo $html;
	}

	public function url_resolver_callback($args)
	{
		$options = get_option('wp_dispatcher_options');

		$html = '<input type="text" id="url_resolver" name="wp_dispatcher_options[url_resolver]" value="' . $options['url_resolver'] . '" />';
		$html .= '<br><i>&nbsp;'  . $args[0] . '</i>';
		echo $html;
	}


	public function validate_input($input)
	{

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach ($input as $key => $value) {

			// Check to see if the current option has a value. If so, process it.
			if (isset($input[$key])) {

				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags(stripslashes($input[$key]));
			} // end if

		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters('validate_input', $output, $input);
	} // end validate_input_examples


}
