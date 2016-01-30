<?php

class DirectoryBuilder {
	
	private $baseDir;
	private $imgDir;
	private $cssDir;
	private $indexHtml;
	private $mobi;
	
	function __construct() {
		$dateTime = new DateTime();
		$rand = rand(0, 99999);
		$this->baseDir = PATH_VAR . sprintf('/html_%s_%05d', $dateTime->format('YmdHis'), $rand);
		$this->imgDir = $this->baseDir . '/images';
		$this->cssDir = $this->baseDir . '/css';
		$this->indexHtml = $this->baseDir . '/index.html';
		$this->mobi = $this->baseDir . '/index.mobi';
	}
	
	public function build() {
		$ret = @mkdir($this->baseDir, 0777, true);
		$ret = @mkdir($this->imgDir, 0777, true);
		$ret = @mkdir($this->cssDir, 0777, true);
		return $ret;
	}
	
	public function putContents($contents) {
		return file_put_contents($this->indexHtml, $contents);
	}
	
	public function putImage($kindleImagePath, $imgFile) {
		$file = $this->baseDir . '/' . $kindleImagePath;
		$dir = pathinfo($file, PATHINFO_DIRNAME);
		$ret = @mkdir($dir, 0777, true);
		return file_put_contents($file, $imgFile);
	}
	
	public function getContentsPath() {
		return $this->indexHtml;
	}
	
	public function getMobiPath() {
		return $this->mobi;
	}
	
	public function remove() {
		d(sprintf('rm -rf %s', $this->baseDir));
		$res = exec(sprintf('rm -rf %s', $this->baseDir), $out);
		d($res);
		d($out);
		if ($out) {
			Log::d($out);
		}
	}
}

class View {
	public static function render($content) {
		echo sprintf('<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="">
<!--&#91;if lt IE 9&#93;>
<script src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<!&#91;endif&#93;-->
<link rel="shortcut icon" href="">
</head>
<body>%s
</body>
</html>', $content);
	}
}

class MailTest {
	public static function test() {
		// メールで日本語使用するための設定をします。
		mb_language("Ja") ;
		mb_internal_encoding("UTF-8");

		$mailto = "nqmxt983@yahoo.co.jp";
		$mailto = "nqmxt983_80@kindle.com";
		$subject = "送信テスト1";
		$content = $_POST['content'] ?: "やったね送れたね。a";
		$mailfrom = "raix5867@gmail.com";

		//Mail::send_mail($mailto, $subject, $content, $mailfrom);
		$fileName = 'テストファイル.txt';
		$mime = 'text/plain';
		echo Mail::send_attached_mail($mailto, $subject, $content, $mailfrom, $content, $fileName, $mime);
		exit();
		
	}
}

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

class ContentsDownloader {
	
	private $url;
	private $encodedContents;
	
	private $info;
	private $error;
	private $result;
	
	function __construct($url) {
		$this->url = $url;
	}
	
	public function exec() {
		//$this->result = file_get_contents($this->url);
		
//		ブログによっては403ではじかれる。ユーザーエージェント？IP？
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		//curl_setopt($ch, CURLOPT_HEADER, true);


		$headers = array(
		    "HTTP/1.0",
		    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		    "Accept-Encoding:gzip ,deflate",
		    "Accept-Language:ja,en-us;q=0.7,en;q=0.3",
		    "Connection:keep-alive",
		    "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0"
		    );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$this->result = curl_exec($ch);
		$this->error = curl_error($ch);
		$this->info = curl_getinfo($ch);
		curl_close ($ch);

		$this->encodedContents = mb_convert_encoding($this->result, 'HTML-ENTITIES', 'UTF-8');
	}
	
	public function encodedContents() {
		return $this->encodedContents;
	}
	
	public function url() {
		return $this->url;
	}
	
	public function result() {
		return $this->result;
	}
	
	public function info() {
		return $this->info;
	}
	
	public function error() {
		return $this->error;
	}
	
}

class ImageDownloader {
	private $domElement;
	
	/**
	 * @var Url
	 */
	private $url;
	private $dirBuilder;
	
	function __construct(DomElement $domElement, Url $url, DirectoryBuilder $dirBuilder) {
		$this->domElement = $domElement;
		$this->url = $url;
		$this->dirBuilder = $dirBuilder;
	}
	
