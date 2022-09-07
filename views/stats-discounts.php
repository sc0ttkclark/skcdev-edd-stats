<?php

use SKCDEV\EDD_Stats\Stats;

$stats_object = Stats::instance();

$stats = $stats_object->get_historical_stats( [
	'order_type'          => 'sale',
	'order_status'        => [
		'complete',
		'partially_refunded',
	],
	'subscription_status' => [
		'active',
		'cancelled',
		'expired',
	],
	'discounted'          => true,
	'comped'              => false,
] );
?>

<h3>Discounts</h3>

<?php $stats_object->render_table( $stats ); ?>
