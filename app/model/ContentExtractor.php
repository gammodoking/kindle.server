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
	public $domCountAll = 0;
	public $pancutuationCountAll = 0;
	public $textLengthAll = 0;
	
	public $title;
	
	public $_params;
	public $params;
	
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
		'style',
		'nav',
		'aside',
		'a',
		'link',
		'iframe',
	);
	
	/**
	 *
	 * @var DomXPath 
	 */
	private $domXPath;
	
	public $domCount = 0;
	
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
		$html = preg_replace('/[ 　\n\t]{1,}/', ' ', $html);
		$doc = new DomDocument();
		@$doc->loadHTML($html);
		
		
		$this->domXPath = new DomXPath($doc);
		$this->title = $doc->getElementsByTagName('title')->item(0)->textContent;
		
		$node = $doc->getElementsByTagName('body')->item(0);
		
		$this->domCountAll = $this->getDomCount($node, 0);
		
		$text = $this->getTextFromNode($node);
		$this->pancutuationCountAll = $this->calcKutenScore($text) + $this->calcTotenScore($text);
		
		$this->textLengthAll = mb_strlen($text);
		
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
		
		if ($score > $this->highScore) {
			$this->highScore = $score;
			$this->setExtractedNode($node);
			$this->params = $this->_params;
		}
		
		foreach ($node->childNodes as $n) {
			if (in_array($n->nodeName, $this->skipNode)) {
				continue;
			}
			self::scan($n);
		}
	}
	
	private function calcKutenScore($text) {
		return count(preg_split('/\./', $text)) - 1 + 
			count(preg_split('/。/', $text)) * 8 - 1;
	}
	private function calcTotenScore($text) {
		return count(preg_split('/,/', $text)) - 1 + 
			count(preg_split('/、/', $text)) * 8 - 1;
	}

	private function calcScore(DomNode $node) {
		$this->domCount = 0;
		
		$text = $this->getTextFromNode($node);
		$params = array();
		$params['domCount'] = $this->domCount;
		$params['textLength'] = mb_strlen($text);
		$params['toten'] = $this->calcTotenScore($text);
		$params['kuten'] = $this->calcKutenScore($text);
		
		$params['panctuationRatio'] = ($this->calcKutenScore($text) + $this->calcTotenScore($text)) / $this->pancutuationCountAll;
		$params['domCountRatio'] = $params['domCount'] / $this->domCountAll;
		$params['textLengthRatio'] = $params['textLength'] / $this->textLengthAll;
		
		$params['reafNodePenalty'] = ($this->domCount === 1 ? 1 : 0);
		$params['bodyNodePenalty'] = ($node->nodeName === 'body' ? 1 : 0);
		
		$class = $node->getAttribute('class');
		$id = $node->getAttribute('id');
		$params['positiveAttributes'] = 
				$class === 'article'
				|| $id === 'article'
				|| $class === 'content'
				|| $id === 'content'
				|| $class === 'main'
				|| $id === 'main'
//				|| strpos($class, 'article') !== false
//				|| strpos($class, 'content') !== false
//				|| strpos($id, 'article') !== false
//				|| strpos($id, 'content') !== false
				
				? 1 : 0;
//		$params['text'] = $text;
 		
		$score = 
				$params['textLengthRatio'] * 10 +
				$params['panctuationRatio'] * 350 + 
				
				($node->tagName === 'article' ? 1 : 0) * 150 +
				$params['positiveAttributes'] * 100 + 
				$node->getElementsByTagName('h1')->length * 5 +
				$node->getElementsByTagName('h2')->length * 5 +
				$node->getElementsByTagName('h3')->length * 5 +
				$node->getElementsByTagName('h4')->length * 5 +
				$node->getElementsByTagName('h5')->length * 3 +
				$node->getElementsByTagName('h6')->length * 1 +
				1

				 - (
					$params['domCountRatio'] * 200 +
//						
//					$this->hasParentbyTagName($node, 'nav') * 500 +
//					$this->hasParentbyTagName($node, 'aside') * 500 +
//					$node->getElementsByTagName('a')->length * 1 +
					$node->getElementsByTagName('img')->length * 1 +
					$node->getElementsByTagName('aside')->length * 15 +
					$this->hasParentbyTagName($node, 'li') * 30 +
					$this->hasParentbyTagName($node, 'dt') * 30 +
					$params['bodyNodePenalty'] * 1000 +
					$params['reafNodePenalty'] * 1000 +
					$node->getElementsByTagName('footer')->length * 5 +
					$node->getElementsByTagName('header')->length * 5 +
					$node->getElementsByTagName('iframe')->length * 5 +
					$node->getElementsByTagName('script')->length * 5 +
//					(
//							count(preg_split('/copyright/', $text)) * 10 - 1 + 
//							count(preg_split('/©/', $text)) * 10 - 1
//					) + 
				1)
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
			
		$params['score'] = $score;
		$this->_params = $params;
		return (int)$score;
	}

	/**
	 * 
	 * @param DomNode $node
	 * @param string $text
	 * @return string
	 */
	public function getTextFromNode(DomNode $node, $text = "") {
		
	    if (@$node->tagName == null) 
	        return $text . html_entity_decode($node->textContent);
	    
		if (@$node->tagName !== null && !in_array($node->nodeName, $this->skipNode)) {
		    $this->domCount += 1;
		}

	    $node = $node->firstChild;
	    if ($node != null) 
	        $text = $this->getTextFromNode($node, $text); 

	    while(@$node->nextSibling != null) { 
			if (in_array($node->nodeName, $this->skipNode)) {
		        $text = $this->getTextFromNode($node->nextSibling, $text);
			}
	        $node = $node->nextSibling; 
	    } 
	    return $text;
	}
	
	private function getDomCount(DomNode $node, $count = 0) {
		if (@$node->tagName !== null && !in_array($node->nodeName, $this->skipNode)) {
		    $count += 1;
		}

	    $node = $node->firstChild;
	    if ($node != null) 
	        $count = $this->getDomCount($node, $count);
		
	    while(@$node->nextSibling != null) { 
	        $count = $this->getDomCount($node->nextSibling, $count);
	        $node = $node->nextSibling; 
	    } 
	    return $count;		
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
	
	private function hasParentbyTagName(DomNode $node, $tagName) {
		return strpos($node->getNodePath(), $tagName) === false
				|| $node->nodeName === $tagName
				? 0 : 1;
		
	}
	
}
