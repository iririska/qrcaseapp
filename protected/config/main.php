<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'CRM',

	'sourceLanguage' => 'en',
	'language' => 'en_US',

	'defaultController' => 'workflow',

	'aliases' => array(
		'bootstrap' => realpath(__DIR__ . '/../vendor/yiistrap/'),
		'yiiwheels' => realpath(__DIR__ . '/../vendor/yiiwheels'),
		'booster' =>  realpath(__DIR__ . '/../vendor/yiibooster'),
		'fullcalendar' =>  realpath(__DIR__ . '/../vendor/fullcalendar-2.2.0'),
		'googleapi' =>  realpath(__DIR__ . '/../vendor/google-api-php-client-read-only/src'),
	),

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

		'bootstrap.behaviors.*',
		'bootstrap.components.*',
		'bootstrap.form.*',
		'bootstrap.helpers.*',
		'bootstrap.widgets.*',

		'googleapi.*',
		'googleapi.contrib.Google_CalendarService',

		/*'application.modules.auth.*',
		'application.modules.auth.components.*',*/
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'%1',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),

			'generatorPaths' => array('bootstrap.gii'),
		),

		'auth'=>array(
			'strictMode' => true,
			'userClass' => 'User',
			'userIdColumn' => 'id',
			'userNameColumn' => 'email',
			'defaultLayout' => 'application.views.layouts.column1',
			'viewDir' => null,
		)

	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'auth.components.AuthWebUser',
			'admins' => array(
				'admin',
				'zergusvcv@gmail.com',
	            //'test@test.com',
			),
		),

		'authManager' => array(
			'class'=>'CDbAuthManager',
			'connectionID'=>'db',
			'behaviors' => array(
				'auth' => array(
					'class' => 'auth.components.AuthBehavior',
				),
			),
			'defaultRoles'=>array('user'),
		),
		/*'bootstrap' => array(
			'class' => 'bootstrap.components.TbApi',
			'bootstrapPath' => Yii::getPathOfAlias('application.vendor.twbs.bootstrap'),
		),*/
		'bootstrap' => array(
			'class' => '\TbApi',
		),

		'booster' => array(
			'class' =>'booster.components.Booster', //turn on when try use boostrap
		),

		'yiiwheels' => array(
			'class' => 'yiiwheels.YiiWheels',
		),

		'format'=>array(
			'class'=>'ZFormatter',
		),

		// uncomment the following to enable URLs in path-format

		/*'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),*/

		/*'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database

		'db'=>
			(strpos( __FILE__, '/zergus' ) !== false ) ?
				array(
					'connectionString' => 'mysql:host=localhost;dbname=YII1-READYAPPS-yiiuser-yiiauth',
					'emulatePrepare'   => true,
					'username'         => 'root',
					'password'         => 'pass123',
					'charset'          => 'utf8',
					'enableParamLogging' => true,
					'enableProfiling' => true
				)
				:
				array(
					'connectionString' => 'mysql:host=localhost;dbname=qrcaseap_app',
					'emulatePrepare'   => true,
					'username'         => 'qrcaseap_usr',
					'password'         => '2KVlOeTy#l#@',
					'charset'          => 'utf8',
				)
	,

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, profile',
				),
				// uncomment the following to show log messages on web pages

				/*array(
					'class'=>'CWebLogRoute',
				),*/

			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require_once dirname(__FILE__).'/params.php',
);