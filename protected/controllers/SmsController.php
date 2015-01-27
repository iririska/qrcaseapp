<?php

class SmsController extends Controller 
{
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
				'actions' => array( 'create' ),
				'users'   => array( '@' ),
			),
			array(
				'allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array( 'create' ),
				'roles'   => array( 'admin' ),
			),
			array(
				'deny',  // deny all users
				'users' => array( '*' ),
			),
		);
	}

	/**
	 * Performs the AJAX validation.
	 * @param Workflow $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sms-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @param integer $wid  workflow id
	 * @throws CHttpException
	 */
	public function actionCreate(){
		$model = new Sms;


		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Sms']))
		{
			$model->attributes=$_POST['Sms'];
			if($model->validate()){
				try{
					etsGateway::sendSMS($model->email, $model->phone, $model->carrier_code, $model->subject, $model->text);
				}catch(etsException $e){
					$model->addError('phone', $e->getMessage());
				}

			}
			if(!empty($model->errors)){
				if (Yii::app()->request->isAjaxRequest) {
					echo CJSON::encode(array(
						'status' => 'error',
						'message' => $model->errors[phone][0],
						'debug' => $model,
					));
					Yii::app()->end();
				}
			}else{
				if ($model->save(false)) {
					if (!Yii::app()->request->isAjaxRequest) {
						$this->redirect( array( 'site/index' ) ); //TODO where to redierct upon Note / ClientNote created
					} else {
						echo CJSON::encode(array(
							'status' => 'success',
							'message' => 'Sms Send',
							'debug' => $model,
						));
						Yii::app()->end();
					}
				} else {
					if (Yii::app()->request->isAjaxRequest) {
						echo CJSON::encode(array(
							'status' => 'error',
							'message' => 'Error send Sms',
							'debug' => $model,
						));
						Yii::app()->end();
					}
				}
			}

			
		}

		if (Yii::app()->request->isAjaxRequest) {
			$cs=Yii::app()->clientScript;
			$cs->scriptMap=array(
				'jquery.js'=>false,
			);
			echo CJSON::encode(
				array(
					'heading' => 'Send Text (SMS)',
					'content' => $this->renderPartial('_form',array('model'=>$model),true,true)
				)
			);
		} else {
			$this->render('create',array(
				'model'=>$model,
			));
		}
		
	}
}