<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://kunato.ai/
 * @since      1.0.0
 *
 * @package    Kunato
 * @subpackage Kunato/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kunato
 * @subpackage Kunato/admin
 * @author     Kunato <ms@kunato.io>
 */
class Kunato_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kunato_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kunato_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kunato-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kunato_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kunato_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kunato-admin.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * custom option and settings
	 * 
	 * @since    1.0.0
	 * 
	*/
	public function kunato_options_settings_init(){
		// Register a new setting for "kunato_options" page.
		register_setting('kunato_options', 'kunato_identification');
		register_setting('kunato_options', 'kunato_currency');
		register_setting('kunato_options', 'kunato_disable_title_modify');

		// Register a new section in the "kunato_options" page.
		add_settings_section(
			'kunato_options_section_developers',
			__('Kunato Identifier.', KUNATO_TEXT_DOMAIN),
			array($this, 'kunato_options_section_developers_callback'),
			'kunato_options'
		);

		// Register a new field in the "kunato_options_section_developers" section, inside the "kunatoplg" page.
		add_settings_field(
			'kunato_identification', // As of WP 4.6 this value is used only internally.
			// Use $args' label_for to populate the id inside the callback.
			__('Identifier', KUNATO_TEXT_DOMAIN),
			array($this, 'kunato_options_identification_name_cb'), // callback
			'kunato_options',
			'kunato_options_section_developers',
			array(
				'label_for'         => 'kunato_identification',
				'class'             => 'kunatoplg_row',
			)
		);

		add_settings_field(
			'kunato_currency', 
			__('Currency', KUNATO_TEXT_DOMAIN),
			array($this, 'kunato_options_currency_name_cb'), // callback
			'kunato_options',
			'kunato_options_section_developers',
			array(
				'label_for'         => 'kunato_currency',
				'class'             => 'kunatoplg_row',
			)
		);

		// Option to disable title modification for HTML injection
		//
		// add_settings_field(
		// 	'kunato_disable_title_modify',
		// 	__('Disable title modify', KUNATO_TEXT_DOMAIN),
		// 	array($this, 'kunato_options_disable_title_modify_cb'), // callback
		// 	'kunato_options',
		// 	'kunato_options_section_developers',
		// 	array(
		// 		'label_for'         => 'kunato_disable_title_modify',
		// 		'class'             => 'kunatoplg_row',
		// 	)
		// );
	}

	/**
	 * Developers section callback function.
	 * 
	 * @param array $args  The settings array, defining title, id, callback.
	 * @since    1.0.0
	 */
	public function kunato_options_section_developers_callback($args){
	?>
		<p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Please enter an identifier.', KUNATO_TEXT_DOMAIN); ?></p>
	<?php
	}


	/**
	 * Kunato Identifier field callback function.
	 *
	 * @param array $args
	 * @since    1.0.0
	 */
	public function kunato_options_identification_name_cb($args)
	{
		// Get the value of the setting we've registered with register_setting()
		$options = get_option('kunato_identification');
	?>
		<input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($args['label_for']); ?>" value="<?php echo $options; ?>" placeholder=<?php _e("Enter Identifier", KUNATO_TEXT_DOMAIN); ?> />
	<?php
	}

	/**
	 * Kunato currency field callback function.
	 *
	 * @param array $args
	 * @since    1.0.0
	 */
	public function kunato_options_currency_name_cb($args){
		// Get the value of the setting we've registered with register_setting()
		$options = get_option('kunato_currency'); 
	?>
		<select id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($args['label_for']); ?>">
			<option value=""><?php _e('Select Currency', KUNATO_TEXT_DOMAIN); ?></option>
			<option value="inr" <?php selected($options, 'inr'); ?>><?php _e('INR', KUNATO_TEXT_DOMAIN); ?></option>
			<option value="usd" <?php selected($options, 'usd'); ?>><?php _e('USD', KUNATO_TEXT_DOMAIN); ?></option>
		</select>
	<?php
	}

	/**
	 * Kunato language field callback function.
	 *
	 * @param array $args
	 * @since    1.0.0
	 */
	public function kunato_options_disable_title_modify_cb($args){
		// Get the value of the setting we've registered with register_setting()
		$options = get_option('kunato_disable_title_modify');
		?>
		<input
			id="<?php echo esc_attr($args['label_for']); ?>"
			name="<?php echo esc_attr($args['label_for']); ?>"
			type="checkbox"
			<?php if($options == 'on') { ?>checked="checked"<?php } ?>
		/>
		<?php
	}

	/**
	 * Add the top level menu page.
	 * 
	 * @since    1.0.0
	 */
	public function kunato_options_page(){
		add_menu_page(
			'Kunato Settings',
			'Kunato',
			'manage_options',
			'kunato_options',
			array($this, 'kunato_options_page_html')
		);
	}


	/**
	 * Top level menu callback function
	 * 
	 * @since    1.0.0
	 */
	public function kunato_options_page_html()
	{
		// check user capabilities
		if (!current_user_can('manage_options')) {
			return;
		}

		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if (isset($_GET['settings-updated'])) {
			// add settings saved message with the class of "updated"
			add_settings_error('kunato_messages', 'kunato_message', __('Settings Saved', KUNATO_TEXT_DOMAIN), 'updated');
		}

		// show error/update messages
		settings_errors('kunato_messages');
	?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "kunatoplg"
				settings_fields('kunato_options');
				// output setting sections and their fields
				// (sections are registered for "kunatoplg", each field is registered to a specific section)
				do_settings_sections('kunato_options');
				// output save settings button
				submit_button(__('Save Settings', KUNATO_TEXT_DOMAIN));
				?>
			</form>
		</div>
	<?php
	}
}
