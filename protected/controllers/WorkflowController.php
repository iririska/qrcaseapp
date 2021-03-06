<?php

class WorkflowController extends Controller
{
	public $defaultAction = 'home';
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index', 'view', 'home', 'cases', 'documentdelete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'create','update', 'home', 'cases', 'documentdelete'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 *
	 * @throws CHttpException
	 */
	public function actionView($id=null)
	{

        //sleep( 10 );

		if (empty($id)) {
			throw new CHttpException("custom-400", Yii::t('app', 'Workflow error. Case type not assigned to the client? Please ' . CHtml::link('edit client', array('client/update', 'id'=>Yii::app()->request->getParam('c')))));
		}

		Yii::app()->getClientScript()->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/moment.min.js' ), CClientScript::POS_END
		);

		Yii::app()->getClientScript()->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/daterangepicker.js' ), CClientScript::POS_END
		);

		Yii::app()->getClientScript()->registerCssFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/daterangepicker-bs3.css' )
		);

        Yii::app()->getClientScript()->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.mask'). '/jquery.mask.min.js' ), CClientScript::POS_END
		);

		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'note'=> new ClientNote,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Workflow;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Workflow']))
		{
			$model->attributes=$_POST['Workflow'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		if(isset($_POST['Workflow']))
		{
			$model->attributes=$_POST['Workflow'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionCases()
	{
		/* @var User $me */
		$me = User::model()->findByPk( Yii::app()->user->getId() );

		$_clients_ids = array_keys(Client::getMyClients());

		$dataProvider=new CActiveDataProvider( 'Workflow',
			array(
				'criteria'      => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					//'order'     => 'create_time DESC',
					//'with'      => array( 'author' ),
				),
				'countCriteria' => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					// 'order' and 'with' clauses have no meaning for the count query
				),
				'pagination'    => array(
					'pageSize' => 1,
				),
			)
		);

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionHome()
	{
		/* @var User $me */
		$me = User::model()->findByPk( Yii::app()->user->getId() );
		$_clients_ids = array_keys(Client::getMyClients());
        
        $dataProvider=new CActiveDataProvider( 'Workflow',
			array(
				'criteria'      => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					//'order'     => 'create_time DESC',
					//'with'      => array( 'author' ),
				),
				'countCriteria' => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					// 'order' and 'with' clauses have no meaning for the count query
				),
				'pagination'    => array(
					'pageSize' => 2,
				),
			)
		);

		// outstanding issues
		$issues=new OutstandingIssues('searchUIssues');
		$issues->unsetAttributes();  // clear any default values
		if(isset($_GET['OutstandingIssues']))
			$issues->attributes=$_GET['OutstandingIssues'];

		// attorney actions
		$actions=new AttorneyActions('searchUAction');
		$actions->unsetAttributes();  // clear any default values
		if(isset($_GET['AttorneyActions']))
			$actions->attributes=$_GET['AttorneyActions'];

		$this->render('home',array(
			'dataProvider'=>$dataProvider,
			'issues'=>$issues,
			'actions'=>$actions,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Workflow('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Workflow']))
			$model->attributes=$_GET['Workflow'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Workflow the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Workflow::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Workflow $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='workflow-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDocumentDelete($id)
	{
		//TODO check access
		$model = Document::model()->findByPk($id);
        if ($model) $model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		else {

		}
	}


}
