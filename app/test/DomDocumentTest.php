<?php

require_once implode('/', [PATH_MODEL, 'ContentExtractor.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'Test.php']);

class DomDocumentTest extends Test {

	const URL_EUC_JP = 'http://chofu.com/';
	const URL_UTF8 = 'http://qiita.com/hkomo746/items/0418be9be922aecd6ec1';

	public function testDomDocument() {
		$html = $this->loadDat('dom_document.html');
//		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

		$extractor = new ContentExtractor();
		$extractor->exec($html);
		$xpath = $extractor->calculateXpath();

		$text = $extractor->scan($extractor->getExtractedNode());
		d($extractor->getExtractedNode()->nodeName);
		d($text);
		d($xpath);
		d($extractor->params);
		d($extractor->text);
		d($extractor->title);
		d('pancutuationCountAll:' . $extractor->pancutuationCountAll);
		d('domCountAll:' . $extractor->domCountAll);
		d('textLengthAll:' . $extractor->textLengthAll);
		d('textAll:' . $extractor->textAll);
		
		d(mb_strlen('あ', 'utf-8'));
		d(mb_strlen('ほげ
				'));
	}
    
    public function testScriptLiteralClose() {
        
        $html = <<< EOD
<html>
<body><div>a</div><script>var a = '</script>';</script></body>
</html>
EOD;
        $this->doc = $doc = new DOMDocument("1.0", "utf-8");
		@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$node = $doc->getElementsByTagName('body')->item(0);
        $this->assertEquals('<body>
<div>a</div>
<script>var a = \'</script>\';</body>', $doc->saveHTML($node), '文字列リテラルの</script>でも閉じタグと認識してしまう');
    }
    
    public function testRemoveChild() {
        
        $html = <<< EOD
<html>
<body><div>a</div><script>var a = 'asdf';</script></body>
</html>
EOD;
        $this->doc = $doc = new DOMDocument("1.0", "utf-8");
		@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$node = $doc->getElementsByTagName('body')->item(0);
        
        $this->remove($node, 'script');
        
        d($doc->saveHTML($node));

        $this->assertEquals('<body><div>a</div></body>', $doc->saveHTML($node));
    }
    
    private function remove($node, $tagName) {
        $elements = [];
        foreach ($node->getElementsByTagName($tagName) as $e) {
            $elements[] = $e;
        }
        foreach ($elements as $e) {
            $e->parentNode->removeChild($e);
        }
    }

}
