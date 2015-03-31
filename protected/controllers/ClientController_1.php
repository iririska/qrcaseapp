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
	public function filters() {
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
	public function accessRules() {
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
	public function actionView($id) {
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {

        /*echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($service->calendarList->listCalendarList(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        $rule = new Google_AclRule ();
        $scope = new Google_AclRuleScope();

        $scope->setType("user");
        $scope->setValue("180333236740-g7cq40idglikjnumfg65310ujuesk653@developer.gserviceaccount.com");
        $rule->setScope($scope);
        $rule->setRole("owner");

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($service->acl->listAcl($calendar->getId()), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        try {
            $event = new Google_Event();
            $event->setSummary( 'test event 2' );
            //$event->setLocation( $model->location );
            //$event->setDescription( $model->description );

            $start = new Google_EventDateTime();
            $start->setDateTime( date('c', strtotime('2015-02-05')) );
            $start->setTimeZone( 'America/Los_Angeles' );
            $event->setStart( $start );

            if (!empty($model->end)) {
                $end = new Google_EventDateTime();
                $end->setDateTime( date('c', strtotime('2015-02-06')) );
                $end->setTimeZone( 'America/Los_Angeles' );
                $event->setEnd( $end );
            } else {
                $end = new Google_EventDateTime();
                $end->setDateTime( date('c', strtotime('2015-02-05')+24*3600) );
                $end->setTimeZone( 'America/Los_Angeles' );
                $event->setEnd( $end );
            }

            $insertedEvent = $service->events->insert( $calendar->getId(), $event );
            Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($insertedEvent->getId(),1) . "\n------------------\n");
        } catch(Google_ServiceException $e) {
            Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($e->getMessage(),1) . "\n------------------\n");
        }

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($service->events->listEvents(
                $calendar->getID()

            ), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($rule->getId(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';
        exit;


        $calendarEntry = new Google_CalendarListEntry();
        $calendarEntry->setId($calendar->getId());
        $calendarEntry->setSelected(true);

        $defaultReminders[0] = new Google_EventReminder();
        $defaultReminders[0]->setMethod('sms');
        $defaultReminders[0]->setMinutes('30');
        $defaultReminders[1] = new Google_EventReminder();
        $defaultReminders[1]->setMethod('email');
        $defaultReminders[1]->setMinutes('60');

        $calendarEntry->setDefaultReminders($defaultReminders);


        $createdEntry = $service->calendarList->insert($calendarEntry);

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($service->calendars->get('5fl2ffgrqd4drmppv24pppnjq4@group.calendar.google.com'), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($service->calendarList->listCalendarList()->getItems(), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        exit;*/
        //====================================================================================
        //====================================================================================
        
        $daterangepicker = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'));
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/moment.min.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerScriptFile($daterangepicker. '/daterangepicker.js', CClientScript::POS_END);
        Yii::app()->getClientScript()->registerCssFile($daterangepicker. '/daterangepicker-bs3.css');

        $model=new Client;
        $user = User::model()->findByPk(Yii::app()->user->id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Client'])) {

			$model->attributes=$_POST['Client'];
			//$model->email = rand().$model->email;//TODO remove
            $model->google_calendar_id = ' ';

            //if ($model->validate() && empty($model->google_calendar_id)) $model->google_calendar_id = $this->_getNewCalendar();

            if ($model->validate() && $model->save()) {

                // create workflow togther with steps from WorkflowStepsByType
				// @see app/models/Workflow::afterSave
				$workflow = new Workflow();
				$workflow->client_id = $model->id;
				$workflow->case_type = $model->case_type;
				$workflow->save();

                $documentTemplate = DocumentListTemplate::model()->findByPk($model->document_list_id);
                if ($documentTemplate) {
                    foreach ($documentTemplate->documentTemplates as $_document) {
                        $doc = new Document();
                        $doc->attributes = $_document->attributes;
                        $doc->workflow_id = $workflow->id;
                        $doc->save();
                    }

                }

                $this->redirect(array('workflow/view','id'=>$workflow->id, 'c'=>$model->id));

			}
		}

		$this->render('create',array(
			'model'=>$model,
            'user'=>$user
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
        Yii::app()->getClientScript()->registerScriptFile(
            Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/moment.min.js' ), CClientScript::POS_END
        );

        Yii::app()->getClientScript()->registerScriptFile(
            Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/daterangepicker.js' ), CClientScript::POS_END
        );

        Yii::app()->getClientScript()->registerCssFile(
            Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.vendor.daterangepicker'). '/daterangepicker-bs3.css' )
        );

        /* @var Client $model*/
		$model=$this->loadModel($id);
        $user = User::model()->findByPk(Yii::app()->user->id);
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if (isset($_POST['Client'])) {
			$model->attributes=$_POST['Client'];
			if ($model->save()) {

				//if for some reasons wokflow data is not present for current client OR checkbox to change case type ticked
				if ( empty($model->case_type) || ($_POST['Client']['change_case_type'] == 1 && !empty($_POST['Client']['case_type'])) ) {
					// create workflow togther with steps from WorkflowStepsByType
					// @see app/models/Workflow::afterSave

					$this->removeClientWorkflow($model);

					$workflow            = new Workflow();
					$workflow->client_id = $model->id;
					$workflow->case_type = $model->case_type;
					if ($workflow->save()) {
						$this->redirect(array('workflow/view','id'=>$workflow->id, 'c'=>$model->id), true);
					}
				}
				//$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
            'user'=>$user
		));
	}

	private function removeClientWorkflow(Client $client){
		$client->current_workflow->delete();
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