<?php

class Request {
	
	function __construct() {
	}
	
	public function getHost() {
		return $_SERVER['HTTP_HOST'];
		return filter_input(INPUT_SERVER, 'SERVER_NAME');
//		return filter_input(INPUT_SERVER, 'HTTP_HOST');
	}
	
	public function server($key) {
		return $_SERVER[$key];
//		return filter_input(INPUT_SERVER, $key);
	}
}