<?php

require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);
require_once implode('/', [PATH_MODEL, 'Kindle.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_VIEW, 'View.php']);

class ApiController extends Controller {
	
	private $result = [
		'result' => -1,
		'message' => ''
	];
	
	public function sendAction() {
		$url = @$_POST['url'] ?: '';
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: '';
		$content = @$_POST['content'] ?: '';
		$imageEnabled = isset($_POST['imageEnabled']) && $_POST['imageEnabled'] === '1' ? true : false;
		
		$this->result['result'] = Service::sendToKindle($url, $sendTo, $from, $imageEnabled, $content);
		$this->render($this->result);
	}
	
	private function render(array $result) {
		echo json_encode($result);
	}
	
}
