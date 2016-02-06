<?php

require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_VIEW, 'View.php']);

class IndexController extends Controller {
	
	public function indexAction() {
		$this->request->getHost();
		
		$url = 'http://semooh.jp/jquery/api/ajax/jQuery.ajax/options/';
		$url = @$_GET['url'];
		
		$sendTo = 'nqmxt983_80@kindle.com';
		$sendTo = @$_COOKIE["send_to"];
		
		$scriptUrl = sprintf('http://%s/', $this->request->getHost());
		$bookmarklet = sprintf("javascript: location.href='%s?url=' + encodeURIComponent(document.location.href);", $scriptUrl);
		
		$content = sprintf('
<form method="post" action="send">
<label for="url">url</label><input name="url" id="url" type="text" value="%s"><br />
<label for="sendTo">send to</label><input name="sendTo" id="sendTo" type="text" value="%s"><br />
<button type="submit">送信</button>
</form>
<a href="%s">kindle</a>
		', $url, $sendTo, $bookmarklet);
		View::render($content);
	}
	
	public function sendAction() {
		$url = @$_POST['url'] ?: '';
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: 'raix5867@gmail.com';

		if ($sendTo) {
			setcookie('send_to', $sendTo, time() + 60 * 60 * 24 * 30 * 365);
		}
		try {
			$ret = Service::sendHtmlToKindle($sendTo, $from, $url);
			if ($ret) {
				View::render("送信しました<br />" );
			} else {
				View::render('失敗しました');
			}
		} catch (Exception $e) {
			$e->getTraceAsString();
			View::render('失敗しました<br />' . $e->getTraceAsString());
		}
	}
}
