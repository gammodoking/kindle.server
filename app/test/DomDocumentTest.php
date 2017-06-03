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

}
