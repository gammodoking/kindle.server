<?php
class AttachedFile {
    
    const KEY_NAME = 'name';
    const KEY_TYPE = 'type';
    const KEY_TMP_NAME = 'tmp_name';
    const KEY_ERROR = 'error';
    const KEY_SIZE = 'size';
    
    /**
     *
     * @var string
     */
	public $fieldKey;
    
    /**
     *
     * @var string
     */
	public $name;
    
    /**
     *
     * @var string
     */
	public $type;
    
    /**
     *
     * @var string
     */
	public $tmpName;
    
    /**
     *
     * @var int
     */
	public $error;
    
    /**
     *
     * @var int
     */
	public $size;
	
    /**
     * 
     * @param string $fieldKey
     * @return boolean
     */
	public static function isAttached($fieldKey) {
        return isset($_FILES[$fieldKey]);
    }
    
    /**
     * 
     * @param string $fieldKey
     * @return \AttachedFile
     */
	public static function newInstance($fieldKey) {
        $params = $_FILES[$fieldKey];
		return new AttachedFile($fieldKey, $params);
	}

	/**
	 * 
	 * @param string $url
	 */
	function __construct($fieldKey, array $params) {
        $this->fieldKey = $fieldKey;
        $this->name = $params[self::KEY_NAME];
        $this->type = $params[self::KEY_TYPE];
        $this->tmpName = $params[self::KEY_TMP_NAME];
        $this->error = $params[self::KEY_ERROR];
        $this->size = $params[self::KEY_SIZE];
	}
    
    public function saveToDir($dirPath) {
        $ret = move_uploaded_file($this->tmpName, $dirPath);
        if (!$ret) {
            throw new Exception('ファイルの移動に失敗しました');
        }
    }
    
    public function isError() {
        return $this->error !== UPLOAD_ERR_OK;
    }
    
    public function getMessage() {
        if ($this->error === UPLOAD_ERR_OK) {
            return '値: 0; エラーはなく、ファイルアップロードは成功しています。';
        }
        if ($this->error === UPLOAD_ERR_INI_SIZE) {
            return '値: 1; アップロードされたファイルは、php.ini の if ($this->error === upload_max_filesize ディレクティブの値を超えています。) {';
        }
        if ($this->error === UPLOAD_ERR_FORM_SIZE) {
            return '値: 2; アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。';
        }
        if ($this->error === UPLOAD_ERR_PARTIAL) {
            return '値: 3; アップロードされたファイルは一部のみしかアップロードされていません。';
        }
        if ($this->error === UPLOAD_ERR_NO_FILE) {
            return '値: 4; ファイルはアップロードされませんでした。';
        }
        if ($this->error === UPLOAD_ERR_NO_TMP_DIR) {
            return '値: 6; テンポラリフォルダがありません。PHP 5.0.3 で導入されました。';
        }
        if ($this->error === UPLOAD_ERR_CANT_WRITE) {
            return '値: 7; ディスクへの書き込みに失敗しました。PHP 5.1.0 で導入されました。';
        }
        if ($this->error === UPLOAD_ERR_EXTENSION) {
            return '値: 8; PHP の拡張モジュールがファイルのアップロードを中止しました。 どの拡張モジュールがファイルアップロードを中止させたのかを突き止めることはできません。 読み込まれている拡張モジュールの一覧を phpinfo() で取得すれば参考になるでしょう。 PHP 5.2.0 で導入されました。';
        }        
    }
    
}
