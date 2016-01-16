<?php

require_once implode('/', [PATH_MODEL, 'Kindle.php']);


class ContentExtractorTest extends Test {
	public function test() {
		$url = 'http://semooh.jp/jquery/api/ajax/jQuery.ajax/options/';
		$url = 'http://dev.classmethod.jp/ria/google-chrome-extension-1/';
		$content = file_get_contents($url);

		$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
		$extractor = new ContentExtractor($content);
		$extractor->exec();
		
		$this->assertEquals(0, 1);
	}
}
