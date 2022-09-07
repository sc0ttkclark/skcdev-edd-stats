<?php

use SKCDEV\EDD_Stats\Stats;

$stats_object = Stats::instance();

$active_stats = $stats_object->get_subscription_stats( [
	'subscription_status' => [
		'active',
	],
	'expired'             => false,
	'comped'              => false,
] );

$cancelled_stats = $stats_object->get_subscription_stats( [
	'subscription_status' => [
		'cancelled',
	],
	'expired'             => false,
	'comped'              => false,
] );

$comped_stats = $stats_object->get_subscription_stats( [
	'subscription_status' => [
		'active',
		'cancelled',
	],
	'expired'             => false,
	'comped'              => true,
] );

$stats = [
	'active'    => [
		'label'                   => 'Active',
		'total_amount'            => $active_stats['total_recurring_amount'],
		'total_customers'         => $active_stats['total_customers'],
		'avg_amount_per_customer' => $active_stats['avg_recurring_amount_per_customer'],
	],
	'cancelled' => [
		'label'                   => 'Cancelled',
		'total_amount'            => $cancelled_stats['total_recurring_amount'],
		'total_customers'         => $cancelled_stats['total_customers'],
		'avg_amount_per_customer' => $cancelled_stats['avg_recurring_amount_per_customer'],
	],
	'comped'    => [
		'label'                   => 'Comped',
		'total_amount'            => $comped_stats['total_recurring_amount'],
		'total_customers'         => $comped_stats['total_customers'],
		'avg_amount_per_customer' => $comped_stats['avg_recurring_amount_per_customer'],
	],
	'all-time'  => [
		'label'                   => 'Store total (non-comped)',
		'total_amount'            => $active_stats['total_recurring_amount'] + $cancelled_stats['total_recurring_amount'],
		'total_customers'         => $active_stats['total_customers'] + $cancelled_stats['total_customers'],
		'avg_amount_per_customer' => $active_stats['avg_recurring_amount_per_customer'] + $cancelled_stats['avg_recurring_amount_per_customer'],
	],
];
?>

<h3>Current Subscriptions</h3>

<?php $stats_object->render_table( $stats ); ?>

