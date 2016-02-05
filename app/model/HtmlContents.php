<?php

require_once implode('/', [PATH_MODEL, 'ImageDownloader.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'HttpRequest.php']);
require_once implode('/', [PATH_MODEL, 'KindleGenCommand.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'ContentsNormalizer.php']);
require_once implode('/', [PATH_MODEL, 'ContentExtractor.php']);

class HtmlContents {
	
	/**
	 *
	 * @var string
	 */
	private $rowContents;
	
	/**
	 *
	 * @var string
	 */
	private $encodedContents;
	
	/**
	 *
	 * @var DirectoryBuilder
	 */
	private $dirBuilder;
	
	/**
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 *
	 * @var boolean
	 */
	private $isImageEnabled;
	
	private $info;
	private $error;
	
	function __construct(DirectoryBuilder $dirBuilder, $isImageEnabled = false) {
		$dirBuilder->build();
		$this->dirBuilder = $dirBuilder;
		$this->isImageEnabled = $isImageEnabled;
	}
	
	public function fromText($url, $html) {
		$this->url = $url;
		$this->rowContents = $html;
		$this->encodedContents = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
	}
	
	public function fromUrl($url) {
		$this->url = $url;
		//$this->result = file_get_contents($this->url);
		
//		ブログによっては403ではじかれる。ユーザーエージェント？IP？
		$httpRequest = new HttpRequest($this->url);
		$httpRequest->exec();
		
//		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_URL, $url);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
//		//curl_setopt($ch, CURLOPT_HEADER, true);
//
//
//		$headers = array(
//		    "HTTP/1.0",
//		    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
//		    "Accept-Encoding:gzip ,deflate",
//		    "Accept-Language:ja,en-us;q=0.7,en;q=0.3",
//		    "Connection:keep-alive",
//		    "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0"
//		    );
//		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//		$this->rowContents = curl_exec($ch);
//		$this->error = curl_error($ch);
//		$this->info = curl_getinfo($ch);
//		curl_close ($ch);
		
		$this->fromText($url, $httpRequest->getResponse());
	}

	public function convertToKindleFile() {
		$extractor = new ContentExtractor($this->encodedContents);
		$extractor->exec($this->encodedContents());

		if ($this->isImageEnabled) {
			$imgDownloader = new ImageDownloader($extractor->contentNode, new Url($this->url), $this->dirBuilder);
			$imgDownloader->exec();
		}

		$normalizer = new ContentsNormalizer($this->url, $extractor->title, $extractor->contentNode);
		$normalizer->exec();
		$html = $normalizer->getHtml();
		
		$ret = $this->dirBuilder->putContents($html);

		$mobiFileName = pathinfo($this->dirBuilder->getMobiPath(), PATHINFO_BASENAME);
		$command = KindleGenCommand::newInstance($this->dirBuilder->getContentsPath(), $mobiFileName);
		$command->exec();
		return  file_get_contents($this->dirBuilder->getMobiPath());
	}

	public function destroy() {
		$this->dirBuilder->remove();
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
