<?php
require_once implode('/', [PATH_MODEL, 'Mail.php']);
require_once implode('/', [PATH_MODEL, 'KindleGenCommand.php']);
require_once implode('/', [PATH_MODEL, 'HtmlContents.php']);
require_once implode('/', [PATH_MODEL, 'ContentsNormalizer.php']);
require_once implode('/', [PATH_MODEL, 'DirectoryBuilder.php']);
require_once implode('/', [PATH_MODEL, 'ImageDownloader.php']);

class Service {
	public static function sendHtmlToKindle($sendTo, $from, $url, $htmlText = '', $isImageEnabled = true) {
		$htmlContents = new HtmlContents(new DirectoryBuilder(), $isImageEnabled);
		if ($htmlText) {
			$htmlContents->setIsExtractEnabled(false);
			$htmlContents->fromText($url, $htmlText);
		} else {
			$htmlContents->fromUrl($url);
		}
		
		$kindleFile = $htmlContents->convertToKindleFile();
		if (!$kindleFile) {
			d(['kindlefileが作成できませんでした', $url, $sendTo, $from, $isImageEnabled, !empty($htmlText)]);
			return false;
		}
		
		$ret = self::sendMail($sendTo, $from, $kindleFile);
		$htmlContents->destroy();
		
		return $ret;
	}

	public static function sendFileToKindle($sendTo, $from, AttachedFile $file) {
        $dirBuilder = new DirectoryBuilder();
        $dirBuilder->build();
        $file->saveToDir($dirBuilder->baseDir);
        d([$sendTo, $from, $file->getSavedFile()]);
		$ret = self::sendMail($sendTo, $from, $file->getSavedFile(), $file->name);
		return $ret;
	}

	public static function sendFeedToKindle($sendTo, $from, $fileName, $file) {
		
	}

	/**
	 * 
	 * @return boolean
	 */
	private static function sendMail($sendTo, $from, $kindleFile, $fileName = 'kindle.mobi') {
		$mail = new Mail();
		$mail->setSendto($sendTo);
		$mail->setFrom($from);
		$mail->setFile($kindleFile);
		$mail->setFileName($fileName);
		return $mail->send();
	}
	
	public static function kindlePath(Url $url) {
		return 'images/' . $url->host . $url->path . '/' . $url->file;
	}

}
