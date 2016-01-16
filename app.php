<?php
define('PATH_ROOT', __DIR__);
require_once implode('/', [PATH_ROOT, 'core/bootstrap.php']);

require_once implode('/', [PATH_CORE_CLASS, 'FrontController.php']);
$app = new FrontController();
$app->exec();
