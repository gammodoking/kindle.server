<?php

require_once implode('/', [PATH_MODEL, 'Kindle.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);

class ApiController extends Controller {
	
	private $result = [
		'result' => -1,
		'message' => ''
	];
	
	public function sendAction() {
		$url = $_POST['url'];
		$sendTo = $_POST['sendTo'];
		$from = $_POST['from'];
		
		$this->result['result'] = Service::sendToKindle($url, $sendTo, $from);
		$this->render($this->result);
	}
	
	private function render(array $result) {
		echo json_encode($result);
	}
	
}
