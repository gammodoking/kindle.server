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

	public static function sendFileToKindle($sendTo, $from, $fileName, $file) {
		
	}

	public static function sendFeedToKindle($sendTo, $from, $fileName, $file) {
		
	}

	/**
	 * 
	 * @return boolean
	 */
	private static function sendMail($sendTo, $from, $kindleFile) {
		$mail = new Mail();
		$mail->setSendto($sendTo);
		$mail->setFileName('kindle.mobi');
		$mail->setFrom($from);
		$mail->setFile($kindleFile);
		return $mail->send();
	}
	
	
	public static function kindlePath(Url $url) {
        // urlが長すぎるとlinuxのファイル名の長さ限界を超えるのでハッシュをとる
        $hashedPath = md5($url->host . $url->path);
		return 'images/' . $hashedPath . '/' . $url->file;
	}

}
