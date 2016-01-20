<?php

require_once implode('/', [PATH_MODEL, 'Kindle.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);

class ApiController extends Controller {
	
	private $result = [
		'result' => -1,
		'message' => ''
	];
	
	const API_KEY = 'jzolRY7PjodmRBHDOOukR8o9JhZrv8G0';
	
	public function sendAction() {
		$url = $_POST['url'];
		$sendTo = $_POST['sendTo'];
		$from = $_POST['from'];
		
		$body = $_POST['body'];
		$noImage = $_POST['noImage'];
		$appKey = $_POST['appKey'];
		
		if ($appKey !== self::API_KEY) {
			$this->result['result'] = '1';
			$this->result['message'] = 'invalid api key';
		} else {
			$this->result['result'] = Service::sendToKindle($url, $sendTo, $from);
		}
		$this->render($this->result);
	}
	
	private function render(array $result) {
		echo json_encode($result);
	}
	
}
