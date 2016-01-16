<?php
require_once implode('/', [PATH_MODEL, 'Kindle.php']);

class Service {
	public static function sendToKindle($url, $sendTo, $from) {
		$downloader = new ContentsDownloader($url);
		$downloader->exec();
		$extractor = new ContentExtractor($downloader->encodedContents());
		$extractor->exec();

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
		
		$mobi = file_get_contents($dirBuilder->getMobiPath());
		
		
		$ret = Mail::sendKindleFasade($from, $sendTo, $mobi, $mobiFileName);
		
		return $ret;
	}
}