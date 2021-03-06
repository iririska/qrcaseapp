<?php

class ClientController extends Controller
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
            array('booster.filters.BoosterFilter - delete'),
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
			array('allow', // allow authenticated client to perform 'create' and 'update' actions
				'actions'=>array('admin', 'create','update', 'delete', 'view'),
				'users'=>array('@'),
			),
			array('allow', // allow admin client to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all clients
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
        $daterangepicker = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'));
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/moment.min.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/daterangepicker.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerCssFile($daterangepicker. '/daterangepicker-bs3.css');

        $model=new Client;
        $user = User::model()->findByPk(Yii::app()->user->id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Client'])) {

			$model->attributes = $_POST['Client'];
			//$model->email = rand().$model->email;//TODO remove
            $model->google_calendar_id = ' ';

            if ($model->save()) {

                // create workflow togther with steps from WorkflowStepsByType
				// @see app/models/Workflow::afterSave
				$workflow = new Workflow();
				$workflow->client_id = $model->id;
				$workflow->case_type = $model->case_type;
				$workflow->save();
                
                $this->saveClientDocuments($model->document_list_id, $workflow->id);
                $this->redirect(array('workflow/view','id'=>$workflow->id, 'c'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
            'user'=>$user
		));
	}
    
    public function saveClientDocuments($document_list_id, $workflow_id) {
        $documentTemplate = DocumentListTemplate::model()->findByPk($document_list_id);
        if ($documentTemplate) {
            foreach ($documentTemplate->documentTemplates as $_document) {
                $doc = new Document();
                $doc->attributes = $_document->attributes;
                $doc->workflow_id = $workflow_id;
                $doc->save();
            }
        }
        return true;
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
        $daterangepicker = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'));
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/moment.min.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/daterangepicker.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerCssFile($daterangepicker. '/daterangepicker-bs3.css');

        /* @var Client $model*/
		$model=$this->loadModel($id);
        $user = $this->_user;
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if (isset($_POST['Client'])) {
            //save prev value document_list_id
            $old_document_list_id = $model->document_list_id;
            
			$model->attributes = $_POST['Client'];
			if ($model->save()) {

				//if for some reasons wokflow data is not present for current client OR checkbox to change case type ticked
				if ( empty($model->case_type) || ($_POST['Client']['change_case_type'] == 1 && !empty($_POST['Client']['case_type'])) ) {
					// create workflow togther with steps from WorkflowStepsByType
					// @see app/models/Workflow::afterSave
                    
                    $documents = false;
                    if($old_document_list_id == $model->document_list_id) //save all documents old workflow
                        $documents = $model->workflow->documents;
                    else {
                        foreach($model->workflow->documents as $doc)
                            $doc->delete();
                    }
                    
					$this->removeClientWorkflow($model);

					$workflow            = new Workflow();
					$workflow->client_id = $model->id;
					$workflow->case_type = $model->case_type;
                    if($workflow->save()) {
                        if($documents) {
                            foreach($documents as $doc) {
                                $doc->workflow_id = $workflow->id;
                                $doc->save(false);
                            }
                        }
                    }
				}
                
                if($old_document_list_id != $model->document_list_id) {
                    /*$old_Doc = Document::model()->findAllByAttributes(array('document_list_id' => $old_document_list_id, 'workflow_id' => $model->workflow->id));
                    if($old_Doc) {
                        foreach($old_Doc as $doc)
                            $doc->delete();
                    }*/
                    $this->saveClientDocuments($model->document_list_id, $model->workflow->id);
                }
                $this->redirect(array('workflow/view','id'=>$model->workflow->id, 'c'=>$model->id), true);
			}
		}

		$this->render('update',array(
			'model'=>$model,
            'user'=>$user
		));
	}

	private function removeClientWorkflow(Client $client) {
        $workflow = $client->workflow;
        foreach($workflow->steps as $step) {
            foreach($step->notes as $note) {
                $note->delete();
            }
            $step->calendarEvent->delete();
            $step->delete();
        }        
		$workflow->delete();
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 *
	 * @throws CDbException
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

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Client');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Client('searchUClient');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Client'])) {
			$model->attributes=$_GET['Client'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Client the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Client::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Client $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='client-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    private function _getNewCalendar(){

        $service = $this->_getGoogleService();

        /* @var Google_CalendarListEntry $_calendar */
        foreach ($service->calendarList->listCalendarList()->getItems() as $_calendar) {
            if ( strpos($_calendar->getSummary(), 'CRM') === false ) $service->calendarList->delete($_calendar->getId());
        }

        $calendar = new Google_Calendar();
        $calendar->setSummary('Test Calendar 2');
        $calendar->setTimeZone('America/Los_Angeles');


        $calendar = $service->calendars->insert($calendar);

        return $calendar->getId();
    }


}