<?php
require_once implode('/', [PATH_MODEL, 'Test.php']);

class TestTest {
	public function testTest() {
		$test = new Test();
//		$test->assertEquals(1, 0, '失敗を確認');
		$dat = $test->loadDat('test.dat');
		$test->assertEquals('test', $dat);
	}
}
