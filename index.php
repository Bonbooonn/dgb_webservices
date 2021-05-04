<?php
date_default_timezone_set('Asia/Manila');
define('DIR', dirname(__FILE__));

require "lib/router.php";
require "lib/const.php";
require "lib/orm.php";
require "lib/db_config.php";
require "lib/logger.php";

$app['router'] = new Router();

$app['db'] = new DBConfig();
$app['logger'] = new Logger();

require "controller/base_controller.php";
require "model/base_model.php";
require "model/models.php";
require_once "routes/routes.php";

$app['router']->run();
?>