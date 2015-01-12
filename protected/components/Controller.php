<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
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

	public $issues = 0; //indicator
	public $attorney_actions = 0; //indicator

	protected function beforeRender( $view ) {
		if (Yii::app()->user->checkAccess('admin')) {
			$this->issues = Yii::app()->db->createCommand( "SELECT count(*) FROM OutstandingIssues oi WHERE oi.status = '1'" )->queryScalar();
			$this->attorney_actions = Yii::app()->db->createCommand( "SELECT count(*) FROM AttorneyActions aa WHERE aa.status = '1'" )->queryScalar();
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
}