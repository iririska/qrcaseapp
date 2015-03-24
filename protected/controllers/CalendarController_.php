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
                'actions'=>array('view', 'getevents', 'createcalendar'),
                'roles'=>array('admin'),
            ),

            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Displays a calendar for selected client.
     * @param integer $c the ID of the Client, whos calendar has to be displayed
     * @throws CHttpException
     */
    public function actionGetEvents() {

        $c = Yii::app()->user->getState('__calendar_client', null);

        if (!empty($c)) {

            /* @var $qr_client Client */
            $qr_client = Client::model()->findByPk((int)$c);

            if (!$qr_client) throw new CHttpException(404, 'No such client');

            if ($qr_client->google_calendar_id) {

                $calendar = new GoogleCalendarProxy(
                    Yii::app()->params['googleApiConfig']['service_acc_email'],
                    Yii::app()->params['googleApiConfig']['service_acc_client_id'],
                    Yii::app()->params['googleApiConfig']['service_acc_key'],
                    $qr_client->google_calendar_id
                );

                try {
                    $calendar->connect();
                } catch(Exception $e) {
                    echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($e, 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';
                }


            }
        }
    }

    public function actionView($c=null){

        //if we have $_GET params 'c'  (client_id)
        //we save it to user state and redirect
        //so later we can use in when loading events for this selected client
        if (!empty($c) && (int)$c > 0) {
            Yii::app()->user->setState('__calendar_client', (int)$c);
            $this->redirect(Yii::app()->createUrl("calendar/view"), true);
        }

        //pull that client to dislplay for customer whic client is selected
        //$client = Client::model()->findByPk( (int)Yii::app()->user->getState('__calendar_client', null) );

        Yii::app()->clientScript->scriptMap=array(
            //'bootstrap.js'=>false,
            'bootstrap.min.js'=>false,
        );

        $dhtmlSchedulerPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('dhtmlscheduler'));

        Yii::app()->clientScript
            ->registerScriptFile($dhtmlSchedulerPath . '/dhtmlxscheduler.js', CClientScript::POS_END)
            ->registerScriptFile($dhtmlSchedulerPath . '/ext/dhtmlxscheduler_year_view.js', CClientScript::POS_END)
            //->registerScriptFile($dhtmlSchedulerPath . '/ext/dhtmlxscheduler_agenda_view.js', CClientScript::POS_END)
            ->registerScriptFile($dhtmlSchedulerPath . '/ext/dhtmlxscheduler_week_agenda.js', CClientScript::POS_END)
            ->registerScriptFile($dhtmlSchedulerPath . '/ext/dhtmlxscheduler_active_links.js', CClientScript::POS_END)
            ->registerCssFile(
                $dhtmlSchedulerPath . '/dhtmlxscheduler_flat.css'
            );

        $this->render(
            'view',
            array(
                'client_id' => (int)Yii::app()->user->getState('__calendar_client'),
            )
        );
    }

    public function actionCreateCalendar(){
        $client = GoogleCalendarProxy::getClient(
            Yii::app()->params['googleApiConfig']['service_acc_email'],
            Yii::app()->params['googleApiConfig']['service_acc_client_id'],
            Yii::app()->params['googleApiConfig']['service_acc_key']
        );

        $service = new Google_CalendarService($client);

        /* @var Google_CalendarListEntry $_calendar */
        /*foreach ($service->calendarList->listCalendarList()->getItems() as $_calendar) {
            //if ( strpos($_calendar->getSummary(), 'CRM') === false ) $service->calendarList->delete($_calendar->getId());
        }*/

        $calendar = new Google_Calendar();
        $calendar->setSummary('Test Calendar 2');
        $calendar->setTimeZone('America/Los_Angeles');


        $calendar = $service->calendars->insert($calendar);

        echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($calendar, 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        //return $calendar->getId();
    }
}