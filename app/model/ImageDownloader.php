<?php
class ImageDownloader {
	private $domElement;
	
	/**
	 * @var Url
	 */
	private $url;
	private $dirBuilder;
	
	function __construct(DomElement $domElement, Url $url, DirectoryBuilder $dirBuilder) {
		$this->domElement = $domElement;
		$this->url = $url;
		$this->dirBuilder = $dirBuilder;
	}
	
	public function exec() {
		$imgs = $this->domElement->getElementsByTagName('img');
		foreach ($imgs as $img) {
			$src = $img->getAttribute('src');
			$imgUrl = Url::parseRelative($this->url->url, $src);
			try {
				$imgFile = file_get_contents($imgUrl->url);
				$this->dirBuilder->putImage(Service::kindlePath($imgUrl), $imgFile);
			} catch (Exception $e) {
				// 無視
				d($e);
			}
		}
	}
}