	public function exec() {
		$imgs = $this->domElement->getElementsByTagName('img');
		foreach ($imgs as $img) {
			$src = $img->getAttribute('src');
			$imgUrl = Url::parseRelative($this->url->url, $src);
			$imgFile = file_get_contents($imgUrl->url);
			$this->dirBuilder->putImage($imgUrl->kindlePath(), $imgFile);
		}
	}
}

class ContentExtractor {
	
	private $highScore;
	public $contentNode;
	public $title;
	
	public $score;
	
	private $skipNode = array(
		'#text',
		'script',
		'link',
		'header',
		'footer',
	);
	
	private $content = '';
	
	/**
	 *
	 * @params string $content
	 */
	function __construct($content) {
		$this->content = $content;
	}
	
	public function exec() {
		$doc = new DomDocument();
		@$doc->loadHTML($this->content);
		$this->title = $doc->getElementsByTagName('title')->item(0)->textContent;
		$node = $doc->getElementsByTagName('body')->item(0);
		$this->highScore = 0;
		$this->contentNode = null;
		$this->scan($node);
	}
	
	public function scan(DomNode $node) {
		if (!$node->childNodes) {
			return;
		}
		
		$score = $this->calcScore($node);
		
		if ($score >= $this->highScore) {
			$this->highScore = $score;
			$this->contentNode = $node;
		}
		
		foreach ($node->childNodes as $n) {
			if (in_array($n->nodeName, $this->skipNode)) {
				continue;
			}
			self::scan($n);
		}
	}

	private $domCount = 0;
	
	private function calcScore(DomNode $node) {
		$this->domCount = 0;
		
		$text = $this->getTextFromNode($node);
		$params = array();
		$params['domCount'] = $this->domCount;
		$params['textLength'] = mb_strlen($text);
		$params['toten'] = count(preg_split('/,/', $text)) - 1 + count(preg_split('/、/', $text)) - 1;
		$params['kuten'] = count(preg_split('/./', $text)) - 1 + count(preg_split('/。/', $text)) - 1;
		$params['copyright'] = count(preg_split('/copyright/', $text)) - 1;
		
		$params['negativeTagCount'] = $node->getElementsByTagName('a')->length;// ? $node->getElementsByTagName('footer').length : 0;
		$params['ngTagCount'] = $node->getElementsByTagName('footer')->length
			 + $node->getElementsByTagName('header')->length;
		
		$score = 
			$params['toten'] * 1
			+ $params['kuten'] * 1
//			+ $params['textLength'] / $params['domCount']
			- 100 * $params['copyright']
			- $params['domCount'] * 10
			- $params['negativeTagCount'] * 20
			- $params['ngTagCount'] * 10000
			;
			
		$this->score = $params;
		return (int)$score;
	}

	private function getTextFromNode($Node, $Text = "") {
		
	    if (@$Node->tagName == null) 
	        return $Text . $Node->textContent;
	    
		if (!in_array($Node->nodeName, $this->skipNode)) {
		    $this->domCount += 1;
		}

	    $Node = $Node->firstChild;
	    if ($Node != null) 
	        $Text = $this->getTextFromNode($Node, $Text); 

	    while(@$Node->nextSibling != null) { 
	        $Text = $this->getTextFromNode($Node->nextSibling, $Text);
	        $Node = $Node->nextSibling; 
	    } 
	    return $Text; 
	} 
	
}

class ContentsNormalizer {
	private $url;
	private $title;
	private $contentNode;
	
	function __construct($url, $title, $contentNode) {
		$this->url = $url;
		$this->title = $title;
		$this->contentNode = $contentNode;
	}
	
	public function exec() {
		$this->normalize();
	}
	
	public function getHtml() {
		$htmlContent = $this->contentNode->C14N();
		return sprintf('<!DOCTYPE html><html><head><title>%s</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>%s</body></html>',
			$this->title, $htmlContent);
	}
	
