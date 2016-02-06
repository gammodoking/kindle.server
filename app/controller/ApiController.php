<?php

require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);
require_once implode('/', [PATH_MODEL, 'Kindle.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_VIEW, 'View.php']);

class ApiController extends Controller {
	
	private $result = [
		'result' => false,
		'message' => ''
	];
	
	public function sendHtmlAction() {
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: '';
		$url = @$_POST['url'] ?: '';
		$content = @$_POST['content'] ?: '';
		$imageEnabled = isset($_POST['imageEnabled']) && $_POST['imageEnabled'] === '1' ? true : false;
		
		try {
			$this->result['result'] = Service::sendHtmlToKindle($sendTo, $from, $url, $content, $imageEnabled);
		} catch (Exception $e) {
			$this->result['message'] = $e->getTraceAsString();
		}
		
		$this->render($this->result);
	}
	
	public function sendFileAction() {
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: '';
		$fileName = @$_POST['fileName'] ?: '';
		$file = @$_POST['file'] ?: '';
		
		try {
			$this->result['result'] = Service::sendFileToKindle($sendTo, $from, $fileName, $file);
		} catch (Exception $e) {
			$this->result['message'] = $e->getTraceAsString();
		}
		
		$this->render($this->result);
	}
	
	public function sendFeedAction() {
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: '';
		$urls = @$_POST['urls'] ?: '';
		
		try {
			$this->result['result'] = Service::sendFeedToKindle($sendTo, $from, $urls);
		} catch (Exception $e) {
			$this->result['message'] = $e->getTraceAsString();
		}
		
		$this->render($this->result);
	}
	
	private function render(array $result) {
		echo json_encode($result);
	}
	
}
