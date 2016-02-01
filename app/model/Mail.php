<?php

class Mail {
	public static function sendKindleFasade($mailfrom, $mailto, $content, $fileName) {
		mb_language('Ja') ;
		mb_internal_encoding('UTF-8');
		$mime = 'text/plain';
		$mime = 'application/octet-stream';
		return Mail::send_attached_mail($mailto, 'あいう', 'えお', $mailfrom, $content, $fileName, $mime);
	}
	
	public static function send_mail($mailto, $subject, $content, $mailfrom) {
		mb_language("ja");
		mb_internal_encoding("UTF-8");
		
		$mailfrom = 'From:' . $mailfrom;

		if(mb_send_mail($mailto,$subject,$content,$mailfrom)){//, '-f ' . $returnMail)){
		echo "送信しました";
		}else{
		echo "送信できませんでした";
		}
	}
	
	public static function send_attached_mail($to, $subject, $plain_message, $from, $attachment = null, $fileName = null, $attach_mime_type = null) {
        if ($attachment === null) {
            self::send_mail($to, $subject, $plain_message, $from);
        } else {
			$fileName = mb_encode_mimeheader( mb_convert_encoding( basename( $fileName ) ,  "ISO-2022-JP" , 'auto' ) );
            //必要に応じて適宜文字コードを設定してください。
            mb_language('Ja');
            mb_internal_encoding('UTF-8');

            $boundary = '__BOUNDARY__'.md5(rand());
                        
            $headers = "Content-Type: multipart/mixed;boundary=\"{$boundary}\"\n";
            $headers .= "From: {$from}";

            $body = "--{$boundary}\n";
            $body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
            $body .= "\n{$plain_message}\n";
            
            $body .= "--{$boundary}\n";
            $body .= "Content-Type: {$attach_mime_type}; name=\"{$fileName}\"\n";
            $body .= "Content-Disposition: attachment; filename=\"{$fileName}\"\n";
            $body .= "Content-Transfer-Encoding: base64\n";
            $body .= "\n";
            $body .= chunk_split(base64_encode($attachment))."\n";

            $body .= "--{$boundary}--";
            $ret = mb_send_mail($to, $subject, $body, $headers);

            return $ret;
        }
    }
}
