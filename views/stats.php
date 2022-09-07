<?php
/**
 * @var string $directory_path
 */
?>

<div class="wrap">
	<h1 class="wp-heading-inline">Customer Stats</h1>

	<div id="poststuff">
		<div id="post-body">
			<div id="post-body-content">
				<h3>Table of contents</h3>
				<ul class="table-of-contents">
					<li><a href="#TotalSales">Total Sales</a></li>
					<li><a href="#CurrentSubscriptions">Current Subscriptions</a></li>
					<li><a href="#NewSubscriptions">New Subscriptions</a></li>
					<li><a href="#Renewals">Renewals</a></li>
					<li><a href="#Discounts">Discounts</a></li>
					<li><a href="#CompedFree">Comped (Free)</a></li>
					<li><a href="#Refunds">Refunds</a></li>
				</ul>

				<hr style="margin:20px 0;" />

				<div id="TotalSales">
					<?php include $directory_path . '/views/stats-sales.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="CurrentSubscriptions">
					<?php include $directory_path . '/views/stats-current-subscriptions.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="NewSubscriptions">
					<?php include $directory_path . '/views/stats-new-subscriptions.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="Renewals">
					<?php include $directory_path . '/views/stats-renewals.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="Discounts">
					<?php include $directory_path . '/views/stats-discounts.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="CompedFree">
					<?php include $directory_path . '/views/stats-comped.php'; ?>
				</div>

				<hr style="margin:20px 0;" />

				<div id="Refunds">
					<?php include $directory_path . '/views/stats-refunds.php'; ?>
				</div>
			</div>
			<!-- .post-body-content -->
		</div>
		<!-- .post-body -->
	</div><!-- #poststuff -->
</div>
