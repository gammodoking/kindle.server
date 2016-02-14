<?php
require_once implode('/', [PATH_MODEL, 'ContentExtractor.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'HttpRequest.php']);
require_once implode('/', [PATH_MODEL, 'Test.php']);

class ContentExtractorTest extends Test {
	
	/**
	 * 
	 * @return array
	 */
	private function getData() {
		return [
			// 英語
		    'news.yahoo.com' => new TestData('http://news.yahoo.com/the-oscars-gift-bag-is-worth-230k-and-will-make-224812805.html', [
				'/html/body/div[1]/div[5]/div/section/div/div[2]',
				]),
			'www.huffingtonpost.com' => new TestData('http://www.huffingtonpost.com/entry/trump-gop-40-percent_us_56bb5f98e4b08ffac12366c4', [
				'/html/body/div[3]/div[3]/article',
				]),
		    'edition.cnn.com' => new TestData('http://edition.cnn.com/2016/02/10/asia/north-korea-army-chief-ri-yong-gil-executed/index.html', [
				'/html/body/div[3]/article',
				'/html/body/div[3]/article/div[1]/div[3]/div[1]/div[2]',
				'/html/body/div[3]/article/div[1]/div[3]/div[1]/div[2]/section[1]',
				'/html/body/div[3]/article/div[1]/div[3]/div[1]/div[2]/section[1]/div[1]',
				]),
		    'abcnews.go.com' => new TestData('http://abcnews.go.com/US/wireStory/key-events-occupation-oregon-wildlife-refuge-36851904', [
				'/html/body/div[3]/section/section/div/section[1]/article',
				'/html/body/div[3]/section/section/div/section[1]/article/div[1]',
				]),
		    'm.signalvnoise.com' => new TestData('https://m.signalvnoise.com/employee-benefits-at-basecamp-d2d46fd06c58#.9tobkjm4w', [
				'/html/body/div[1]/div[2]/div/article',
				]),
			'en.wikipedia.org' => new TestData('https://en.wikipedia.org/wiki/Android_(robot)', [
				'/html/body/div[3]',
				]),
			'www.nytimes.com' => new TestData('http://www.nytimes.com/2016/02/08/us/politics/obamas-lofty-plans-on-gun-violence-amount-to-little-action.html?ref=todayspaper&_r=0', [
				'/html/body/div[2]/div[2]/main/article/div',
				'/html/body/div[2]/div[2]/main/article',
				]),
			'en.gigazine.net' => new TestData('http://en.gigazine.net/news/20090726_wf2009_s_demonbane/', [
				'/html/body/div/div[2]/div/div[2]',
				'/html/body/div/div[2]/div/div[2]/div',
				'/html/body/div/div[2]/div/div[2]/div/p[2]',
				]),
			'www.martinfowler.com' => new TestData('http://www.martinfowler.com/agile.html', [
				'/html/body/div[3]',
				]),

			// 日本語
			'agora-web.jp' => new TestData('http://agora-web.jp/archives/1669420.html', [
				'/html/body/div[2]/div[4]/div[2]/div/div/div[4]',
				'/html/body/div[2]/div[4]/div[2]/div/div/div[4]/div',
				'/html/body/div[2]/div[4]/div[2]/div/div/div[4]/div/div',
				'/html/body/div[2]/div[4]/div[2]/div/div/div[4]/div/div/div[2]',
				'/html/body/div[2]/div[4]/div[2]/div/div/div[4]/div/div/div[2]/div',
				]),
			'jp.techcrunch.com' => new TestData('http://jp.techcrunch.com/2016/02/11/20160210google-forms-gets-templates-add-on-support-and-more/', [
				'/html/body/div[1]/article',
				]),
			'president.jp' => new TestData('http://president.jp/articles/-/17310', [
				'/html/body/div/div[3]/div/div[1]/div[3]/div[4]',
				]),
			'php.net' => new TestData('http://php.net/manual/ja/function.is-dir.php', [
				'/html/body/div[4]/section/div[2]',
				'/html/body/div[4]/section',
				'/html/body/div[3]/section',
				]),
			'hrnabi.com' => new TestData('http://hrnabi.com/2015/06/24/8520/', [
				'/html/body/div[2]/div',
				'/html/body/div[2]/div/div[1]/div/div[2]',
				]),
			'qiita.com' => new TestData('http://qiita.com/taiyop/items/78d3a0614be9be77ce41?utm_source=Qiita%E3%83%8B%E3%83%A5%E3%83%BC%E3%82%B9&utm_campaign=2f6534f084-Qiita_newsletter_183_11_25_2015&utm_medium=email&utm_term=0_e44feaa081-2f6534f084-33117453', [
				'/html/body/div[2]/article',
				'/html/body/div[2]/article/div[2]/div/div[1]',
				'/html/body/div[2]/article/div[2]/div/div[1]/section',
				]),
			'headlines.yahoo.co.jp' => new TestData('http://headlines.yahoo.co.jp/hl?a=20160205-00000100-mai-soci', [
				'/html[1]/body/div[1]/div/div[2]/div[2]/div[1]',
				'/html[1]/body/div[1]/div/div[2]/div[2]/div[1]/div[1]',
				'/html[1]/body/div[1]/div/div[2]/div[2]/div[1]/div[1]/div[1]',
				'/html[1]/body/div[1]/div/div[2]/div[2]/div[1]/div[1]/div[1]/div[3]',
				]),
			'www.rbbtoday.com' => new TestData('http://www.rbbtoday.com/article/2016/02/04/139367.html', [
				'/html/body/div[2]/div[1]/div[2]/div[2]',
				'/html/body/div[2]/div[1]/div[2]/div[2]/div/div',
				'/html/body/div[2]/div[1]/div[2]/div[2]/div/div/div[5]/div[3]',
				]),
			'markezine.jp' => new TestData('https://markezine.jp/article/detail/23868', [
				'/html/body/main/div/article',
				'/html/body/main/div[1]/article',
				'/html/body/main/div[1]/article/div[3]/div',
				'/html/body/main/div[1]/article/div[3]/div/div[4]',
				]),
			'ja.wikipedia.org' => new TestData('https://ja.wikipedia.org/wiki/%E6%97%A5%E6%9C%AC%E3%81%AB%E3%81%8A%E3%81%91%E3%82%8B%E5%A4%96%E5%9B%BD%E4%BA%BA%E5%8F%82%E6%94%BF%E6%A8%A9', [
				'/html/body/div[3]/div[3]/div[4]',
				'/html/body/div[3]/div[3]',
				'/html/body/div[3]',
				]),
		];
	}
	
	public function t_estXpathSetup() {
		return;
		
		$datPath = implode('/', [PATH_TEST, 'dat', 'ContentExtractor']);
		
//		$res = exec(sprintf('rm -rf %s/*', $datPath), $out);
		
		foreach ($this->getData() as $name => $testData) {
			$httpRequest = new HttpRequest($testData->url);
			$ret = $httpRequest->exec();
			if (!$ret) {
				d($httpRequest->getError());
				d($httpRequest->getInfo());
			}
			$this->assertEquals(true, $ret);
			file_put_contents($datPath . '/' . $name, $httpRequest->getResponse());
		}
	}
	
	public function testXpath() {
		$datPath = implode('/', [PATH_TEST, 'dat', 'ContentExtractor']);
		foreach ($this->getData() as $name => $testData) {
			// ファイル名が正解のxpathのキー
			$path = implode('/', [$datPath, $name]);
			if (is_dir($path)) {
				continue;
			}
			
			$html = file_get_contents($path);
			$extractor = new ContentExtractor();
			$extractor->exec($html);
			$xpath = $extractor->calculateXpath();
			
			$text = $extractor->scan($extractor->getExtractedNode());
			$hit = false;
			foreach ($testData->xpathCandidates as $xpathExpected) {
				if ($xpathExpected === $xpath) {
					$hit = true;
				}
			}
			if (!$hit) {
				d($text);
		file_put_contents('./test2.txt', $text);
				
d($extractor->getExtractedNode()->nodeName);
				d($xpath);
				d($testData->url);
				d($extractor->params);
				d('pancutuationCountAll:' . $extractor->pancutuationCountAll);
				d('domCountAll:' . $extractor->domCountAll);
				d('textLengthAll:' . $extractor->textLengthAll);
				d('textAll:' . $extractor->textAll);
//				d('preProcessedInput:' . $extractor->preProcessedInput);
			}
			$this->assertEquals(true, $hit, $xpath . ' ' . $testData->url);// . PHP_EOL . var_export($extractor->params, true) . PHP_EOL);
		}
	}
}

class TestData {
	public $url;
	public $xpathCandidates;
	public function __construct($url, array $xpathCandidates) {
		$this->url = $url;
		$this->xpathCandidates = $xpathCandidates;
	}
}
