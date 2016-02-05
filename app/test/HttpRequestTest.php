<?php

require_once implode('/', [PATH_MODEL, 'Test.php']);
require_once implode('/', [PATH_MODEL, 'HttpRequest.php']);
require_once implode('/', [PATH_CORE_CLASS, 'Url.php']);

class HttpRequestTest extends Test {
	
	const URL_EUC_JP = 'http://chofu.com/';
	const URL_UTF8 = 'http://qiita.com/hkomo746/items/0418be9be922aecd6ec1';
	
	public function test() {
		
		$httpRequest = new HttpRequest(self::URL_UTF8);
		$ret = $httpRequest->exec();
		$this->assertEquals($ret, true);
		
		$httpRequest = new HttpRequest(self::URL_EUC_JP);
		$ret = $httpRequest->exec();
		$this->assertEquals($ret, true);
		
	}
}
