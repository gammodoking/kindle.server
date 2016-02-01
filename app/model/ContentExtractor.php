<?php
class ContentExtractor {
	
	private $highScore;
	public $contentNode;
	public $title;
	
	public $score;
	
	private $skipNode = array(
		'#text',
		'script',
		'link',
		'header',
		'footer',
	);
	
	private $content = '';
	
	/**
	 *
	 * @params string $content
	 */
	function __construct($content) {
		$this->content = $content;
	}
	
	public function exec() {
		$doc = new DomDocument();
		@$doc->loadHTML($this->content);
		$this->title = $doc->getElementsByTagName('title')->item(0)->textContent;
		$node = $doc->getElementsByTagName('body')->item(0);
		$this->highScore = 0;
		$this->contentNode = null;
		$this->scan($node);
	}
	
	public function scan(DomNode $node) {
		if (!$node->childNodes) {
			return;
		}
		
		$score = $this->calcScore($node);
		
		if ($score >= $this->highScore) {
			$this->highScore = $score;
			$this->contentNode = $node;
		}
		
		foreach ($node->childNodes as $n) {
			if (in_array($n->nodeName, $this->skipNode)) {
				continue;
			}
			self::scan($n);
		}
	}

	private $domCount = 0;
	
	private function calcScore(DomNode $node) {
		$this->domCount = 0;
		
		$text = $this->getTextFromNode($node);
		$params = array();
		$params['domCount'] = $this->domCount;
		$params['textLength'] = mb_strlen($text);
		$params['toten'] = count(preg_split('/,/', $text)) - 1 + count(preg_split('/、/', $text)) - 1;
		$params['kuten'] = count(preg_split('/./', $text)) - 1 + count(preg_split('/。/', $text)) - 1;
		$params['copyright'] = count(preg_split('/copyright/', $text)) - 1;
		
		$params['negativeTagCount'] = $node->getElementsByTagName('a')->length;// ? $node->getElementsByTagName('footer').length : 0;
		$params['ngTagCount'] = $node->getElementsByTagName('footer')->length
			 + $node->getElementsByTagName('header')->length;
		
		$score = 
			$params['toten'] * 1
			+ $params['kuten'] * 1
//			+ $params['textLength'] / $params['domCount']
			- 100 * $params['copyright']
			- $params['domCount'] * 10
			- $params['negativeTagCount'] * 20
			- $params['ngTagCount'] * 10000
			;
			
		$this->score = $params;
		return (int)$score;
	}

	private function getTextFromNode($Node, $Text = "") {
		
	    if (@$Node->tagName == null) 
	        return $Text . $Node->textContent;
	    
		if (!in_array($Node->nodeName, $this->skipNode)) {
		    $this->domCount += 1;
		}

	    $Node = $Node->firstChild;
	    if ($Node != null) 
	        $Text = $this->getTextFromNode($Node, $Text); 

	    while(@$Node->nextSibling != null) { 
	        $Text = $this->getTextFromNode($Node->nextSibling, $Text);
	        $Node = $Node->nextSibling; 
	    } 
	    return $Text; 
	} 
	
}
