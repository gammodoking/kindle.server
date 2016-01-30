<?php

require_once implode('/', [PATH_MODEL, 'Kindle.php']);

class SampleTest extends Test {
	public function testSample() {
		
		$dt = new DateTime();
		echo $dt->format('Y-m-d H:i:s');
		$this->assertEquals('2016', $dt->format('Y'), 'DateTime format ');
	}
	
	
}
