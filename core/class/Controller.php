<?php
require_once implode('/', [PATH_CORE_CLASS, 'Request.php']);

class Controller {
	/**
	 *
	 * @var Request
	 */
	protected $request;
	
	function __construct(Request $request) {
		$this->request = $request;
	}
}