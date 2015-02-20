<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	// status alert
    const ALERT_ERROR = 'error';
    const ALERT_SUCCESS = 'success';
    const ALERT_INFO = 'info';
    const ALERT_WARNING = 'warning';

    // Array lists keys with values
    public static $alerts = array(
        self::ALERT_ERROR,
        self::ALERT_SUCCESS,
        self::ALERT_INFO,
        self::ALERT_WARNING,
    );
    
    /**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
    public $_user;

    public $issues = 0; //indicator
	public $attorney_actions = 0; //indicator

	protected function beforeRender( $view ) {

        $alert = self::getAlert();
        if(!empty($alert))
        {
            foreach($alert as $k=>$v){
                Yii::app()->user->setFlash(key($v), $alert[$k][key($v)]);
            }
        }
        
        Yii::app()->clientScript->scriptMap=array(
            //'bootstrap.js'=>false,
            'bootstrap.min.js'=>false,
        );

        if (Yii::app()->user->checkAccess('admin')) {
            $this->_user = User::model()->findByPk(Yii::app()->user->id);
            if($allIssues = $this->_user->getallIssues(1))
                $this->issues = count($allIssues);
            if($allAction = $this->_user->getallAction(1))
                $this->attorney_actions = count($allAction);
		}
        return parent::beforeRender( $view );
	}

    public function getQRImage($data=null){
		//Yii::app()->log->routes[1]->enabled=false;
		if (empty($data)) throw new CHttpException(500, 'Invalid QR code input data');
		/*echo $this->widget('application.vendor.qrcode.QRCodeGenerator', array(
			'data' => $data,
			'filePath' => realpath(Yii::getPathOfAlias('webroot.qr')),
			'fileUrl' => 'qr',
			'filename' => md5($data) . '.png',
			'subfolderVar' => false,
			'matrixPointSize' => 5,
			'displayImage'=>true, // default to true, if set to false display a URL path
			'errorCorrectionLevel'=>'L', // available parameter is L,M,Q,H
        ),
			true
		);*/
		$url = $this->widget( 'application.vendor.qrcode.QRCodeGenerator', array(
				'data'                 => $data,
				'filePath'             => realpath( Yii::getPathOfAlias( 'webroot.qr' ) ),
				'fileUrl'              => '/qr',
				'filename'             => md5( $data ) . '.png',
				'subfolderVar'         => false,
				'matrixPointSize'      => 5,
				'displayImage'         => false, // default to true, if set to false display a URL path
				'errorCorrectionLevel' => 'L', // available parameter is L,M,Q,H
			),
			true
		);

		$url = Yii::app()->getBaseUrl( true ) . $url;

		return $url;
	}

    public static function validateDate($date, $format = 'm/d/Y')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public static function addAlert($name, $message, $status = 'success')
    {
        if (in_array($status, self::$alerts)) {
            if(!isset(Yii::app()->session['alert']))
            {
                Yii::app()->session['alert'] = array($name => array($status => $message));
            }else{
                $a = Yii::app()->session['alert'];
                $a[$name] = array($status => $message);
                Yii::app()->session['alert'] = $a;
            }
            return true;
        }
        return false;
    }
    
    public static function delAlert($name = null)
    {
        if($name)
        {
            $a = Yii::app()->session['alert'];
            unset($a[$name]);
            Yii::app()->session['alert'] = $a;
        }else
            unset(Yii::app()->session['alert']);
        return true;
    }
    
    public static function getAlert($name = null)
    {
        $alert = null;
        if(!empty(Yii::app()->session['alert']))
        {
            $alert1 = Yii::app()->session['alert'];
            if($name && isset($alert1[$name]))
                $alert = $alert1[$name];
            if($name == null)
                $alert = $alert1;
            self::delAlert($name);
        }
        return $alert;
    }
}