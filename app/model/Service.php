<?php
require_once implode('/', [PATH_MODEL, 'Mail.php']);
require_once implode('/', [PATH_MODEL, 'KindleGenCommand.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'ContentsNormalizer.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'ImageDownloader.php']);

class Service {
	public static function sendToKindle($url, $sendTo, $from, $isImageEnabled = true, $htmlText = '') {
		
		$htmlContents = new HtmlContents(new DirectoryBuilder(), $isImageEnabled);
		if ($htmlText) {
			$htmlContents->fromText($url, $htmlText);
		} else {
			$htmlContents->fromUrl($url);
		}
		
		$kindleFile = $htmlContents->convertToKindleFile();
		
		$mail = new Mail();
		$mail->setSendto($sendTo);
		$mail->setFileName('kindle.mobi');
		$mail->setFrom($from);
		$mail->setFile($kindleFile);
		$ret = $mail->send();
		$htmlContents->destroy();
		
		return $ret;
	}
	
	public static function kindlePath(Url $url) {
		return 'images/' . $url->host . $url->path . '/' . $url->file;
	}

}
