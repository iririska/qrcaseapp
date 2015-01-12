<?php

class NoteController extends Controller
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
				'actions'=>array('index','view', 'create','update', 'admin', 'delete'),
				'users'=>array('@'),
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
		$model = $this->loadModel( $id );

		if (Yii::app()->request->isAjaxRequest) {
			$this->layout = false;
			echo CJSON::encode(
				array(
					'heading' => date(Yii::app()->params["fullDateFormat"], strtotime($model->created)),
					'content' => CHtml::encode($model->content),
				)
			);
		} else {

			$this->render( 'view', array(
				'model' => $model
			) );
		}
	}


	public function actionCreate($s)
	{

		if (isset($_GET['clientnote'])) {
			$model = new ClientNote;
			$model->client_id = (int)$s;
			$_type = 'ClientNote';
		} else {
			$model = new Note;
			$model->step_id = (int)$s;
			$_type = 'Note';
		}

		$model->author = Yii::app()->user->getId();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if (isset($_POST[$_type])) {

			$model->attributes=$_POST[$_type];
			if ($model->save()) {

				if ($_type == 'ClientNote') {
					//save Activity record
					$model2                = new Activity;
					$model2->activity_type = $_POST[$_type]['activity_type'];
					$model2->note_id       = $model->id;
					if ( $model2->save() ) {
						// nothing
					} else {
						print_r( $model2->getErrors() );
						exit;
						// TODO errors handling
					}
				}


				if (!Yii::app()->request->isAjaxRequest) {
					if ($_type == 'Note') {
						$this->redirect( array( 'workflow/view', 'id' => $model->step->workflow_id ) );
					} else {
						$this->redirect( array( 'site/index' ) ); //TODO where to redierct upon Note / ClientNote created
					}
				} else {
					echo CJSON::encode(array(
						'status' => 'success',
						'message' => 'Note added',
						'debug' => $model,
					));
					Yii::app()->end();
				}
			} else {
				if (Yii::app()->request->isAjaxRequest) {
					echo CJSON::encode(array(
						'status' => 'error',
						'message' => 'Error adding note',
						'debug' => $model,
					));
					Yii::app()->end();
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
					'heading' => 'Add note to the step #' . (int)$s,
					'content' => $this->renderPartial('_form',array(
							'model'=>$model,
						),
						true,
						true
					)
				)
			);
		} else {
			$this->render('create',array(
				'model'=>$model,
			));
		}
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

		if (isset($_POST['Note'])) {
			$model->attributes=$_POST['Note'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
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
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		} else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Note');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Note('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Note'])) {
			$model->attributes=$_GET['Note'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Note the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		if (Yii::app()->request->getParam('type') == 'clientnote') {
			$model = ClientNote::model()->findByPk( $id );
		} else {
			$model = Note::model()->findByPk( $id );
		}
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Note $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && ($_POST['ajax']==='note-form' || $_POST['ajax']==='client-note-form' || $_POST['ajax']==='step-note-form') ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}