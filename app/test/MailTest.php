<?php
require_once implode('/', [PATH_MODEL, 'Mail.php']);
class MailTest {
	public static function test() {
		// メールで日本語使用するための設定をします。
		mb_language("Ja") ;
		mb_internal_encoding("UTF-8");

		$mailto = "nqmxt983@yahoo.co.jp";
		$mailto = "nqmxt983_80@kindle.com";
		$subject = "送信テスト1";
		$content = "やったね送れたね。a";
		$mailfrom = "raix5867@gmail.com";

		//Mail::send_mail($mailto, $subject, $content, $mailfrom);
		$fileName = 'テストファイル.txt';
		$mime = 'text/plain';
//		echo Mail::send_attached_mail($mailto, $subject, $content, $mailfrom, $content, $fileName, $mime);
	}
}
