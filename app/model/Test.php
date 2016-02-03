<?php
class Test {
	
	/**
	 * 
	 * @param string $fileName
	 */
	public function loadDat($fileName) {
		return file_get_contents(PATH_TEST . '/dat/' . $fileName);
	}
	
	/**
	 * 
	 * @param mixed $expected
	 * @param mixed $got
	 * @param string $message
	 */
	public function assertEquals($expected, $got, $message = '') {
		$e = new Exception();
		$trace = $e->getTrace()[1];
		$line = $e->getTrace()[0]['line'];
		if ($expected !== $got) {
			echo sprintf(PHP_EOL . '# ASSERTION ERROR: %s::%s (%s) %s' . PHP_EOL . 'expected:%s' . PHP_EOL . 'got:%s' . PHP_EOL,
					$trace['class'], $trace['function'], $line, $message, var_export($expected, true), var_export($got, true));
		} else {
			echo '.';
		}
	}
}