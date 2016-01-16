<?php
class Url_ {
	public $url;
	public $scheme;
	public $host;
	public $path;
	public $file;
	public $qeury;
	public $fragment;
	
	public static function parse($url) {
		return new Url($url);
	}
	
	/**
	 * srcやhrefの相対パスをbaseUrlを元に絶対URLに変換してUrlインスタンスを返す
	 * string
	 */
	public static function parseRelative($baseUrl, $targetUrl) {
		if (strpos($targetUrl, 'http') === 0) {
			return self::parse($targetUrl);
		}
		
		$baseUrlObj = self::parse($baseUrl);
		if (strpos($targetUrl, '/') === 0) {
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $targetUrl);
		}
		
		if (!$baseUrl) {
			throw new Exception();
		}

		if (strpos($href, './') === 0) {
			$src = substr($targetUrl, 2);
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $baseUrlObj->path . '/' . $src);
		} else {
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $baseUrlObj->path .  '/' . $targetUrl);
		}
		
		throw new Exception();
	}
	
	function __construct($url) {
		$this->url = $url;
		$this->scheme = parse_url($url, PHP_URL_SCHEME) ? parse_url($url, PHP_URL_SCHEME) . '://' : '';
		$this->host = parse_url($url, PHP_URL_HOST);
		$this->path = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_DIRNAME);
		$this->path = $this->path === '/' ? '' : $this->path;
		$this->file = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);
		$this->qeury = parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '';
		$this->fragment = parse_url($url, PHP_URL_FRAGMENT) ? '#' . parse_url($url, PHP_PHP_URL_FRAGMENTURL_QUERY) : '';
	}
}
