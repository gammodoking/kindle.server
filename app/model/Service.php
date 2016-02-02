<?php
require_once implode('/', [PATH_MODEL, 'Mail.php']);
require_once implode('/', [PATH_MODEL, 'KindleGenCommand.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'ContentsNormalizer.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'ImageDownloader.php']);

class Service {
	public static function sendToKindle($url, $sendTo, $from, $imageOk = true, $htmlText = '') {
		
		$htmlContents = new HtmlContents();
		if ($htmlText) {
			$htmlContents->fromText($htmlText);
		} else {
			$htmlContents->fromUrl($url);
		}
		$extractor = new ContentExtractor($htmlContents);
		$extractor->exec();

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
	
	public static function kindlePath(Url $url) {
		return 'images/' . $url->host . $url->path . '/' . $url->file;
	}

}
