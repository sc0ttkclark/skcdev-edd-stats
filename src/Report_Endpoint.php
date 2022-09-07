<?php

namespace SKCDEV\EDD_Stats;

use EDD\Reports\Data\Endpoint;

class Report_Endpoint extends Endpoint {

	/**
	 * Endpoint view (type).
	 *
	 * @var string
	 */
	protected $view = 'table';

	public function __construct() {
		parent::__construct( [] );
	}

	public function get_data() {
		return [];
	}

	public function display() {
		Stats::instance()->render_page();
	}

}
