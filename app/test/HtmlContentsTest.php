<?php
require_once implode('/', [PATH_MODEL, 'Test.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'Mail.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Url.php']);

class HtmlContentsTest extends Test {
	public function test() {
		$htmlContents = new HtmlContents(new DirectoryBuilder());
		
		$filename = 'html_utf8.html';
		
		$url = 'http://php.net/manual/ja/language.namespaces.rationale.php';
		$html = $this->loadDat($filename);
		$setdto = 'nqmxt983_80@kindle.com';
		$from = 'raix5867@gmail.com';
		
		$htmlContents->fromText($url, $html);
//		$htmlContents->fromUrl($url);
		
		$htmlContents->bodyExtract();
		$htmlContents->loadImage();
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
