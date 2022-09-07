<?php
/**
 * @var string    $directory_path
 * @var array     $stats
 * @var null|bool $no_amounts
 */

$no_amounts = $no_amounts ?? false;
?>

<table class="widefat striped importers">
	<thead>
	<tr>
		<th scope="col">Statistic</th>

		<?php if ( ! $no_amounts ) : ?>
			<th scope="col">Amount</th>
		<?php endif; ?>

		<th scope="col">Customers</th>

		<?php if ( ! $no_amounts ) : ?>
			<th scope="col">Average</th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $stats as $period => $period_stats ) {
		if ( 'all-time' === $period ) {
			continue;
		}

		include $directory_path . '/views/stats-table-row.php';
	}
	?>
	</tbody>

	<?php if ( isset( $stats['all-time'] ) ) : ?>
		<tfoot>
		<?php
		$period_stats = $stats['all-time'];

		include $directory_path . '/views/stats-table-row.php';
		?>
		</tfoot>
	<?php endif; ?>
</table>
