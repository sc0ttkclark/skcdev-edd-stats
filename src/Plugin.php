<?php

namespace SKCDEV\EDD_Stats;

/**
 * Plugin specific functionality.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Constant that stores the current plugin version.
	 *
	 * @since 1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * Constant that stores the minimum supported PHP version.
	 *
	 * @since 1.0.0
	 */
	const MIN_PHP_VERSION = '7.2';

	/**
	 * Constant that stores the minimum supported WP version.
	 *
	 * @since 1.0.0
	 */
	const MIN_WP_VERSION = '5.8';

	/**
	 * Constant that stores the minimum supported EDD version.
	 *
	 * @since 1.0.0
	 */
	const MIN_EDD_VERSION = '3.0';

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = '';

	/**
	 * The plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_file = '';

	/**
	 * Plugin directory URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_dir_url = '';

	/**
	 * Plugin directory path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_dir_path = '';

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file The plugin file.
	 */
	private function __construct( string $plugin_file ) {
		// Set the plugin name.
		$this->plugin_name = __( 'SKCDEV Easy Digital Downloads Add-On', 'skcdev-edd-stats' );


		// Store the plugin directory URL for assets usage later.
		$this->plugin_file     = $plugin_file;
		$this->plugin_dir_url  = untrailingslashit( plugin_dir_url( $this->plugin_file ) );
		$this->plugin_dir_path = untrailingslashit( plugin_dir_path( $this->plugin_file ) );

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Setup and get the instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $plugin_file The plugin file.
	 *
	 * @return self The instance of the class.
	 */
	public static function instance( string $plugin_file = null ) : self {
		if ( ! self::$instance ) {
			self::$instance = new self( $plugin_file );
		}

		return self::$instance;
	}

	/**
	 * Handle init of plugin functionality.
	 *
	 * @since 1.0.0
	 */
	public function init() : void {
		global $wp_version;

		$requirements = [
			[
				'check'   => $wp_version && version_compare( self::MIN_WP_VERSION, $wp_version, '<=' ),
				// translators: %1$s: The WordPress version number, %2$s: The plugin name.
				'message' => sprintf( __( 'You need WordPress %1$s+ installed in order to use the %2$s.', 'skcdev-edd-stats' ), self::MIN_WP_VERSION, $this->plugin_name ),
			],
			[
				'check'   => version_compare( self::MIN_PHP_VERSION, PHP_VERSION, '<=' ),
				// translators: %1$s: The PHP version number, %2$s: The plugin name.
				'message' => sprintf( __( 'You need PHP %1$s+ installed in order to use the %2$s.', 'skcdev-edd-stats' ), self::MIN_PHP_VERSION, $this->plugin_name ),
			],
			[
				'check'   => defined( 'EDD_VERSION' ) && version_compare( self::MIN_EDD_VERSION, EDD_VERSION, '<=' ),
				// translators: %1$s: The Pods version number, %2$s: The plugin name.
				'message' => sprintf( __( 'You need Easy Digital Downloads %1$s+ installed and activated in order to use the %2$s.', 'skcdev-edd-stats' ), self::MIN_EDD_VERSION, $this->plugin_name ),
			],
		];

		// Check if this add-on should load.
		if ( ! $this->check_requirements( $requirements ) ) {
			return;
		}

		require_once 'Stats.php';

		Stats::instance()->hook();
	}

	/**
	 * Check whether the requirements were met.
	 *
	 * @since 1.0.0
	 *
	 * @param array $requirements List of requirements.
	 *
	 * @return bool Whether the requirements were met.
	 */
	public function check_requirements( array $requirements ) : bool {
		foreach ( $requirements as $requirement ) {
			// Check if requirement passed.
			if ( $requirement['check'] ) {
				continue;
			}

			// Show admin notice if there's a message to be shown.
			if ( ! empty( $requirement['message'] ) && $this->should_show_notices() ) {
				printf(
					'
						<div id="message" class="error fade">
							<p>%s</p>
						</div>
					',
					esc_html( $requirement['message'] )
				);
			}

			return false;
		}

		return true;
	}

	/**
	 * Check whether we should show notices.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether we should show notices.
	 */
	public function should_show_notices() : bool {
		global $pagenow;

		// We only show notices on admin pages.
		if ( ! is_admin() ) {
			return false;
		}

		// We only show on the plugins.php page.
		if ( 'plugins.php' !== $pagenow ) {
			return false;
		}

		return true;
	}

}
