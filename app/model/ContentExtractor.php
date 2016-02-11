<?php
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_LIB, 'simple_html_dom.php']);

class ContentExtractor {
	private $debug = false;
	
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
	public $textAll = '';
	public $preProcessedInput = '';
	
	public $text = '';
	
	private $domCount = 0;
	
	public $title;
	
	public $_params;
	public $params;
	
//	private $removeTagNames = array(
//		'#comment',
//		'#cdata-section',
//		'script',
//		'link',
//		'iframe',
//	);
	
	private $skipNode = array(
		'#comment',
		'#cdata-section',
		'script',
		'noscript',
		'style',
		'iframe',
		'select',
		'textarea',
		'option',
		'header',
		'footer',
		'aside',
		'nav',
		'a',
		'link',
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
		
		mb_language('Japanese');
		
		// 1.プリプロセス

		// scriptテキスト削除
		// script内に文字列リテラルの閉じタグがあるとDomDocumentがscriptのソースを#text扱いしてしまうので
		// script内の文字を削除する
		// 正規表現で削除しようとするとSegmentation faultが発生する（StackOverFlow?）ので
		// simple_html_domでscript内文字列を削除
		// MAX_FILE_SIZEの制限にひっかかったので、ソースを編集してデフォルトの3倍に変更している
		$simpleHtml = str_get_html($html);
		foreach ($simpleHtml->find('script') as $script) {
			$script->innertext = '';
		}
		$html = $simpleHtml->outertext;
		
		// トリム
		$html = preg_replace('/(\s|　)+/mi', ' ', $html);
		
		// 2. dom生成
		$doc = new DomDocument("1.0", "utf-8");
		@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$node = $doc->getElementsByTagName('body')->item(0);

		$this->preProcessedInput = $node->textContent;
		// 3.プロパティを初期化
		$this->domXPath = new DomXPath($doc);
		$this->title = $doc->getElementsByTagName('title')->item(0)->textContent;
		$text = $this->scan($node);
		$this->textAll = $text;
		$this->domCountAll = $this->domCount;
		$this->pancutuationCountAll = $this->calcKutenScore($text) + $this->calcTotenScore($text);
		$this->textLengthAll = mb_strlen($text);
		$this->highScore = -1000000;
		$this->extracedNode = null;
		

		// 4.実行
		$this->extract($node);
		
	}
	
	private function extract(DomNode $node) {
		
		if (!$node->childNodes) {
			return;
		}
		
		$score = $this->calcScore($node);
		
		if ($score > $this->highScore) {
			$this->highScore = $score;
			$this->setExtractedNode($node);
			$this->params = $this->_params;
		}
		
		foreach ($node->childNodes as $nextNode) {
			if ($this->shouldSkip($nextNode)) {
				continue;
			}
			$this->extract($nextNode);
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
	
	private function shouldSkip(DomNode $node) {
		return in_array($node->nodeName, $this->skipNode);
	}

	private function calcScore(DomNode $node) {
		$this->domCount = 0;
		
		$text = $this->scan($node);
		$params = array();
		$params['domCount'] = $this->domCount;
		$params['textLength'] = mb_strlen($text);
		$params['toten'] = $this->calcTotenScore($text);
		$params['kuten'] = $this->calcKutenScore($text);
		
		$params['panctuationRatio'] = 
				($params['toten'] + $params['kuten']) / 
				$this->pancutuationCountAll;
		$params['domCountRatio'] = $params['domCount'] / $this->domCountAll;
		$params['textLengthRatio'] = $params['textLength'] / $this->textLengthAll;
		
		$params['reafNodePenalty'] = ($this->domCount === 1 ? 1 : 0);
		$params['bodyNodePenalty'] = ($node->nodeName === 'body' ? 1 : 0);
		
		$class = $node->getAttribute('class');
		$id = $node->getAttribute('id');
		$params['positiveAttributes'] = 
				$class === 'article'
				|| $id === 'article'
				|| strpos($class, 'article') !== false
				|| strpos($id, 'article') !== false
				|| $class === 'content'
				|| $id === 'content'
				|| strpos($class, 'content') !== false
				|| strpos($id, 'content') !== false
				|| $class === 'main'
				|| $id === 'main'
				|| strpos($class, 'main') !== false
				|| strpos($id, 'main') !== false
				? 1 : 0;
		
		$params['negativeAttributes'] = 
				false
				|| strpos($class, 'sub') !== false
				|| strpos($id, 'sub') !== false
				|| strpos($class, 'right') !== false
				|| strpos($id, 'right') !== false
				? 1 : 0;		
//		$params['text'] = $text;
 		
		$score = 
				$params['textLengthRatio'] * 10 +
				$params['panctuationRatio'] * 350 + 
				
				($node->tagName === 'article' ? 1 : 0) * 150 +
				$params['positiveAttributes'] * 50 + 
				$node->getElementsByTagName('h1')->length * 10 +
				$node->getElementsByTagName('h2')->length * 10 +
				$node->getElementsByTagName('h3')->length * 5 +
				$node->getElementsByTagName('h4')->length * 5 +
				$node->getElementsByTagName('h5')->length * 3 +
				$node->getElementsByTagName('h6')->length * 1 +
				1

				 - (
					$params['domCountRatio'] * 200 +
					$params['negativeAttributes'] * 100 +
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
					(
							count(preg_split('/copyright/', $text)) * 10 - 1 + 
							count(preg_split('/©/', $text)) * 10 - 1
					) + 
				1)
				;
		
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
	public function scan(DomNode $node, $text_ = "") {
		$this->text = $text_;
		if ($node instanceof DomElement) {
		    $this->domCount += 1;
		}
		
	    if (@$node->nodeName === '#text') {
			if ($this->debug) {
				d(sprintf('%s(%s) id(%s) class(%s)',
						get_class($node),
						$node->nodeName,
						$node instanceof DomElement ? $node->getAttribute('id') : '',
						$node instanceof DomElement ? $node->getAttribute('class') : ''
						));
				$parent = $node->parentNode;
				d($parent->getAttribute('class') . ':' . $node->parentNode->getNodePath());
				d($node->textContent);
			}
	        return $this->text . html_entity_decode($node->textContent);
		}
	   
		if (!$node->childNodes) {
			return $this->text;
		}
		
		foreach ($node->childNodes as $nextNode) {
			if (!$this->shouldSkip($nextNode)) {
		        $this->text = $this->scan($nextNode, $this->text);
			}
	    }
	    return $this->text;
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
