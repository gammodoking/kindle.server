<?php
define('PATH_ROOT', implode('/', [__DIR__, '../..']));
require_once implode('/', [PATH_ROOT, 'core/bootstrap.php']);


$files = scandir('./');

foreach ($files as $file) {
	if (preg_match('/.*Test\.php/', $file) !== 1) {
		continue;
	}
	
	$filePath = __DIR__ . '/' . $file;
	if (!file_exists($filePath) && !is_file($filePath)) {
		continue;
	}
	
	require_once __DIR__ . '/' . $file;
	$className = explode('.', $file)[0];
	$class = new ReflectionClass($className);
	foreach ($class->getMethods() as $method) {
		if (preg_match('/test.*/', $method) !== 1) {
			continue;
		}
		$reflectionMethod = new ReflectionMethod($class->getName(), $method->name);
		$reflectionMethod->invoke($class->newInstance());
	}
	
	echo PHP_EOL;
}