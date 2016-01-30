<?php

require_once implode('/', [PATH_CORE_CLASS, 'Controller.php']);
require_once implode('/', [PATH_MODEL, 'Kindle.php']);
require_once implode('/', [PATH_MODEL, 'Service.php']);

class IndexController extends Controller {
	
	public function indexAction() {
		$this->request->getHost();
		
		$url = 'http://semooh.jp/jquery/api/ajax/jQuery.ajax/options/';
		$url = @$_GET['url'];
		
		$sendTo = 'nqmxt983_80@kindle.com';
		$sendTo = @$_COOKIE["send_to"];
		
		$scriptUrl = sprintf('http://%s/', $this->request->getHost());
		$bookmarklet = sprintf("javascript: location.href='%s?url=' + encodeURIComponent(document.location.href);", $scriptUrl);
		
		$content = sprintf('
<form method="post" action="send">
<label for="url">url</label><input name="url" id="url" type="text" value="%s"><br />
<label for="sendTo">send to</label><input name="sendTo" id="sendTo" type="text" value="%s"><br />
<button type="submit">送信</button>
</form>
<a href="%s">kindle</a>
		', $url, $sendTo, $bookmarklet);
		View::render($content);
	}
	
	public function sendAction() {
		$url = $_POST['url'] ?: '';
		$sendTo = @$_POST['sendTo'] ?: '';
		$from = @$_POST['from'] ?: 'raix5867@gmail.com';

		if ($sendTo) {
			setcookie('send_to', $sendTo, time() + 60 * 60 * 24 * 30 * 365);
		}
		
		$ret = Service::sendToKindle($url, $sendTo, $from);
		if ($ret) {
			View::render("送信しました<br />" );
		} else {
			View::render('失敗しました');
		}
	}
	
	public function testAction() {
		$url = 'http://semooh.jp/jquery/api/ajax/jQuery.ajax/options/';
		$urlWithRelativeSrc = 'http://shimizuyu.jp/index.html#facilities';
		$url = $urlWithRelativeSrc;
		$urlWithImage = 'http://qiita.com/taiyop/items/050c6749fb693dae8f82';
		$url = $urlWithImage;
		
		$sendTo = 'nqmxt983_80@kindle.com';
//		$sendTo = 'nqmxt983@yahoo.co.jp';
		
		$downloader = new ContentsDownloader($url);
		$downloader->exec();
		$extractor = new ContentExtractor($downloader->encodedContents());
		$extractor->exec();
		Log::d($extractor->score);
		
		$dirBuilder = new DirectoryBuilder();
		$ret = $dirBuilder->build();
		$imgDownloader = new ImageDownloader($extractor->contentNode, new Url($url), $dirBuilder);
		$imgDownloader->exec();
		
		$normalizer = new ContentsNormalizer($url, $extractor->title, $extractor->contentNode);
		$normalizer->exec();
		$html = $normalizer->getHtml();
		
		$ret = $dirBuilder->putContents($html);
		
		$mobiFileName = pathinfo($dirBuilder->getMobiPath(), PATHINFO_BASENAME);
		$command = KindleGenCommand::newInstance($dirBuilder->getContentsPath(), $mobiFileName);
		$command->exec();
		Log::d($command->result);
		
		$mobi = file_get_contents($dirBuilder->getMobiPath());

//		$ret = Mail::sendKindleFasade($sendTo, $mobi, $mobiFileName);
		Log::d($dirBuilder->getMobiPath());
		Log::d($mobiFileName);
		Log::d($ret);
		
		View::render($extractor->contentNode->getNodePath() . '<br /><br />' . $html);
//		View::render($extractor->contentNode->getNodePath() . '<br /><br />' . $extractor->html());
		
//		ContentExtractorTest::test();
//		MailTest::test();
		exit();
	}
}
