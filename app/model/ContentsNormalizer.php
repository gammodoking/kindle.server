<?php
class ContentsNormalizer {
	private $url;
	private $title;
	private $contentNode;
	
	/**
	 * 
	 * @param string $url
	 * @param string $title
	 * @param DomNode $contentNode
	 */
	function __construct($url, $title, $contentNode) {
		$this->url = $url;
		$this->title = $title;
		$this->contentNode = $contentNode;
	}
	
	public function exec() {
		$this->normalize();
	}
	
	public function getHtml() {
		$htmlContent = $this->contentNode->C14N();
		return sprintf('<!DOCTYPE html><html><head><title>%s</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>'
                    . '<body>'
                    . "<a href='{$this->url}'>original document<br>{$this->url}</a><br><br>"
                    . '%s'
                    . "<br><br><a href='{$this->url}'>original document<br>{$this->url}</a>"
                    . '</body>'
                    . '</html>',
                $this->title,
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
