<?php
require_once implode('/', [PATH_MODEL, 'Test.php']);

class SampleTest extends Test {
	public function testSample() {
		$dt = new DateTime();
		$this->assertEquals('2017', $dt->format('Y'), 'DateTime format ');
	}
	
	
}
