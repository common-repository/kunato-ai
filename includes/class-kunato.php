<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://kunato.ai/
 * @since      1.0.0
 *
 * @package    Kunato
 * @subpackage Kunato/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Kunato
 * @subpackage Kunato/includes
 * @author     Kunato <ms@kunato.io>
 */
class Kunato {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kunato_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'KUNATO_VERSION' ) ) {
			$this->version = KUNATO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'kunato';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Kunato_Loader. Orchestrates the hooks of the plugin.
	 * - Kunato_i18n. Defines internationalization functionality.
	 * - Kunato_Admin. Defines all hooks for the admin area.
	 * - Kunato_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kunato-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kunato-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kunato-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-kunato-public.php';

		$this->loader = new Kunato_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Kunato_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Kunato_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Kunato_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Register our kunatoplg_settings_init to the admin_init action hook.
		 */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'kunato_options_settings_init' );

		
		/**
		 * Register our kunato_options_page to the admin_menu action hook.
		 */
		$this->loader->add_action('admin_menu', $plugin_admin, 'kunato_options_page');
	}	

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Kunato_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 1 );

        // Inject custom script just after <body> tag
        $this->loader->add_action( 'wp_print_scripts', $plugin_public, 'script_head_injection', 1000 );

		// Inject custom script just before </body> tag
		$this->loader->add_action( 'wp_print_footer_scripts', $plugin_public, 'script_injection', 1000 );

		// Disabling HTML injection in title
		//  $this->loader->add_filter( 'the_title', $plugin_public, 'wrap_the_title', 10, 2 );

		$this->loader->add_action( 'wp_print_scripts', $plugin_public, 'script_injection_amp', 1000 );
		$this->loader->add_filter( 'wp_kses_allowed_html', $plugin_public, 'filter_kses_data', 10, 2 );

		// This filter is not working
		//$this->loader->add_filter( 'ampforwp_modify_title_tag', $plugin_public, 'wrap_the_title_amp' );
		
		/* Wrap title filters for AMP blue */
		$this->loader->add_filter( 'ampforwp_filter_single_title', $plugin_public, 'wrap_single_post_title_amp' );
		$this->loader->add_filter( 'ampforwp_prev_link', $plugin_public, 'wrap_single_post_title_amp' );
		$this->loader->add_filter( 'ampforwp_next_link', $plugin_public, 'wrap_single_post_title_amp' );


		/* Add <amp-script> tag */
		$this->loader->add_action( 'ampforwp_body_beginning', $plugin_public, 'add_amp_tag_body_beginning');
		$this->loader->add_action( 'amp_post_template_footer', $plugin_public, 'add_amp_tag_body_end');
		$this->loader->add_action( 'amp_post_template_head', $plugin_public, 'add_amp_sha_meta');
		$this->loader->add_action( 'amp_post_template_head', $plugin_public, 'add_amp_styles');
		$this->loader->add_action( 'amp_post_template_footer', $plugin_public, 'add_amp_script');

		
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Kunato_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
