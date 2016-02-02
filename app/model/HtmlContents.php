<?php

class HtmlContents {
	
	private $rowContents;
	private $encodedContents;
	
	private $info;
	private $error;
	
	function __construct() {
	}
	
	public function fromText($html) {
		$this->encodedContents = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->rowContents = $html;
	}
	
	public function fromUrl($url) {
		//$this->result = file_get_contents($this->url);
		
//		ブログによっては403ではじかれる。ユーザーエージェント？IP？
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		//curl_setopt($ch, CURLOPT_HEADER, true);


		$headers = array(
		    "HTTP/1.0",
		    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		    "Accept-Encoding:gzip ,deflate",
		    "Accept-Language:ja,en-us;q=0.7,en;q=0.3",
		    "Connection:keep-alive",
		    "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0"
		    );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$this->rowContents = curl_exec($ch);
		$this->error = curl_error($ch);
		$this->info = curl_getinfo($ch);
		curl_close ($ch);

		$this->encodedContents = mb_convert_encoding($this->rowContents, 'HTML-ENTITIES', 'UTF-8');
	}
	
	public function encodedContents() {
		return $this->encodedContents;
	}
	
	public function info() {
		return $this->info;
	}
	
	public function error() {
		return $this->error;
	}
	
}
