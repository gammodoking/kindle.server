<?php
require_once implode('/', [PATH_MODEL, 'ContentExtractor.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'Test.php']);

class ContentExtractorTest extends Test {
	public function test() {
		$url = 'http://semooh.jp/jquery/api/ajax/jQuery.ajax/options/';
		$url = 'http://dev.classmethod.jp/ria/google-chrome-extension-1/';
//		$content = file_get_contents($url);
		$html = $this->loadDat('html_utf8.html');
		
		$extractor = new ContentExtractor();
		$extractor->exec($html);
		
		$this->assertEquals(1, 1);
	}
}
