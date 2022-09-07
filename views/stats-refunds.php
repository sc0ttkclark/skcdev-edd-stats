<?php

use SKCDEV\EDD_Stats\Stats;

$stats_object = Stats::instance();

$stats = $stats_object->get_historical_stats( [
	'order_type'          => 'refund',
	'order_status'        => 'complete',
	'subscription_status' => [
		'active',
		'cancelled',
		'expired',
	],
	'comped'              => false,
] );
?>

<h3>Refunds</h3>

<?php $stats_object->render_table( $stats ); ?>
