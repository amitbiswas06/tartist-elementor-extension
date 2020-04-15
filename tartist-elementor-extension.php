<?php
/*
Plugin Name: Tartist Elementor Extension
Plugin URI: https://github.com/amitbiswas06/tartist-elementor-extension
Description: Extendes Elementor with custom widgets.
Version: 1.0.0
Author: Amit Biswas
Author URI: https://templateartist.com
License: GPLv2 and later
Text Domain: tartist-elementor-extension
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Tartist_Elementor_Extension {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.8.5';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Tartist_Elementor_Extension The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Tartist_Elementor_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'tartist-elementor-extension' );

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );

		// Register Widget Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );

		//hook for creating custom categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'tartistElementor__widget_categories' ] );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'tartist-elementor-extension' ),
			'<strong>' . esc_html__( 'Tartist Elementor Extension', 'tartist-elementor-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'tartist-elementor-extension' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'tartist-elementor-extension' ),
			'<strong>' . esc_html__( 'Tartist Elementor Extension', 'tartist-elementor-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'tartist-elementor-extension' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'tartist-elementor-extension' ),
			'<strong>' . esc_html__( 'Tartist Elementor Extension', 'tartist-elementor-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'tartist-elementor-extension' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Adding custom widget category
	 */
	public function tartistElementor__widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'tartist-widgets',
			[
				'title' => __( 'TemplateArtist', 'tartist-elementor-extension' ),
				'icon' => 'fas fa-plug',
			]
		);
	
	}


	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {

		/**
		 * Posts Widget
		 */
		//Include Posts Widget File 
		require_once( __DIR__ . '/widgets/tartist-posts-widget.php' );
		// Register posts widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Tartist_Elementor_Posts_Widget() );		

	}

	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_controls() {

		// Include Control files
		/*
		* require_once( __DIR__ . '/controls/tartist-control.php' );
		*/

		// Register control
		/**
		 * \Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );
		 */
		

	}

	/**
	 * Init Styles
	 *
	 * Register style files
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function widget_styles() {

		/**
		 * widget styles goes here
		 * wp_register_style( 'widget-1', plugins_url( 'css/widget-1.css', __FILE__ ) );
		 * wp_register_style( 'widget-2', plugins_url( 'css/widget-2.css', __FILE__ ) );
		 */

	}

}

Tartist_Elementor_Extension::instance();


/**
 * 1. enqueue the required css/js
 */
function tartistElementor__enqueue_scripts(){
    wp_enqueue_style( 'tartistElementor-style', plugins_url( 'style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'tartistElementor__enqueue_scripts' );

/**
 * Retrieve posts by post type and make array with post_id and post_title
 */
function tartistElementeor__posts_array( $post_type = 'post' ){
	//post_type required
	$get_all_posts = get_posts( array(
		'post_type'     => esc_attr($post_type),
		'post_status'   => 'publish',
		'numberposts'   => -1
	) );

	$posts = array();

	foreach( $get_all_posts as $newPosts ){
		$posts[$newPosts->ID] = esc_html($newPosts->post_title);
	}

	return $posts;

}

/**
 * Retrieve taxonomy terms based on taxonomy
 */
function tartistElementeor__terms_array( $taxonomy = 'category', $slug = false ){
	//taxonomy required
	$all_terms = get_terms( array(
		'taxonomy' => $taxonomy,
		'hide_empty' => false
	) );

	$terms = array();

	if( !$slug ){
		foreach( $all_terms as $newTerms ){
			$terms[$newTerms->term_id] = esc_html($newTerms->name);
		}
	}
	if( $slug ){
		foreach( $all_terms as $newTerms ){
			$terms[$newTerms->slug] = esc_html($newTerms->name);
		}
	}

	return $terms;

}

?>