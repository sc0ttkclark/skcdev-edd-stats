<?php
/**
 * Plugin Name: SKCDEV Easy Digital Download Stats
 * Plugin URI: https://skc.dev/
 * Description: Custom stats page for Easy Digital Downloads (EDD).
 * Version: 1.0.0
 * Author: SKC Development, LLC
 * Author URI: https://skc.dev/
 * License: GPL-2.0+
 * Text Domain: skcdev-edd-stats
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use SKCDEV\EDD_Stats\Plugin;

require_once __DIR__ . '/src/Plugin.php';

add_action( 'plugins_loaded', static function () {
	Plugin::instance( __FILE__ );
} );
