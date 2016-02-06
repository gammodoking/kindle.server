<?php
define('PATH_ROOT', implode('/', [__DIR__, '../..']));
require_once implode('/', [PATH_ROOT, 'core/bootstrap.php']);

// 第一引数の正規表現にマッチするメソッドのみテスト
$filterMethodRegexp = '';
if (isset($argv[1])) {
	$filterMethodRegexp = $argv[1];
}

$files = scandir('./');

foreach ($files as $file) {
	if (preg_match('/.*Test\.php/', $file) !== 1) {
		continue;
	}
	
	$filePath = __DIR__ . '/' . $file;
	if (!file_exists($filePath) && !is_file($filePath)) {
		continue;
	}
	
	echo PHP_EOL . $file . ' | ';
	require_once __DIR__ . '/' . $file;
	$className = explode('.', $file)[0];
	$class = new ReflectionClass($className);
	foreach ($class->getMethods() as $method) {
		if (preg_match('/test.*/', $method) !== 1) {
			continue;
		}
		
		if ($filterMethodRegexp && preg_match('#' . $filterMethodRegexp . '#', strtolower($method)) !== 1) {
			continue;
		}
		
		$reflectionMethod = new ReflectionMethod($class->getName(), $method->name);
		try {
			$reflectionMethod->invoke($class->newInstance());
		} catch (Exception $e) {
			std(toString($e));
			d($e);
		}
	}
	
	echo PHP_EOL;
}