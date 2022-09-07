<?php
/**
 * @var string    $directory_path
 * @var array     $period_stats
 * @var null|bool $no_amounts
 */

$total_amount = '$' . number_format_i18n( $period_stats['total_amount'], 2 );
$total_amount = str_replace( '$-', '-$', $total_amount );

$avg_amount_per_customer = '$' . number_format_i18n( $period_stats['avg_amount_per_customer'], 2 );
$avg_amount_per_customer = str_replace( '$-', '-$', $avg_amount_per_customer );
?>

<tr>
	<td scope="row" class="row-title"><strong><?php echo esc_html( $period_stats['label'] ); ?></strong></td>

	<?php if ( ! $no_amounts ) : ?>
		<td><?php echo esc_html( $total_amount ); ?></td>
	<?php endif; ?>

	<td><?php echo esc_html( sprintf( '%s %s', number_format_i18n( $period_stats['total_customers'], 0 ), _n( 'customer', 'customers', $period_stats['total_customers'] ) ) ); ?></td>

	<?php if ( ! $no_amounts ) : ?>
		<td><?php echo esc_html( $avg_amount_per_customer ); ?> / customer</td>
	<?php endif; ?>
</tr>
