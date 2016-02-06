<?php
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);

class ContentExtractor {
	
	/**
	 *
	 * @var int
	 */
	private $highScore;
	
	/**
	 *
	 * @var DomNode
	 */
	private $extracedNode;
	
	/**
	 *
	 * @var int
	 */
	private $domCount = 0;
	
	public $title;
	
	public $score;
	
	private $removeTagNames = array(
		'#text',
		'script',
		'link',
		'iframe',
	);
	
	private $skipNode = array(
		'header',
		'footer',
		'#text',
		'script',
		'link',
		'iframe',
	);
	
	/**
	 *
	 * @var DomXPath 
	 */
	private $domXPath;
	
	/**
	 *
	 * @params HtmlContents $content
	 */
	function __construct() {
	}
	
	/**
	 * 
	 * @param string $html
	 */
	public function exec($html) {
		$doc = new DomDocument();
		@$doc->loadHTML($html);
		
		
		$this->domXPath = new DomXPath($doc);
		
		$this->title = $doc->getElementsByTagName('title')->item(0)->textContent;
		
		$node = $doc->getElementsByTagName('body')->item(0);
		$this->highScore = -1000000;
		$this->extracedNode = null;
		$this->scan($node);
		
//		foreach ($this->removeTagNames as $tagName) {
//			$nodes = $doc->getElementsByTagName($tagName);
//			foreach ($nodes as $node) {
//				try {
//					$doc->removeChild($node);
//				} catch (Exception $e) {
//					// 無視
//				}
//			}
//		}
	}
	
	public function scan(DomNode $node) {
		if (!$node->childNodes) {
			return;
		}
		
		$score = $this->calcScore($node);
		
		if ($score >= $this->highScore) {
			$this->highScore = $score;
			$this->setExtractedNode($node);
		}
		
		foreach ($node->childNodes as $n) {
			if (in_array($n->nodeName, $this->skipNode)) {
				continue;
			}
			self::scan($n);
		}
	}

	private function calcScore(DomNode $node) {
		$this->domCount = 0;
		
		$text = $this->getTextFromNode($node);
		$params = array();
		$params['domCount'] = $this->domCount;
		$params['textLength'] = mb_strlen($text);
		$params['toten'] = count(preg_split('/,/', $text)) - 1 + count(preg_split('/、/', $text)) - 1;
		$params['kuten'] = count(preg_split('/./', $text)) - 1 + count(preg_split('/。/', $text)) - 1;
		
//		
//		
//		$classname="my-class";
//		$nodes = $this->domXPath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
		
//		
//		$params['positiveTagCount'] = 
//				$node->getElementsByTagName('article')->length * 20 +
//				$node->getElementsByTagName('h1')->length * 20 +
//				$node->getElementsByTagName('h2')->length * 10 +
//				$node->getElementsByTagName('h3')->length * 5 +
//				$node->getElementsByTagName('h4')->length * 3 +
//				$node->getElementsByTagName('h5')->length +
//				$node->getElementsByTagName('h6')->length
//				;
//		$params['negativeTagCount'] = 
//				$node->getElementsByTagName('aside')->length * 100 +
//				$node->getElementsByTagName('a')->length +
//				$node->getElementsByTagName('li')->length
//				;
//		
//		$params['ngTagCount'] =
//				$node->getElementsByTagName('footer')->length +
//				$node->getElementsByTagName('header')->length;
//		
//		$params['negativeKeywordCount'] = 
//				count(preg_split('/copyright/', $text)) - 1 + 
//				count(preg_split('/©/', $text)) - 1
//				;
		
		$score = 
				$params['kuten'] * 0.02 +
				$params['toten'] * 0.02 +
				$node->getElementsByTagName('article')->length * 20 +
				$node->getElementsByTagName('h1')->length * 10 +
				$node->getElementsByTagName('h2')->length * 10 +
				$node->getElementsByTagName('h3')->length * 10 +
				$node->getElementsByTagName('h4')->length * 5 +
				$node->getElementsByTagName('h5')->length * 3 +
				$node->getElementsByTagName('h6')->length * 1 +

				(
				$params['domCount'] * 0.05 +
				$node->getElementsByTagName('aside')->length * 300 +
				$node->getElementsByTagName('a')->length * 3 +
				$node->getElementsByTagName('li')->length * 2 +
				
				$node->getElementsByTagName('footer')->length * 100 +
				$node->getElementsByTagName('header')->length * 100 +
				$node->getElementsByTagName('script')->length * 100 +
				
				count(preg_split('/copyright/', $text)) - 1 + 
				count(preg_split('/©/', $text)) - 1
				) * -1
				;
//				echo $score . PHP_EOL;
//			$params['toten'] * 1
//			+ $params['kuten'] * 1
//			+ $params['positiveTagCount'] * 10
//			- $params['domCount'] * 1
//			- $params['negativeTagCount'] * 300
//			- $params['ngTagCount'] * 1000
//			- $params['negativeKeywordCount'] * 100
//			;
			
		$this->score = $params;
		return (int)$score;
	}

	/**
	 * 
	 * @param DomNode $node
	 * @param string $text
	 * @return string
	 */
	private function getTextFromNode(DomNode $node, $text = "") {
		
	    if (@$node->tagName == null) 
	        return $text . $node->textContent;
	    
		if (!in_array($node->nodeName, $this->skipNode)) {
		    $this->domCount += 1;
		}

	    $node = $node->firstChild;
	    if ($node != null) 
	        $text = $this->getTextFromNode($node, $text); 

	    while(@$node->nextSibling != null) { 
	        $text = $this->getTextFromNode($node->nextSibling, $text);
	        $node = $node->nextSibling; 
	    } 
	    return $text; 
	}
	
	/**
	 * 
	 * @return string
	 */
	public function calculateXpath() {
		return $this->extracedNode->getNodePath();
	}
	
	/**
	 * 
	 * @return type
	 */
	public function getExtractedNode() {
		return $this->extracedNode;
	}
	
	private function setExtractedNode(DomNode $node) {
		$this->extracedNode = $node;
	}
	
}
