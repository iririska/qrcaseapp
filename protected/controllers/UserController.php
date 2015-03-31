<?php

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	public $defaultAction='admin';

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
            array('allow', // admin permissions
				'actions'=>array('create', 'update', 'admin', 'view', 'delete', 'updatepassword', 'assign'),
				'roles'=>array('admin'),
			),

			array('allow', // all logged users
				'actions'=>array('update', 'updatepassword'),
				'roles'=>array('user'),
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
	public function actionCreate() {
		$model=new User('createUser');
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);
		if (isset($_POST['User'])) {
			$model->attributes=$_POST['User'];
			if ($model->save()) {
				$this->redirect(array('admin'));
			}
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
		// return Yii::app()->user->id==$data->id || Yii::app()->user->role == 'admin';

		$model=$this->loadModel($id);

		if(!Yii::app()->user->checkAccess( 'updateOwnData', array( 'user'=>$model )) ) throw new CHttpException(403, 'You can edit only your own data');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['User'])) {
			$model->attributes=$_POST['User'];
			if ($model->save()) {
				//$this->redirect(array('view','id'=>$model->id));
				$this->redirect(array('admin'));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionUpdatePassword($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['User'])) {
			$model->attributes=$_POST['User'];
			if ($model->save()) {
				echo CJSON::encode(array(
					'status' => 'success',
					'message' => 'Password updated successfully',
				));
				Yii::app()->end();
			}
		}

	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 * @throws CHttpException
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

	public function actionAssign(){
		$clients = Client::getMyClients();
        $users = CHtml::listData( User::model()->findByPk(Yii::app()->user->id)->paralegal, 'id' , 'email' );
        
		$assignments = self::getAssignmentsArray();

		if (isset($_POST['ClientUser'])) {

			$this->clearAssignments();

			foreach ( $_POST['ClientUser'] as $client_id => $user_ids ) {
				foreach ( $user_ids as $user_id ) {
					$clientuser            = new ClientUser();
					$clientuser->client_id = $client_id;
					$clientuser->user_id   = $user_id;
					$clientuser->save();

				}
			}
            Controller::addAlert('assign','User-client assignments were successfully updated.');
			$this->redirect('/user/assign');
		}

		$this->render('assign',array(
			'clients'=>$clients,
			'users'=>$users,
			'assignments'=>$assignments,
		));
	}

	/**
	 * Lists all models.
	 */
	/*
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}*/

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $model=new User('searchAccountUsers');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['User'])) {
			$model->attributes=$_GET['User'];
        }
        $dataProvider = $model->searchAccountUsers();
        
		$this->render('admin',array(
			'model'=>$model,
            'dataProvider'=>$dataProvider
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='user-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public static function getAssignmentsArray() {
		$ret = Yii::app()->db->createCommand()
		              ->select('client_id, user_id')
		              ->from( 'ClientUser' )
		              ->queryAll( true );
		$assignments = array();

		foreach ( $ret as $assignment_item ) {
			$assignments[$assignment_item['client_id']][] = $assignment_item['user_id'];
		}

		return $assignments;
	}

	public function clearAssignments(){
		Yii::app()->db->createCommand("TRUNCATE ClientUser")->query();
	}
}