<?php
require_once implode('/', [PATH_MODEL, 'Kindle.php']);

class Service {
	public static function sendToKindle($url, $sendTo, $from, $imageOk = true, $htmlText = '') {
		
		if ($url) {
			$downloader = new ContentsDownloader($url);
			$downloader->exec();
			$extractor = new ContentExtractor($downloader->encodedContents());
			$extractor->exec();
		} else {
			$extractor = new ContentExtractor(mb_convert_encoding($htmlText, 'HTML-ENTITIES', 'UTF-8'));
			$extractor->exec();
		}

		$dirBuilder = new DirectoryBuilder();
		$ret = $dirBuilder->build();
		
		if ($imageOk) {
			$imgDownloader = new ImageDownloader($extractor->contentNode, new Url($url), $dirBuilder);
			$imgDownloader->exec();
		}
		
		
		$normalizer = new ContentsNormalizer($url, $extractor->title, $extractor->contentNode);
		$normalizer->exec();
		$html = $normalizer->getHtml();
		
		$ret = $dirBuilder->putContents($html);
		
		
		$mobiFileName = pathinfo($dirBuilder->getMobiPath(), PATHINFO_BASENAME);
		$command = KindleGenCommand::newInstance($dirBuilder->getContentsPath(), $mobiFileName);
		$command->exec();
		
		$mobi = file_get_contents($dirBuilder->getMobiPath());
		
		
//		$ret = Mail::sendKindleFasade($from, $sendTo, $mobi, $mobiFileName);
		$dirBuilder->remove();
		
		return $ret;
	}
}