	private function normalize() {
		// a hrefを絶対パスに変更
		$u = Url::parse($this->url);
		foreach ($this->contentNode->getElementsByTagName('a') as $element) {
			$href = $element->getAttribute('href');
			if (strpos($href, 'http') === 0 || strpos($href, '#') === 0) {
				continue;
			}
			if (strpos($href, '/') === 0) {
				$element->setAttribute('href', $u->scheme . $u->host . $href);
			} else if (strpos($href, './') === 0) {
				$element->setAttribute('href', $u->scheme . $u->host . $u->path . substr($href, 2));
			}
		}
		
		// img srcをdomain/相対パスに変更 hoge.domain.com/s/r/c.png
		foreach ($this->contentNode->getElementsByTagName('img') as $element) {
			$src = $element->getAttribute('src');
			$s = Url::parseRelative($this->url, $src);
			$element->setAttribute('src', $s->kindlePath());
		}
	}
}

class KindleGenCommand {
	private $COMMAND;
	
	private $inputFile;
	private $outputFile;
	
	public $output;
	public $result;
	
	public static function newInstance($inputFile, $outputFile) {
		$command = new KindleGenCommand();
		$command->setInputFile($inputFile);
		$command->setOutputFile($outputFile);
		return $command;
	}
	
	function __construct() {
		$this->COMMAND = PATH_LIB . '/kindlegen';
	}
	
	public function setInputFile($inputFile) {
		$this->inputFile = $inputFile;
	}
	
	public function setOutputFile($outputFile) {
		$this->outputFile = $outputFile;
	}
	
	public function exec() {
		exec(sprintf('%s %s -o %s', $this->COMMAND, $this->inputFile, $this->outputFile), $this->output, $this->result);
	}
}

class Url {
	
	public $url;
	public $scheme;
	public $host;
	public $path;
	public $file;
	public $qeury;
	public $fragment;
	
	public static function parse($url) {
		return new Url($url);
	}
	
	/**
	 * srcやhrefの相対パスをbaseUrlを元に絶対URLに変換してUrlインスタンスを返す
	 * string
	 */
	public static function parseRelative($baseUrl, $targetUrl) {
		if (strpos($targetUrl, 'http') === 0) {
			return self::parse($targetUrl);
		}
		
		$baseUrlObj = self::parse($baseUrl);
		if (strpos($targetUrl, '/') === 0) {
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $targetUrl);
		}
		
		if (!$baseUrl) {
			throw new Exception();
		}

		if (strpos($href, './') === 0) {
			$src = substr($targetUrl, 2);
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $baseUrlObj->path . '/' . $src);
		} else {
			return self::parse($baseUrlObj->scheme . $baseUrlObj->host . $baseUrlObj->path .  '/' . $targetUrl);
		}
		
		throw new Exception();
	}
	
	function __construct($url) {
		$this->url = $url;
		$this->scheme = parse_url($url, PHP_URL_SCHEME) ? parse_url($url, PHP_URL_SCHEME) . '://' : '';
		$this->host = parse_url($url, PHP_URL_HOST) ?: '';
		$this->path = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_DIRNAME);
		$this->path = $this->path === '/' ? '' : $this->path;
		$this->file = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);
		$this->qeury = parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '';
		$this->fragment = parse_url($url, PHP_URL_FRAGMENT) ? '#' . parse_url($url, PHP_PHP_URL_FRAGMENTURL_QUERY) : '';
	}
	
	public function kindlePath() {
		return 'images/' . $this->host . $this->path . '/' . $this->file;
	}
}

class Log {
	public static function d($log) {
		self::out(self::getTrace(), $log);
	}
	public static function i($log) {
		self::out(self::getTrace(), $log);
	}
	
	public static function out($trace, $log) {
		echo sprintf('%s %s %s<br />%s', pathinfo($trace['file'], PATHINFO_BASENAME) , $trace['line'], var_export($log, true), PHP_EOL);
	}
	
	public static function getTrace() {
		$trace = debug_backtrace();
		return $trace[1];
	}
}

class Test {
	protected function assertEquals($expected, $got, $message = '') {
		$e = new Exception();
		$trace = $e->getTrace()[1];
		$line = $e->getTrace()[0]['line'];
		$file = array_pop(explode('/', $trace['file']));
		if ($expected !== $got) {
			echo sprintf(PHP_EOL . '# ASSERTION ERROR: %s::%s (%s) %s' . PHP_EOL . 'expected:%s' . PHP_EOL . 'got:%s' . PHP_EOL,
					$trace['class'], $trace['function'], $line, $message, var_export($expected, true), var_export($got, true));
		} else {
			echo '.';
		}
	}
}