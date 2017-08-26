<?php
class ContentsNormalizer {
	private $url;
	private $title;
	private $contentNode;
    
    /**
     * @var ContentExtractor 
     */
	private $contentExtractor;
	
	/**
	 * 
	 * @param string $url
	 * @param string $title
	 * @param ContentExtractor $contentExtractor
	 */
	function __construct($url, $title, ContentExtractor $contentExtractor) {
		$this->url = $url;
		$this->title = $title;
		$this->contentExtractor = $contentExtractor;
		$this->contentNode = $contentExtractor->getExtractedNode();
	}
	
	public function exec() {
		$this->normalize();
	}
	
	public function getHtml() {
        $htmlContent = $this->contentExtractor->getDocument()->saveHTML($this->contentNode);
        
//        C14N()するとエラー（恐らくout of memory）
//		$htmlContent = $this->contentNode->C14N();
		return sprintf('<!DOCTYPE html><html><head><title>%1$s</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>'
                    . '<body>'
                    . '<a href="%2$s">original document<br>%2$s</a><br><br>'
                    . '%3$s'
                    . '<br><br><a href="%2$s">original document<br>%2$s</a>'
                    . '</body>'
                    . '</html>',
                $this->title,
                $this->url,
                $htmlContent);
	}
	
	private function normalize() {
		// a hrefを絶対パスに変更
		$u = Url::parse($this->url);
		foreach ($this->contentNode->getElementsByTagName('a') as $element) {
			$href = $element->getAttribute('href');
			if (strpos($href, 'http') === 0 || strpos($href, '#') === 0) {
				continue;
			}
			if (strpos($href, '/') === 0) {
				$element->setAttribute('href', $u->scheme . $u->host . $href);
			} else if (strpos($href, './') === 0) {
				$element->setAttribute('href', $u->scheme . $u->host . $u->path . substr($href, 2));
			}
		}
		
		// img srcをdomain/相対パスに変更 hoge.domain.com/s/r/c.png
		foreach ($this->contentNode->getElementsByTagName('img') as $element) {
			$src = $element->getAttribute('src');
			$srcUrl = Url::parseRelative($this->url, $src);
			$element->setAttribute('src', Service::kindlePath($srcUrl));
		}
	}
}
