<?php

require_once implode('/', [PATH_CORE_CLASS, 'Request.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Url.php']);

class FrontController {
	private $routing = [
		'/' => ['IndexController', 'index'],
		'/send' => ['IndexController', 'send'],
		'/test' => ['IndexController', 'test'],
		'/api/sendToKindle' => ['ApiController', 'send'],
	];
	
	public function exec() {
		$request = new Request();
		$url = new Url_($request->server('REQUEST_URI'));
		$path = $url->path . '/' . $url->file;
		d($url);
		if (!isset($this->routing[$path])) {
			throw new Exception('routing not found');
		}
		$rounting = $this->routing[$path];
		
		// Controllerをrequire
		require_once implode('/', [PATH_CONTROLLER, $rounting[0] . '.php']);
		$controller = $rounting[0];
		$action = $rounting[1];
		
		// actionを実行
		$class = new ReflectionClass($controller);
		$instance = $class->newInstance($request);
		$reflMethod = new ReflectionMethod($controller, $action . 'Action');
		$reflMethod->invoke($instance);

	}
}