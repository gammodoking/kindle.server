<?php
class HttpRequest {
	
	/**
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 *
	 * @var string
	 */
	private $response;
	
	/**
	 *
	 * @var string
	 */
	private $methodName;
	
//	private $headers = [
//		"HTTP/1.0",
//		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
//		"Accept-Encoding:gzip ,deflate",
//		"Accept-Language:ja,en-us;q=0.7,en;q=0.3",
//		"Connection:keep-alive",
//		"User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0"
//	];

	private $headers = [
		'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Encoding' => 'gzip ,deflate',
		'Accept-Language' => 'ja,en-us;q=0.7,en;q=0.3',
		'Connection' => 'keep-alive',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36',
//        'User-Agent' => 'Mozilla/5.0 (Linux; U; Android 2.3.2; ja-jp; SonyEricssonSO-01C Build/3.0.D.2.79) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
	];
	
	/**
	 *
	 * @var array
	 */
	private $info;
	
	/**
	 *
	 * @var array
	 */
	private $error;
	
	public function __construct($url) {
		$this->url = $url;
	}
    
    public function setHeaderAuthToken($token) {
        $this->headers['Authorization'] = sprintf('OAuth %s', $token);
    }
	
	public function setMethod($methodName) {
		$this->methodName = $methodName;
	}
	
	public function exec() {
//		ブログによっては403ではじかれる。ユーザーエージェント？IP？
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->createHeader());
//		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->getCookiePath());
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->getCookiePath());
		

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		$this->response = curl_exec($ch);
		if ($this->response) {
			$this->response = mb_convert_encoding($this->response, 'UTF-8', 'UTF-8, CP51932, EUC-win, SJIS-win, ASCII');
		}
		$this->error = curl_error($ch);
		$this->info = curl_getinfo($ch);
		curl_close ($ch);
		
		return $this->info['http_code'] === 200;
	}
    
    private function createHeader() {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = implode(':', [$key, $value]);
        }
        return $headers;
    }
	
	public function getResponseCode() {
		return $this->info['http_code'];
	}
	
	public function getResponse() {
		return $this->response;
	}
    
	public function getResponseAsJson() {
		return json_decode($this->response);
	}
	
	public function getHtmlEntitiesEncodedResponse() {
		return mb_convert_encoding($this->response, 'HTML-ENTITIES', 'UTF-8');
	}
	
	public function getInfo() {
		return $this->info;
	}
	
	public function getError() {
		return $this->error;
	}
	
	private function getCookiePath() {
		return PATH_VAR . '/cookie.txt';
	}
}