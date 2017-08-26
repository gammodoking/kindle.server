<?php
define('PATH_LIB', PATH_ROOT . '/lib');
define('PATH_APP', PATH_ROOT . '/app');
define('PATH_CONTROLLER', PATH_APP . '/controller');
define('PATH_MODEL', PATH_APP . '/model');
define('PATH_TEST', PATH_APP . '/test');
define('PATH_VIEW', PATH_APP . '/view');
define('PATH_CORE', PATH_ROOT . '/core');
define('PATH_CORE_CLASS', PATH_CORE . '/class');
define('PATH_VAR', PATH_CORE . '/var');

mb_internal_encoding("UTF-8");

if (strpos(getcwd(), 'dev') !== false) {
	define('ENV', 'DEV');
} else {
	define('ENV', 'PROD');
}

if (is_prod()) {
	require_once implode('/', [PATH_CORE, 'conf_prod.php']);
} else {
	require_once implode('/', [PATH_CORE, 'conf_dev.php']);
}

function is_prod() {
	return ENV === 'PROD';
}



function d($log = '') {
	output(getTrace(), $log);
}

function i($log) {
	output(getTrace(), $log);
}

function std($log) {
	echo $log . PHP_EOL;
}

/**
 * 
 * @param mixed $log
 */
function toString($log) {
	if ($log instanceof Exception) {
		$log = sprintf("%s(%d) %s\n%s", $log->getFile(), $log->getLine(), $log->getMessage(), $log->getTraceAsString());
	} else {
		$log = var_export($log, true);
	}
	return $log;
}

function output($trace, $log) {
	$log = toString($log);
	$dt = new DateTime();
	$str = sprintf('%s %s(%s) %s' . PHP_EOL, $dt->format('Y-m-d H:i:s'), pathinfo($trace['file'], PATHINFO_BASENAME) , $trace['line'], $log);
	file_put_contents(PATH_VAR . '/app.log', $str, FILE_APPEND);
}

function getTrace() {
	$trace = debug_backtrace();
	return $trace[1];
}

function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // このエラーコードが error_reporting に含まれていない場合
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");
