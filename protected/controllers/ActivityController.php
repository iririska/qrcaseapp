<?php

class ActivityController extends Controller {
	public $defaultAction = 'index';
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array(
				'allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions' => array( 'record' ),
				'users'   => array( '@' ),
			),
			array(
				'allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array( 'record' ),
				'roles'   => array( 'admin' ),
			),
			array(
				'deny',  // deny all users
				'users' => array( '*' ),
			),
		);
	}

	public function actionRecord() {
		if ( empty( $_POST['type'] ) ) {
			throw new CHttpException( 403, 'Invalid input' );
		}

		$model                = new Activity;
		$model->activity_type = $_POST['type'];

		if ( $model->save() ) {
			echo CJSON::encode( array(
				'status'  => 'success',
				'message' => 'Activity successfully registered',
			) );
		} else {
			echo CJSON::encode( array(
				'status'  => 'error',
				'message' => $model->getErrors(),
			) );
		}
		Yii::app()->end();
		//$this->render('record');
	}
}