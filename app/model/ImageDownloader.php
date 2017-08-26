<?php
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
			try {
				$this->dirBuilder->asyncDownload(Service::kindlePath($imgUrl), $imgUrl->url);
                
//                同期ダウンロード
//				$imgFile = file_get_contents($imgUrl->url);
//				$this->dirBuilder->putImage(Service::kindlePath($imgUrl), $imgFile);
			} catch (Throwable $e) {
				// 無視
				d($imgUrl);
				d($e);
			}
		}

        try {
            $waitList = [1, 3, 5, 8];
            foreach ($waitList as $waitSec) {
                $files = [];
                d("ls {$this->dirBuilder->getTmpDir()} ");
                $r = exec("ls {$this->dirBuilder->getTmpDir()} ", $files);
                d($r);
                d($files);
                $isDownloading = false;
                foreach ($files as $file) {
                    if (strpos($file, 'downloading') !== false) {
                        $isDownloading = true;
                        break;
                    }
                }
                if (!$isDownloading) {
                    break;
                }

                sleep($waitSec);
            }
        } catch (Throwable $e) {
            // 無視
            d($imgUrl);
            d($e);
        }
	}
}
