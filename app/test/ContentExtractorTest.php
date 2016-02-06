<?php
require_once implode('/', [PATH_MODEL, 'ContentExtractor.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'HttpRequest.php']);
require_once implode('/', [PATH_MODEL, 'Test.php']);

class ContentExtractorTest extends Test {
	private $urls = [
			'/html/body/div[4]/section/div[2]' => 'http://php.net/manual/ja/function.is-dir.php',
			'/html/body/div[3]' => 'http://www.martinfowler.com/agile.html',
			'/html/body/div[2]/div/div[1]/div/div[2]' => 'http://hrnabi.com/2015/06/24/8520/',
			'/html/body/div[2]/article/div[2]/div/div[1]/section' => 'http://qiita.com/taiyop/items/78d3a0614be9be77ce41?utm_source=Qiita%E3%83%8B%E3%83%A5%E3%83%BC%E3%82%B9&utm_campaign=2f6534f084-Qiita_newsletter_183_11_25_2015&utm_medium=email&utm_term=0_e44feaa081-2f6534f084-33117453',
			'/html[1]/body/div[1]/div/div[2]/div[2]/div[1]/div[1]/div[1]/div[3]/div[1]/p' => 'http://headlines.yahoo.co.jp/hl?a=20160205-00000100-mai-soci',
			'/html/body/div[2]/div[1]/div[2]/div[2]/div/div/div[5]/div[3]' => 'http://www.rbbtoday.com/article/2016/02/04/139367.html',
			'/html/body/main/div[1]/article/div[3]/div/div[4]' => 'https://markezine.jp/article/detail/23868',
		];
	
	public function t_estXpathSetup() {
		return;
		
		$datPath = implode('/', [PATH_TEST, 'dat', 'ContentExtractor']);
		
		$res = exec(sprintf('rm -rf %s/*', $datPath), $out);
		
		foreach ($this->urls as $xpath => $url) {
			$httpRequest = new HttpRequest($url);
			$ret = $httpRequest->exec();
			$this->assertEquals(true, $ret);
			file_put_contents($datPath . '/' . urlencode($xpath), $httpRequest->getResponse());
		}
	}
	
	public function testXpath() {
		$datPath = implode('/', [PATH_TEST, 'dat', 'ContentExtractor']);
		$files = scandir($datPath);
		foreach ($this->urls as $xpathExpected => $url) {
			// ファイル名が正解のxpath
			$path = implode('/', [$datPath, urlencode($xpathExpected)]);
			if (is_dir($path)) {
				continue;
			}
			
			$html = file_get_contents($path);
			$extractor = new ContentExtractor();
			$extractor->exec($html);
			$xpath = $extractor->calculateXpath();

			$this->assertEquals(urldecode($xpathExpected), $xpath, $url);
		}
	}
}
