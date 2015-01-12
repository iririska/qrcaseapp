<?php

class CaseController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	public $defaultAction = 'admin';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
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
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'index','view', 'delete', 'admin'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{

		Yii::app()->clientScript->registerScriptFile( Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor'). '/knockoutjs/knockout-3.2.0.js' ), CClientScript::POS_HEAD );

		$model=new WorkflowType;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$steps = $this->getItemsToUpdate();


		if(isset($_POST['WorkflowType']))
		{
			$model->attributes=$_POST['WorkflowType'];
			if($model->validate()) {
				/* @var WorkflowStepsByType[] $steps */
				if(isset($_POST['WorkflowStepsByType']))
				{
					$valid=true;
					foreach($steps as $i=>$step)
					{
						if(isset($_POST['WorkflowStepsByType'][$i])) {
							$step->attributes=$_POST['WorkflowStepsByType'][$i];
							$step->case_type = $model->id;
							$step->priority = $i;
						}
						$valid = $step->validate(array('title', 'priority')) && $valid;

						//echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($step->getErrors(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';
					}
					if($valid) {

						// Case Type if valid as well as all steps are valid
						// Then we save Case Type and all corresponding steps
						if ($model->save()) {
							foreach ( $steps as $i => $step ) {
								$step->case_type = $model->id;
								$step->save();
							}

							$this->redirect( array( 'case/admin' ) );
						}

					}
				}
			}
		}

		/*echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($model->getErrors(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

		foreach($steps as $i=>$step) {
			echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($step->getErrors(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';
		}
		*/

		$this->render('create',array(
			'model' => $model,
			'steps' => $steps,
		));
	}

	/**
	 * @param string $for_action
	 * @param null WorkflowType $model
	 *
	 * @return array
	 */
	public function getItemsToUpdate($for_action='create', $model = null) {
		// Create an empty list of records
		$items = array();

		// Iterate over each item from the submitted form
		if (isset($_POST['WorkflowStepsByType']) && is_array($_POST['WorkflowStepsByType'])) {
			foreach ($_POST['WorkflowStepsByType'] as $item) {
				// If item id is available, read the record from database
				if ( array_key_exists('id', $item) ){
					$items[] = WorkflowStepsByType::model()->findByPk($item['id']);
				}
				// Otherwise create a new record
				else {
					$items[] = new WorkflowStepsByType();
				}
			}
		} else {
			if ($for_action == 'create') {
				$items[] = new WorkflowStepsByType();
			} else {
				$items = $model->template_steps;
			}
		}
		return $items;
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{

		//We use knockout.js and angular is too heavy and not needed here
		Yii::app()->clientScript->registerScriptFile( Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor'). '/knockoutjs/knockout-3.2.0.js' ), CClientScript::POS_HEAD );

		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$steps = $this->getItemsToUpdate('update', $model);

		if(isset($_POST['WorkflowType']))
		{
			$model->attributes=$_POST['WorkflowType'];
			if($model->validate()) {
				/* @var WorkflowStepsByType[] $steps */
				if(isset($_POST['WorkflowStepsByType']))
				{
					$valid=true;
					WorkflowStepsByType::model()->deleteAll("case_type=:case_type", array(":case_type"=>$model->id));
					//Yii::app()->db->createCommand("DELETE ")
					foreach($steps as $i=>$step)
					{
						if(isset($_POST['WorkflowStepsByType'][$i])) {
							$step->attributes=$_POST['WorkflowStepsByType'][$i];
							$step->case_type = $model->id;
							//$step->priority = $i;
						}
						$valid = $step->validate(array('title', 'priority')) && $valid;
					}
					if($valid) {

						// Case Type if valid as well as all steps are valid
						// Then we save Case Type and all corresponding steps
						if ($model->save()) {
							foreach ( $steps as $i => $step ) {
								$step->case_type = $model->id;
								$step->save();
							}

							$this->redirect( array( 'case/admin' ) );
						}

					}
				}
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'steps' => $steps,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new WorkflowType('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['WorkflowType']))
			$model->attributes=$_GET['WorkflowType'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return WorkflowType the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=WorkflowType::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param WorkflowType $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='workflow-type-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
