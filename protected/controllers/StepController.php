<?php

class StepController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request

			array( 'booster.filters.BoosterFilter' )
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'delete', 'setstatus', 'setdates', 'setprogress', 'setpriority', 'finish'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin', 'update', 'delete', 'setstatus', 'setdates', 'setprogress', 'setpriority', 'finish'),
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
	 * @param integer $wid  workflow id
	 * @throws CHttpException
	 */
	public function actionCreate($wid=null)
	{
		$model=new Step;

		if (empty($wid)) throw new CHttpException('400', 'Invalid request. Please do not repeat this request again.');

		$model->workflow_id = (int)$wid;


		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Step']))
		{
			$model->attributes=$_POST['Step'];
			if($model->save())
				$this->redirect(array('workflow/view','id'=>$model->workflow_id, 'c'=>$model->workflow->client_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Step']))
		{
			$model->attributes=$_POST['Step'];
			if($model->save())
				$this->redirect(array('workflow/view','id'=>$model->workflow_id, 'c'=>$model->workflow->client_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($step)
	{

		$this->loadModel($step)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Step');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Step('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Step']))
			$model->attributes=$_GET['Step'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Step the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Step::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Step $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='step-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionSetStatus($step, $status){
		$step=$this->loadModel($step);
		$step->status=$status;
		$step->updated=date('Y-m-d H:i:s');
		if($step->saveAttributes(array('status', 'updated'))) {
			if ($status == Step::ATTORNEYATTN) {

				$attorneyattn = AttorneyActions::model()->find("workflow_id=:workflow AND step_id=:step AND client_id=:client", array(
					':workflow'=>$step->workflow_id,
					':step'=>$step->id,
					':client'=>$step->workflow->client_id,
				));

				//if $attorneyattn not exist, e.g. action not brought to attorney attention already then do so, save
				if (!$attorneyattn) {
					$attorneyattn = new AttorneyActions();
					$attorneyattn->workflow_id = $step->workflow_id;
					$attorneyattn->step_id = $step->id;
					$attorneyattn->client_id = $step->workflow->client_id;
					$attorneyattn->author = Yii::app()->user->id;
					$attorneyattn->save();
				}

				echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($attorneyattn->getErrors(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';
			}
			echo CJSON::encode(array(
				'status'=>'success',
				'message'=>'Status updated successfully',
				'extra' => array(
					'bg' => $step->getStatusColor(),
					'step' => $step->id,
				)
			));
		} else {
			echo CJSON::encode(array(
				'status'=>'error',
				'message'=>print_r($step->getErrors())
			));
		}
	}

	public function actionSetDates($step){
		if (Yii::app()->request->isAjaxRequest) {
			$step = $this->loadModel($step);
			$step->date_start = date( 'Y-m-d H:i:s', strtotime( Yii::app()->request->getPost('start', date('Y-m-d'))));
			$step->date_end = date( 'Y-m-d H:i:s', strtotime( Yii::app()->request->getPost('end', date('Y-m-d'))));
			if ($step->saveAttributes(array('date_start', 'date_end'))) {
				echo CJSON::encode(array(
					'status' => 'success',
					'message' => 'Dates changed successfully',
				));
			}
		}
	}

	public function actionFinish($step){
		if (Yii::app()->request->isAjaxRequest) {
			$step = $this->loadModel($step);
			if ($step->markCompleted()) {
				echo CJSON::encode(array(
					'status' => 'success',
					'message' => 'Step marked finished successfully',
					'extra' => array(
						'bg' => $step->getStatusColor(),
						'step' => $step->id
					)
				));
			}
		}
	}

	public function actionSetProgress($step){
		if (Yii::app()->request->isAjaxRequest) {
			$step = $this->loadModel($step);
			$step->progress = Yii::app()->request->getPost('progress', 0);
			if ($step->saveAttributes(array('progress'))) {
				$workflow = Workflow::model()->findByPk($step->workflow_id); /* @var Workflow $workflow */
				echo CJSON::encode(array(
					'status' => 'success',
					'message' => 'Step progress changed successfully',
					'extra' => ($workflow)?$workflow->getOverallProgress(true):'',
				));
			}
		}
	}

	public function actionSetPriority($step){
		if (Yii::app()->request->isAjaxRequest) {
			$step = $this->loadModel($step);
			$step->priority = $step->getPriorityString(Yii::app()->request->getPost('priority', 0), true);
			if ($step->saveAttributes(array('priority'))) {
				//$workflow = Workflow::model()->findByPk($step->workflow_id); /* @var Workflow $workflow */
				echo CJSON::encode(array(
					'status' => 'success',
					'message' => 'Step progress changed successfully',
					//'extra' => ($workflow)?$workflow->getOverallProgress(true):'',
				));
			}
		}
	}
}
