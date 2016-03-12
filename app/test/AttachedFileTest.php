<?php
require_once implode('/', [PATH_MODEL, 'Test.php']);
require_once implode('/', [PATH_CORE_CLASS, 'AttachedFile.php']);

class AttachedFileTest extends Test {
	
	public function testNormalAttachedFile() {
        $params = [
            AttachedFile::KEY_NAME => 'a',
            AttachedFile::KEY_TYPE => 'b',
            AttachedFile::KEY_TMP_NAME => 'c',
            AttachedFile::KEY_ERROR => UPLOAD_ERR_OK,
            AttachedFile::KEY_SIZE => 10,
        ];
        
        $file = new AttachedFile('d', $params);
        
		$this->assertEquals('a', $file->name);
		$this->assertEquals('b', $file->type);
		$this->assertEquals('c', $file->tmpName);
		$this->assertEquals('d', $file->fieldKey);
		$this->assertEquals(UPLOAD_ERR_OK, $file->error);
		$this->assertEquals(10, $file->size);
        
		$this->assertEquals(false, $file->isError());
	}
    
	public function testErrorAttachedFile() {
        $params = [
            AttachedFile::KEY_NAME => 'a',
            AttachedFile::KEY_TYPE => 'b',
            AttachedFile::KEY_TMP_NAME => 'c',
            AttachedFile::KEY_ERROR => UPLOAD_ERR_NO_FILE,
            AttachedFile::KEY_SIZE => 10,
        ];
        
        $file = new AttachedFile('d', $params);
        
		$this->assertEquals(true, $file->isError());
	}
	
}
