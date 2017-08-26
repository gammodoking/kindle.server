<?php
class DirectoryBuilder {
	
	private $baseDir;
	private $imgDir;
	private $cssDir;
    private $tmpDir;
	private $indexHtml;
	private $mobi;
	
	function __construct() {
		$dateTime = new DateTime();
		$rand = rand(0, 99999);
		$this->baseDir = PATH_VAR . sprintf('/html_%s_%05d', $dateTime->format('YmdHis'), $rand);
		$this->imgDir = $this->baseDir . '/images';
		$this->cssDir = $this->baseDir . '/css';
		$this->tmpDir = $this->baseDir . '/tmp';
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

    /**
     * バックグラウンドでファイルをダウンロードする
     * ダウンロード中は$prefixをプレフィックスとするファイルを作成する
     * @param string $kindleImagePath
     * @param string $downloadUrl
     * @param string $prefix
     * @return string filename
     */
	public function asyncDownload($kindleImagePath, $downloadUrl, $prefix = 'downloading_') {
        $tmpFilepath = $this->tmpDir . '/' . $prefix . urlencode($downloadUrl);
        
		$filePath = $this->baseDir . '/' . $kindleImagePath;
		$dir = pathinfo($filePath, PATHINFO_DIRNAME);
		$ret = @mkdir($dir, 0777, true);
        exec("(touch $tmpFilepath ; curl $downloadUrl -o $filePath ; rm -f $tmpFilepath ) &");
        return $tmpFilepath;
	}
    
	public function getContentsPath() {
		return $this->indexHtml;
	}
	
	public function getMobiPath() {
		return $this->mobi;
	}
	
	public function remove() {
		$out = [];
		$res = exec(sprintf('rm -rf %s', $this->baseDir), $out);
		if ($out) {
			d($res);
			d($out);
		}
	}
    
    public function getTmpDir() {
        return $this->tmpDir;
    }
}
