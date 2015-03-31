<?php
/**
 * To use Google Calendar API:
 * 1) Select a project, or create a new one in Google Developer Console https://console.developers.google.com/project
 * 2) In the sidebar on the left, expand APIs & auth. Next, click APIs. In the list of APIs, make sure the status is ON for the Google Calendar API
 * 3) In the sidebar on the left, select Credentials.
 * 4) Go to `Credentials` and click `Create new Client ID`, then choose `Web application`
 * ???
 */

class CalendarController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column2';

    public $defaultAction = 'view';
    
    //save new Google_Client()
    public $_gclient;
    //user, which token is used
    public $_use_user;
        
    public $alert_1 = '<b>This client doesn\'t have a calendar yet.</b><br />';
    public $alert_2 = 'To create it, you have to <a href="/auth2/googleoauth2">login Google Acount</a>.';
    public $alert_3 = '<button class="btn btn-default" id="create_cal">Create</button>';
    public $alert_4 = '<b>You are independent paralegal.</b> ';
    public $alert_5 = 'You can create it. Please, before create, <a href="/auth2/googleoauth2">Activate Api Calendar</a> from Google Acount.';
    public $alert_6 = 'Do you want <span id="create_cal">create it</span>?';
    public $alert_7 = 'You are not independent paralegal. You cann\'t create calendar for this client.';
    
    /**
     * @return array action filters
     */
    public function filters() {
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
    public function accessRules() {
        return array(
            array('allow', // admin permissions
                'actions'=>array('view', 'getevents', 'createcalendar'),
                'roles'=>array('admin'),
            ),

            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    
    protected function beforeAction($action) {
        if(parent::beforeAction($action)){
            //if user authorizate
            if($this->_user){                
                //if select client calendar
                if(!empty(Yii::app()->user->__calendar_client)) {
                    $client = Client::model()->findByPk( (int) Yii::app()->user->__calendar_client);                            
                    //Check for exists google calendar for this client
                    if((empty($client->google_calendar_id) || $client->google_calendar_id == ' ')) {
                        if($alert = $this->beforeCreateCalendar($client->id)) { //???
                            if(!Yii::app()->request->isAjaxRequest) {
                                //show alert
                                Controller::addAlert('calendar_client', $alert['alert'], 'info');
                                $this->_use_user = $alert['user'];
                            }
                        }
                    } /*else {
                        //open calendar
                        $cal = Calendars::model()->find('google_calendar_id = \''.$client->google_calendar_id.'\'');
                        $user = User::model()->findByPk($cal->user_id);
                        if($this->setGoogleClient($user->session_data, $user->refresh_token)) {
                            $service = new Google_Service_Calendar($this->_gclient);
                            $events = $service->events->listEvents($client->google_calendar_id);
                            foreach ($events->getItems() as $event) {
                                var_dump($event);
                            }
                        }
                    }*/
                }
            }
        }
        return true;
    }  

    public function actionView($client = null){
        $client_id = '';
        $this->fullCalendarSet(); //connect fullcalendar js and css
        
        //if we have $_GET params 'client'  (client_id)
        //we save it to user state and redirect
        //so later we can use in when loading events for this selected client
        if (!empty($client) && (int)$client > 0) {
            Yii::app()->user->setState('__calendar_client', (int)$client); 
            Controller::delAlert('calendar_client');
            $this->redirect(Yii::app()->createUrl("calendar/view"), true);
        } else {
            if(!empty(Yii::app()->user->__calendar_client)) {
                //pull that client to dislplay for customer which client is selected
                $client_id = (int)Yii::app()->user->__calendar_client;
            }
        }

        Yii::app()->clientScript->scriptMap=array('bootstrap.min.js' => false);        
        $this->render('test', array(
            'client_id' => $client_id,
            'user_id' => $this->_use_user->id
        ));
    }
    
    public function actionCreateCalendar(){
        $calendar = new Calendars();
        $this->performAjaxValidation($calendar);
        $error = 0;
        $clientModel = Client::model()->findByPk($calendar->client_id);
        if (Yii::app()->request->isAjaxRequest && !empty($_POST['Calendars'])) {
            $calendar = new Calendars();
            $calendar->attributes = $_POST['Calendars'];

            if($calendar->validate()){
                $user = User::model()->findByPk($calendar->user_id);

                if($this->setGoogleClient($user->session_data, $user->refresh_token))
                    $calendar->google_calendar_id = $calendar->createCalendar($this->_gclient);
                else 
                    $error = 1;
                    
            }
            if($calendar->google_calendar_id) {
                if($calendar->save()){
                    
                    $clientModel = Client::model()->findByPk($calendar->client_id);
                    $clientModel->google_calendar_id = $calendar->google_calendar_id;
                    if($clientModel->save(false)) {
                                        
                        $event = array();
                        $workflow = $clientModel->workflow;
                        $steps = $workflow->steps;
                        foreach($steps as $step){
                            $calevent = null;
                            if($calevent = $step->createNewEvent($calendar->google_calendar_id, $calendar->client_id)){
                                if($calevent->createEvent($this->_gclient, $calendar->google_calendar_id)){
                                }  else {
                                    $error = 2;
                                }
                            }
                        }
                    } else 
                        $error = 3;
                }
            }
        }
        if(!$error) {
            Controller::addAlert('calendar_client', 'Good! Calendar created.');
            echo json_encode(array(
                'status'=>'success',
                'message'=>'Good! Calendar created.'
            ));
            
        } else {
            Controller::addAlert('calendar_client', 'Calendar is not created. Try again later.', 'error');
            echo json_encode(array(
                'status'=>'error',
                'message'=>'Something gone wrong '.$error
            ));
        }
        Yii::app()->end();
    }
    
    /**
     * @param null $start      period start
     * @param null $end        period end
     * @param null $timezone   timezone
     * @param null $c          client id
     */
	public function actionGetEvents($start=null, $end=null, $timezone=null, $c=null){
        $output_arrays = array();
        $events = array();
        if(Yii::app()->user->__calendar_client){
            $range_start = Event::parseDateTime($start);
            $range_end = Event::parseDateTime($end);

            if (!empty($timezone)) {
                $timezone = new DateTimeZone($timezone);
            }

            $client = Client::model()->findByPk(Yii::app()->user->__calendar_client);
            if($client->google_calendar_id){
                $events = (array)CalendarEvent::model()->findAll(
                    "((`start` >= :start AND `end` <= :end)
                    OR (`eventDate` BETWEEN :start AND :end )
                    OR (`start` >= :start AND `end` IS NULL)) 
                    AND `google_calendar_id` = '".$client->google_calendar_id."'"
                    , array(
                    ':start' => (empty($start)) ? date('Y-m-d') : $range_start->format('Y-m-d H:i:s'),
                    ':end' => (empty($end)) ? date('Y-m-d') : $range_end->format('Y-m-d H:i:s'),
                ));
            }
            
            foreach ($events as $data) {

                // Convert the input array into a useful Event object
                $event = new Event($data->attributes, $timezone);

                // If the event is in-bounds, add it to the output
                if ($event->isWithinDayRange($range_start, $range_end)) {
                    $output_arrays[] = $event->toArray();
                }
            }
        }

        // Send JSON to the client.
		echo json_encode($output_arrays);
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
	 *
	 * @return bool
	 */
	private function updateGoogleEvent($model) {
        if($this->setGoogleClient($user->session_data, $user->refresh_token)){
            $service = new Google_Service_Calendar($this->_gclient);
        }

		try {
			$event = $service->events->get($model->google_calendar_id, $model->google_calendar_event_id);

			$event->setSummary( $model->title );
			$event->setLocation( $model->location );
			$event->setDescription( $model->description );

			$start = new Google_Service_Calendar_EventDateTime();
			$start->setDateTime( date('c', strtotime($model->start)) );
			$start->setTimeZone( 'America/Los_Angeles' );
			$event->setStart( $start );
            
            $end = new Google_Service_Calendar_EventDateTime();            
			if (!empty($model->end))
				$end->setDateTime( date('c', strtotime($model->end)) );
			else
				$end->setDateTime( date('c', strtotime($model->start)+24*3600) );

            $end->setTimeZone( 'America/Los_Angeles' );
            $event->setEnd( $end );
            
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

    

    
    public function fullCalendarSet(){
        $fullcalendar_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('fullcalendar'));
        Yii::app()->clientScript->registerScriptFile($fullcalendar_path. '/lib/moment.min.js' , CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($fullcalendar_path. '/fullcalendar.min.js' , CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($fullcalendar_path. '/gcal.js' , CClientScript::POS_END);
        Yii::app()->clientScript->registerCssFile($fullcalendar_path. '/fullcalendar.css', 'screen');
        Yii::app()->clientScript->registerCssFile($fullcalendar_path. '/fullcalendar.print.css', 'print');
    }
    
     /*
     * method refresh Access Token and insert new Access Token in user table
     * return boolean
    */
    public function refreshTokenG($refresh_token) {
        $user = User::model()->findByAttributes(array('refresh_token' => $refresh_token));
        if($user) {
            try {
                $this->_gclient->refreshToken($refresh_token);
            } catch (Google_Auth_Exception $e) {
                Yii::app()->user->setState('approval_prompt', 'force');
                Controller::addAlert('google_token','Something wrong! Please, try <a href="/auth2/googleoauth2">activate Google Account</a> again.','error');
                    return false;
            }
            $access_token = $this->_gclient->getAccessToken();
            $user->session_data = $access_token;
            if($user->save())
                return true;
            
        } else
            Controller::addAlert('google_token','User with this Refresh Token not exist','error');
        return false;
    }

    /*
     * method setting Google connect, setting Access Token and check time expired token
     * return boolean
    */
    protected function setGoogleClient($token, $refresh_token) {
        $this->_gclient = $this->GoogleClientConf(true);
        $this->_gclient->setAccessToken($token);
        if($this->_gclient->isAccessTokenExpired()) {
            if($this->refreshTokenG($refresh_token))
                return true;
            else
                return false;
        }
        return true;
    }
    
    public function beforeCreateCalendar($client_id) {
        if(Yii::app()->user->isGuest)
            return false;

        $alert = $this->alert_1; 
        $userT = $this->_user;
        if(Yii::app()->user->isAdmin) { //Check user role
            //if user - admin, he can creating calendar
            if(empty($this->_user->refresh_token)) {//This User not registered API Calendar yet 
                Yii::app()->user->setState('approval_prompt', 'force');
                $alert .= $this->alert_2;
            } else
                $alert .= $this->alert_3;
        } else {
            //if user - user (paralegal), finding his attorney
            if(!$this->_user->parent_id) { //attorney not exists
                $alert = $this->alert_4;
                if(empty($this->_user->refresh_token)) //This User not registered API Calendar yet 
                    $alert .= $this->alert_5;
                else
                    $alert .= $this->alert_6;
            } else { //user have attorney
                $attorney = User::model()->findByPk($this->_user->parent_id);
                //check - exist refresh Token
                if($attorney && empty($attorney->refresh_token)) //if Attorney does not create calendar for this client yet
                    $alert .= $this->alert_7;
                else {
                    $alert .= $this->alert_6;
                    $userT = $attorney;
                }
            }
        }
        return array('alert' => $alert, 'user' => $userT);
    }
    
    /**
	 * Performs the AJAX validation.
	 * @param Workflow $model the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax']==='newcal-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
    
}