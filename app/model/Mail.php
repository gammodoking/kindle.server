<?php

class Mail {
	
	private $sendto = '';
	private $from = '';
	private $body = '';
	private $fileName = '';
    
    /**
     *
     * @var binary
     */
	private $file = null;
	
	
	public function setSendto($sendto) {
		$this->sendto = $sendto;
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function setBody($body) {
		$this->body = $body;
	}
	
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}
	
	public function setFile($file) {
		$this->file = $file;
	}
	
	public function __toString() {
		return sprintf('sendto:%s, from:%s, fileName:%s, body:%s, file:%s',
				$this->sendto,
				$this->from,
				$this->fileName,
				$this->body,
				$this->file ? 'has file' : 'no file'
				);
	}
	
	public function send() {
		return self::sendKindleFasade($this->from, $this->sendto, $this->file, $this->fileName);
	}
	
	public static function sendKindleFasade($mailfrom, $mailto, $binFile, $fileName) {
		mb_language('Ja') ;
		mb_internal_encoding('UTF-8');
		$mime = 'text/plain';
		$mime = 'application/octet-stream';
		return Mail::send_attached_mail($mailto, '', '', $mailfrom, $binFile, $fileName, $mime);
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
	
	public static function send_attached_mail(
			$to, $subject, $plain_message, $from,
			$attachment = null, $fileName = null, $attach_mime_type = null) {
        if ($attachment === null) {
            self::send_mail($to, $subject, $plain_message, $from);
        } else {
			$fileName = mb_encode_mimeheader( mb_convert_encoding( basename( $fileName ) ,  "ISO-2022-JP" , 'auto' ) );
			$from = mb_encode_mimeheader( mb_convert_encoding( basename( $from ) ,  "ISO-2022-JP" , 'auto' ) );
            //必要に応じて適宜文字コードを設定してください。
            mb_language('Ja');
            mb_internal_encoding('UTF-8');

            $boundary = '__BOUNDARY__'.md5(rand());
                        
            $headers = "Content-Type: multipart/mixed;boundary=\"{$boundary}\"\n";
            $headers .= "From: {$from}<{$from}>\n";
            $headers .= "Reply-To: {$from}\n";

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
