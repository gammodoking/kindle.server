<?php
require_once implode('/', [PATH_MODEL, 'Test.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'Mail.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Url.php']);

class HtmlContentsTest extends Test {
	public function test() {
		
		$filename = 'html_utf8.html';
		$html = $this->loadDat($filename);
		$url = 'http://php.net/manual/ja/language.namespaces.rationale.php';
		$setdto = 'nqmxt983_80@kindle.com';
		$from = 'raix5867@gmail.com';
		
		$htmlContents = new HtmlContents(new DirectoryBuilder());
		$htmlContents->fromText($url, $html);
//		$htmlContents->fromUrl($url);
		
		$kindleFile = $htmlContents->convertToKindleFile();
		
		$mail = new Mail();
		$mail->setSendto($setdto);
		$mail->setFileName('kindle.mobi');
		$mail->setFrom($from);
		$mail->setFile($kindleFile);
		$ret = $mail->send();
		d('' . $mail);

		d($ret);
		
		$htmlContents->destroy();
		
		$this->assertEquals(1, 1);
	}
}
