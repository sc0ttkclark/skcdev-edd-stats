<?php

namespace SKCDEV\EDD_Stats;

/**
 * Stats page functionality.
 *
 * @since 1.0.0
 */
class Stats {

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Nothing to see here.
	}

	/**
	 * Handle adding hooks.
	 */
	public function hook() : void {
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 11 );
	}

	/**
	 * Handle removing hooks.
	 */
	public function unhook() : void {
		remove_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Setup and get the instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return self The instance of the class.
	 */
	public static function instance() : self {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Handle admin init of stats functionality.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() : void {
		$give_tools_page = add_submenu_page(
			'edit.php?post_type=download',
			__( 'Customer Stats', 'skcdev-edd-stats' ),
			__( 'Customer Stats', 'skcdev-edd-stats' ),
			'manage_options',
			'skcdev-edd-stats',
			[ $this, 'render_page' ]
		);
	}

	public function render_page() : void {
		$directory_path = Plugin::instance()->plugin_dir_path;

		include_once $directory_path . '/views/stats.php';
	}

	public function render_table( array $stats, bool $no_amounts = false ) : void {
		$directory_path = Plugin::instance()->plugin_dir_path;

		include $directory_path . '/views/stats-table.php';
	}

	public function get_historical_stats( array $args = [] ) : array {
		/** @var $wpdb wpdb */ global $wpdb;

		$stats = [
			'last-7-days'    => [
				'label'                => 'Last 7 days',
				'created_since_period' => date_i18n( 'Y-m-d', strtotime( '-7 days' ) ),
				'created_end_period'   => null,
			],
			'last-30-days'   => [
				'label'                => 'Last 30 days',
				'created_since_period' => date_i18n( 'Y-m-d', strtotime( '-30 days' ) ),
				'created_end_period'   => null,
			],
			'last-3-months'  => [
				'label'                => 'Last 3 months',
				'created_since_period' => date_i18n( 'Y-m-d', strtotime( '-3 months' ) ),
				'created_end_period'   => null,
			],
			'last-6-months'  => [
				'label'                => 'Last 6 months',
				'created_since_period' => date_i18n( 'Y-m-d', strtotime( '-6 months' ) ),
				'created_end_period'   => null,
			],
			'last-12-months' => [
				'label'                => 'Last 12 months',
				'created_since_period' => date_i18n( 'Y-m-d', strtotime( '-12 months' ) ),
				'created_end_period'   => null,
			],
		];

		$sql = '
			SELECT
				YEAR( orders.date_created )
			FROM pgp_edd_orders AS orders
			ORDER BY orders.date_created ASC
			LIMIT 1
		';

		$current_year = (int) date_i18n( 'Y' );
		$since_year   = (int) $this->cached_get_var( $sql );

		for ( $x = $current_year; $since_year <= $x; $x -- ) {
			$year_label = 'Year: ' . $x;

			$stats[ 'year-' . $x ] = [
				'label'                => $year_label,
				'created_since_period' => $x . '-01-01',
				'created_end_period'   => $x . '-12-31',
			];
		}

		$stats['all-time'] = [
			'label' => 'Store total',
		];

		/**
		 * Allow filtering the historical stats periods used.
		 *
		 * @since 1.0.0
		 *
		 * @param array $stats The stats periods.
		 * @param array $args  The stats args used.
		 */
		$stats = apply_filters( 'skcdev_edd_stats_historical_stats_periods', $stats, $args );

		foreach ( $stats as $key => $period ) {
			$stats_args = array_merge( $period, $args );

			$period_stats = $this->get_order_stats( $stats_args );

			$period['total_amount']              = $period_stats['total_amount'];
			$period['total_discount']            = $period_stats['total_discount'];
			$period['total_orders']              = $period_stats['total_orders'];
			$period['total_customers']           = $period_stats['total_customers'];
			$period['avg_amount_per_customer']   = $period_stats['avg_amount_per_customer'];
			$period['avg_amount_per_order']      = $period_stats['avg_amount_per_order'];
			$period['avg_discount_per_customer'] = $period_stats['avg_discount_per_customer'];
			$period['avg_discount_per_order']    = $period_stats['avg_discount_per_order'];

			$stats[ $key ] = $period;
		}

		return $stats;
	}

	public function get_order_stats( array $args ) : array {
		/** @var $wpdb wpdb */ global $wpdb;

		$sql = '
			SELECT
				SUM( CAST( edd_order.total AS DECIMAL( 10, 2 ) ) ) AS total_amount,
				SUM( CAST( edd_order.discount AS DECIMAL( 10, 2 ) ) ) AS total_discount,
	            COUNT( DISTINCT edd_order.id ) AS total_orders,
	            COUNT( DISTINCT edd_order.customer_id ) AS total_customers
			FROM pgp_edd_orders AS edd_order
			LEFT JOIN pgp_edd_subscriptions AS edd_subscription
				ON edd_subscription.customer_id = edd_order.customer_id
		';

		$where         = [];
		$prepared_args = [];

		if ( ! empty( $args['order_status'] ) ) {
			$where[] = '
				edd_order.status IN ( ' . implode( ', ', array_fill( 0, count( (array) $args['order_status'] ), '%s' ) ) . ' )
			';

			$prepared_args = array_merge( $prepared_args, (array) $args['order_status'] );
		}

		if ( ! empty( $args['order_type'] ) ) {
			$where[] = '
				edd_order.type IN ( ' . implode( ', ', array_fill( 0, count( (array) $args['order_type'] ), '%s' ) ) . ' )
			';

			$prepared_args = array_merge( $prepared_args, (array) $args['order_type'] );
		}

		if ( ! empty( $args['subscription_status'] ) ) {
			$where[] = '
				edd_subscription.status IN ( ' . implode( ', ', array_fill( 0, count( (array) $args['subscription_status'] ), '%s' ) ) . ' )
			';

			$prepared_args = array_merge( $prepared_args, (array) $args['subscription_status'] );
		}

		if ( ! empty( $args['created_since_period'] ) ) {
			$where[] = '
				%s <= edd_order.date_created
			';

			$prepared_args[] = $args['created_since_period'];
		}

		if ( ! empty( $args['created_end_period'] ) ) {
			$where[] = '
				edd_order.date_created <= %s
			';

			$prepared_args[] = $args['created_end_period'];
		}

		if ( isset( $args['comped'] ) ) {
			if ( ! empty( $args['comped'] ) ) {
				$where[] = '
					edd_order.total = 0
				';
			} else {
				$where[] = '
					edd_order.total != 0
				';
			}
		}

		if ( isset( $args['discounted'] ) ) {
			if ( ! empty( $args['discounted'] ) ) {
				$where[] = '
					edd_order.discount != 0
				';
			} else {
				$where[] = '
					edd_order.discount = 0
				';
			}
		}

		if ( ! empty( $where ) ) {
			$sql .= '
				WHERE
				' . implode( "\n AND ", $where ) . '
			';
		}

		if ( ! empty( $prepared_args ) ) {
			$sql = $wpdb->prepare( $sql, $prepared_args );
		}

		$result = $wpdb->get_row( $sql );

		$stats = [
			'total_amount'              => 0,
			'total_discount'            => 0,
			'total_orders'              => 0,
			'total_customers'           => 0,
			'avg_amount_per_customer'   => 0,
			'avg_amount_per_order'      => 0,
			'avg_discount_per_customer' => 0,
			'avg_discount_per_order'    => 0,
		];

		if ( ! empty( $result ) ) {
			$stats['total_amount']    = (int) $result->total_amount;
			$stats['total_discount']  = (int) $result->total_discount;
			$stats['total_orders']    = (int) $result->total_orders;
			$stats['total_customers'] = (int) $result->total_customers;

			if ( 0 !== (int) $result->total_customers && 0 !== (int) $result->total_orders ) {
				if ( 0 !== (int) $result->total_amount ) {
					$stats['avg_amount_per_customer'] = max( abs( $result->total_amount / $result->total_customers ), 0 );
					$stats['avg_amount_per_order']    = max( abs( $result->total_amount / $result->total_orders ), 0 );
				}

				if ( 0 !== (int) $result->total_discount ) {
					$stats['avg_discount_per_customer'] = max( abs( $result->total_discount / $result->total_customers ), 0 );
					$stats['avg_discount_per_order']    = max( abs( $result->total_discount / $result->total_orders ), 0 );
				}
			}

			// Refunds should have negative numbers.
			if ( ! empty( $args['order_type'] ) && 'refund' === $args['order_type'] ) {
				$stats['avg_amount_per_customer']   *= - 1;
				$stats['avg_amount_per_order']      *= - 1;
				$stats['avg_discount_per_customer'] = 0;
				$stats['avg_discount_per_order']    = 0;
			}
		}

		return $stats;
	}

	public function get_subscription_stats( array $args ) : array {
		/** @var $wpdb wpdb */ global $wpdb;

		$sql = '
			SELECT
				SUM( CAST( edd_subscription.initial_amount AS DECIMAL( 10, 2 ) ) ) AS total_initial_amount,
				SUM( CAST( edd_subscription.recurring_amount AS DECIMAL( 10, 2 ) ) ) AS total_recurring_amount,
	            COUNT( DISTINCT edd_subscription.customer_id ) AS total_customers
			FROM pgp_edd_subscriptions AS edd_subscription
		';

		$where         = [];
		$prepared_args = [];

		if ( ! empty( $args['subscription_status'] ) ) {
			$where[] = '
				edd_subscription.status IN ( ' . implode( ', ', array_fill( 0, count( (array) $args['subscription_status'] ), '%s' ) ) . ' )
			';

			$prepared_args = array_merge( $prepared_args, (array) $args['subscription_status'] );
		}

		if ( ! empty( $args['created_since_period'] ) ) {
			$where[] = '
				%s <= edd_subscription.created
			';

			$prepared_args[] = $args['created_since_period'];
		}

		if ( ! empty( $args['created_end_period'] ) ) {
			$where[] = '
				edd_subscription.created <= %s
			';

			$prepared_args[] = $args['created_end_period'];
		}

		if ( isset( $args['comped'] ) ) {
			if ( ! empty( $args['comped'] ) ) {
				$where[] = '
					edd_subscription.recurring_amount = 0
				';
			} else {
				$where[] = '
					edd_subscription.recurring_amount != 0
				';
			}
		}

		if ( isset( $args['expired'] ) ) {
			if ( ! empty( $args['expired'] ) ) {
				$where[] = '
					edd_subscription.expiration < NOW()
				';
			} else {
				$where[] = '
					NOW() <= edd_subscription.expiration
				';
			}
		}

		if ( ! empty( $where ) ) {
			$sql .= '
				WHERE
				' . implode( "\n AND ", $where ) . '
			';
		}

		if ( ! empty( $prepared_args ) ) {
			$sql = $wpdb->prepare( $sql, $prepared_args );
		}

		$result = $wpdb->get_row( $sql );

		$stats = [
			'total_initial_amount'   => 0,
			'total_recurring_amount' => 0,
			'total_customers'        => 0,
		];

		if ( ! empty( $result ) ) {
			$stats['total_initial_amount']   = (int) $result->total_initial_amount;
			$stats['total_recurring_amount'] = (int) $result->total_recurring_amount;
			$stats['total_customers']        = (int) $result->total_customers;

			if ( 0 !== (int) $result->total_customers ) {
				if ( 0 !== (int) $result->total_initial_amount ) {
					$stats['avg_initial_amount_per_customer'] = max( abs( $result->total_initial_amount / $result->total_customers ), 0 );
				}

				if ( 0 !== (int) $result->total_recurring_amount ) {
					$stats['avg_recurring_amount_per_customer'] = max( abs( $result->total_recurring_amount / $result->total_customers ), 0 );
				}
			}
		}

		return $stats;
	}

	public function cached_get_var() : ?string {
		$args = func_get_args();

		$md5 = md5( wp_json_encode( $args ) );

		$cached = wp_cache_get( $md5, 'skcdev-edd-stats-var' );

		if ( ! empty( $cached ) ) {
			return $cached;
		}

		global $wpdb;

		$var = $wpdb->get_var( ...$args );

		wp_cache_set( $md5, $var, 'skcdev-edd-stats-var', HOUR_IN_SECONDS * 12 );

		return $var;
	}

}
