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
	private $isExtractEnabled = true;
	
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
		$this->encodedContents = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8, CP51932, EUC-win, SJIS-win, ASCII');
	}
	
	/**
	 * 
	 * @param boolean $enabled
	 */
	public function setIsExtractEnabled($enabled) {
		$this->isExtractEnabled = $enabled;
	}
	
	public function fromUrl($url) {
		$this->url = $url;
		
//		ブログによっては403ではじかれる。ユーザーエージェント？IP？
		$httpRequest = new HttpRequest($this->url);
		$httpRequest->exec();
        d($httpRequest->getInfo());
        d($httpRequest->getError());

		$this->fromText($url, $httpRequest->getResponse());
	}

	/**
	 * 
	 * @return byte
	 */
	public function convertToKindleFile() {
		$html = $this->rowContents;
		if ($this->isExtractEnabled) {
			$extractor = new ContentExtractor();
			$extractor->exec($this->rowContents);

			if ($this->isImageEnabled) {
				$imgDownloader = new ImageDownloader($extractor->getExtractedNode(), new Url($this->url), $this->dirBuilder);
				$imgDownloader->exec();
			}

			$normalizer = new ContentsNormalizer($this->url, $extractor->title, $extractor->getExtractedNode());
			$normalizer->exec();
			$html = $normalizer->getHtml();
		}
		
		$ret = $this->dirBuilder->putContents($html);

		$mobiFileName = pathinfo($this->dirBuilder->getMobiPath(), PATHINFO_BASENAME);
		$command = KindleGenCommand::newInstance($this->dirBuilder->getContentsPath(), $mobiFileName);
		$command->exec();
			
        // 失敗する　http://stackoverflow.com/questions/11123543/alarmmanager-repeat
		$mobiFile = file_get_contents($this->dirBuilder->getMobiPath());
		return  $mobiFile;
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
