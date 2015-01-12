<?php
/**
 *
 * To use Google Calendar API:
 * 1) Create Project in Google Developer Console https://console.developers.google.com/project
 * 2) Enter created project, go to `APIs & auth` section
 * 3) Go to `APIs` and enable `Calendar API`
 * 4) Go to `Credentials` and click `Create new Client ID`, then choose `Service account`
 * 5) Move generated downloaded key to @app/config/google_service_acc_key_77680b6167eed83e2d9652f7214a8c881f65e1b0_privatekey.p12
 * 6) Add `EMAIL ADDRESS` from Service Account properties to selected calendar calendar properties
 * 7) Adjust settings accordingly in @app/config/google_api.php
 */

class CalendarController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	public $defaultAction='view';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + deleteevent', // we only allow deletion via POST request
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
			array('allow', // admin permissions
				'actions'=>array('view', 'addevent', 'getevents', 'updateevent', 'deleteevent'),
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
	 */
	public function actionView()
	{

		Yii::app()->clientScript->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'). '/lib/moment.min.js' ), CClientScript::POS_END
		);

		Yii::app()->clientScript->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'). '/fullcalendar.min.js' ), CClientScript::POS_END
		);

		Yii::app()->clientScript->registerScriptFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'). '/gcal.js' ), CClientScript::POS_END
		);

		Yii::app()->clientScript->registerCssFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'). '/fullcalendar.css' ),
			'screen'
		);
		Yii::app()->clientScript->registerCssFile(
			Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'). '/fullcalendar.print.css' ),
			'print'
		);

		$this->render('view', array(
			'event' => new CalendarEvent()
		));
	}

	public function actionDeleteEvent($id) {
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			CalendarEvent::model()->findByPk($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (Yii::app()->request->isAjaxRequest) {
				echo json_encode(array(
					'status'=>'success',
					'message' => 'Event successfully removed'
				));
			} else {
				$this->redirect( array('calendar/view'), true);
			}
		} else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionAddEvent() {

		$model = new CalendarEvent();

		$this->performAjaxValidation( $model );

		if ( isset( $_POST['CalendarEvent'] ) ) {
			$model->attributes = $_POST['CalendarEvent'];
			if (empty($model->end)) $model->end = null;

			$model->google_calendar_id = Client::model()->findByPk($model->client_id)->google_calendar_id;

			//add google calendar event
			if ($model->google_calendar_id) {
				$model->google_calendar_event_id = $this->addGoogleEvent( $model );
			}

			if ( $model->save() ) {
				if (Yii::app()->request->isAjaxRequest) {
					echo json_encode(array(
						'status'=>'success',
						'message'=>'Event successfully added'
					));
					Yii::app()->end();
				} else {
					$this->redirect(array('calendar/view'), true);
				}

			} else {
				if (Yii::app()->request->isAjaxRequest) {
					echo json_encode(array(
						'status'=>'error',
						'message'=>print_r($model->getErrors(),1)
					));
					Yii::app()->end();
				}
			}
		}
		if (Yii::app()->request->isAjaxRequest) {
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
			Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
			$this->renderPartial( '_event', array( 'event' => $model ), false, true );
		} else {
			$this->renderPartial('_event', array('event'=>$model));
		}
	}

	public function actionUpdateEvent($id) {
		$model = CalendarEvent::model()->findByPk($id);

		$this->performAjaxValidation( $model );

		if ( isset( $_POST['CalendarEvent'] ) ) {
			$model->attributes = $_POST['CalendarEvent'];
			if (empty($model->end)) $model->end = null;
			$this->updateGoogleEvent($model);
			if ( $model->save() ) {
				if (Yii::app()->request->isAjaxRequest) {
					echo json_encode(array(
						'status'=>'success',
						'message'=>'Event successfully added'
					));
					Yii::app()->end();
				} else {
					$this->redirect(array('calendar/view'), true);
				}
			} else {
				if (Yii::app()->request->isAjaxRequest) {
					echo json_encode(array(
						'status'=>'error',
						'message'=>print_r($model->getErrors(),1)
					));
					Yii::app()->end();
				}
			}
		}

		if (Yii::app()->request->isAjaxRequest) {
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
			Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
			$this->renderPartial('_event', array('event'=>$model), false, true);
		} else {
			$this->renderPartial('_event', array('event'=>$model));
		}
	}

	/**
	 * @param CalendarEvent $model
	 * @throws CHttpException
	 * @return string|false
	 */
	private function addGoogleEvent($model) {

		$client = new Google_Client();
		$client->setAccessType('online');
		$client->setUseObjects(true);

		if (strpos(__FILE__, 'zergus') !== false) {
			$client->setClientId( '180333236740-lavivuob0e9p3kkqnvumqmlnic34s8p0.apps.googleusercontent.com' );
			$client->setRedirectUri('http://localhost/YII1-READYAPPS/yii-user-yii-auth?r=calendar/addevent');
		} else {
			$client->setClientId( '180333236740-5lupqsprb1glhdrtu3b9fa7h624qn2p2.apps.googleusercontent.com' );
			$client->setRedirectUri('http://ctcommerce.com/CRM/index.php?r=calendar/addevent');
		}

		$client->setAssertionCredentials(
			new Google_AssertionCredentials(
				"180333236740-g7cq40idglikjnumfg65310ujuesk653@developer.gserviceaccount.com",
				array(
					"https://www.googleapis.com/auth/calendar"
				),
				file_get_contents( Yii::getPathOfAlias('application.config') . "/google_service_acc_key_77680b6167eed83e2d9652f7214a8c881f65e1b0_privatekey.p12")
			)
		);

		$service = new Google_CalendarService($client);

		try {
			$event = new Google_Event();
			$event->setSummary( $model->title );
			$event->setLocation( $model->location );
			$event->setDescription( $model->description );

			$start = new Google_EventDateTime();
			$start->setDateTime( date('c', strtotime($model->start)) );
			$start->setTimeZone( 'America/Los_Angeles' );
			$event->setStart( $start );

			if (!empty($model->end)) {
				$end = new Google_EventDateTime();
				$end->setDateTime( date('c', strtotime($model->end)) );
				$end->setTimeZone( 'America/Los_Angeles' );
				$event->setEnd( $end );
			} else {
				$end = new Google_EventDateTime();
				$end->setDateTime( date('c', strtotime($model->start)+24*3600) );
				$end->setTimeZone( 'America/Los_Angeles' );
				$event->setEnd( $end );
			}

			$insertedEvent = $service->events->insert( 'j0tnd4lr49cbcptcc4nlqfas6s@group.calendar.google.com', $event );

			return $insertedEvent->getId();
			Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($insertedEvent->getId(),1) . "\n------------------\n");
		} catch(Google_ServiceException $e) {
			return false;
			Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($e->getMessage(),1) . "\n------------------\n");
			//throw new CHttpException('custom-500', 'Error occured when creating event <div><a class="btn btn-sm btn-primary" href="javascript:history.back(-1)">Back</a></div>' );
		}
	}

	/**
	 * @param CalendarEvent $model
	 *
	 * @return bool
	 */
	private function updateGoogleEvent($model) {
		$client = new Google_Client();
		$client->setAccessType('online');
		$client->setUseObjects(true);

		if (strpos(__FILE__, 'zergus') !== false) {
			$client->setClientId( '180333236740-lavivuob0e9p3kkqnvumqmlnic34s8p0.apps.googleusercontent.com' );
			$client->setRedirectUri('http://localhost/YII1-READYAPPS/yii-user-yii-auth?r=calendar/addevent');
		} else {
			$client->setClientId( '180333236740-5lupqsprb1glhdrtu3b9fa7h624qn2p2.apps.googleusercontent.com' );
			$client->setRedirectUri('http://ctcommerce.com/CRM/index.php?r=calendar/addevent');
		}

		$client->setAssertionCredentials(
			new Google_AssertionCredentials(
				"180333236740-g7cq40idglikjnumfg65310ujuesk653@developer.gserviceaccount.com",
				array(
					"https://www.googleapis.com/auth/calendar"
				),
				file_get_contents( Yii::getPathOfAlias('application.config') . "/google_service_acc_key_77680b6167eed83e2d9652f7214a8c881f65e1b0_privatekey.p12")
			)
		);

		$service = new Google_CalendarService($client);

		try {
			$event = $service->events->get($model->google_calendar_id, $model->google_calendar_event_id);

			$event->setSummary( $model->title );
			$event->setLocation( $model->location );
			$event->setDescription( $model->description );

			$start = new Google_EventDateTime();
			$start->setDateTime( date('c', strtotime($model->start)) );
			$start->setTimeZone( 'America/Los_Angeles' );
			$event->setStart( $start );

			if (!empty($model->end)) {
				$end = new Google_EventDateTime();
				$end->setDateTime( date('c', strtotime($model->end)) );
				$end->setTimeZone( 'America/Los_Angeles' );
				$event->setEnd( $end );
			} else {
				$end = new Google_EventDateTime();
				$end->setDateTime( date('c', strtotime($model->start)+24*3600) );
				$end->setTimeZone( 'America/Los_Angeles' );
				$event->setEnd( $end );
			}

			$updatedEvent = $service->events->update('primary', $event->getId(), $event);

// Print the updated date.
			echo $updatedEvent->getUpdated();

			Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($updatedEvent->getId(),1) . "\n------------------\n");
		} catch(Google_ServiceException $e) {
			return false;
			Yii::log("GOOGLE CALENDAR ERROR\n------------\n" . print_r($e->getMessage(),1) . "\n------------------\n");
			//throw new CHttpException('custom-500', 'Error occured when creating event <div><a class="btn btn-sm btn-primary" href="javascript:history.back(-1)">Back</a></div>' );
		}
	}



	public function actionGetEvents($start=null, $end=null, $timezone=null){

		$range_start = Event::parseDateTime($start);
		$range_end = Event::parseDateTime($end);

		if (!empty($timezone)) {
			$timezone = new DateTimeZone($timezone);
		}

		$events = (array)CalendarEvent::model()->findAll(
			"(`start` >= :start AND `end` <= :end)
			OR (`eventDate` BETWEEN :start AND :end )
			OR (`start` >= :start AND `end` IS NULL)
			"
			, array(
			':start' => (empty($start)) ? date('Y-m-d') : $range_start->format('Y-m-d H:i:s'),
			':end' => (empty($end)) ? date('Y-m-d') : $range_end->format('Y-m-d H:i:s'),
		));

		$output_arrays = array();
		foreach ($events as $data) {

			// Convert the input array into a useful Event object
			$event = new Event($data->attributes, $timezone);

			// If the event is in-bounds, add it to the output
			if ($event->isWithinDayRange($range_start, $range_end)) {
				$output_arrays[] = $event->toArray();
			}
		}

// Send JSON to the client.
		echo json_encode($output_arrays);
	}

	/**
	 * Performs the AJAX validation.
	 * @param Note $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='calendar-event-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


}