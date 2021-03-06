<?php

// change the following paths if necessary
$yii = (strpos( __FILE__, '/zergus' ) !== false ) ? dirname(__FILE__).'/../yii-1.1.15/framework/yii.php' : dirname(__FILE__).'/../../yii-1.1.16-framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

//SMS library
require_once(dirname(__FILE__).'/protected/vendor/sms/classes/ets_old.php');
require(dirname(__FILE__).'/protected/vendor/google-api-php-client_/autoload.php');

require_once($yii);
Yii::createWebApplication($config)->run();
