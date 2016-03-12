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
    
    public function testFeedly() {
        $url = 'http://cloud.feedly.com/v3/profile';
        $token = 'Az9LGdl3TwpM7hm0ElJzbKUYfDyFRotbKh_nEbmhokb3qliaptUsHQl7Bu93I2AhaTzlLX8KkUnONqKJT9UVCRv73MhBotctE7srWu-P6yFm1cvhzeMcVhQEsPkI5ej5-k916w7IL1tzW-NW0RaFs2N70V9vw3hVIEQCidbX1EFDGl86ATz9isAb4GkKb99R0YSQNdvyKZXX_TFu0dd2Re_wDreFxDU:feedlydev';
        
		$httpRequest = new HttpRequest($url);
        $httpRequest->setHeaderAuthToken($token);
		$ret = $httpRequest->exec();
        d($httpRequest->getResponseAsJson());
        d($httpRequest->getInfo());
        d($httpRequest->getError());
		$this->assertEquals($ret, true);
        
    }
}
