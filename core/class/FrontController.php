<?php

require_once implode('/', [PATH_CORE_CLASS, 'Request.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Url.php']);

class FrontController {
	private $routing = [
		'/' => ['IndexController', 'index'],
		'/send' => ['IndexController', 'send'],
		'/test' => ['IndexController', 'test'],
		'/api/sendHtml' => ['ApiController', 'sendHtml'],
		'/api/sendFile' => ['ApiController', 'sendFile'],
		'/api/sendFeed' => ['ApiController', 'sendFeed'],
	];
	
	public function exec() {
		try {
			$request = new Request();
			$url = new Url($request->server('REQUEST_URI'));
			$path = $url->path . '/' . $url->file;
			if (!isset($this->routing[$path])) {
    			d('' . $url);
                return;
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
		} catch (Exception $e) {
			d('####### 処理されない例外 #######');
			d($e);
		}

	}
}