<?php

class QRController extends Controller
{
	public $defaultAction = 'generate';
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('generator', 'image'),
				'users'=>array('@'),
			),

			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionGenerator($data=null){
		if (isset($_POST['data'])) {
			if ( ! empty( $_POST['data'] ) ) {

				$url = $this->widget( 'application.vendor.qrcode.QRCodeGenerator', array(
						'data'                 => $_POST['data'],
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

				echo CJSON::encode(
					array(
						'status' => 'success',
						'image'  => CHtml::image( $url . '?stmt=' . time().rand(9999,99999), CHtml::encode( $_POST['data'] ) ),
						'url'    => $url,
						'debug' => $_POST,
					)
				);
				Yii::app()->end();
			} else {
				echo CJSON::encode(
					array(
						'status'  => 'error',
						'message' => 'Data cannot be empty',
					)
				);
				Yii::app()->end();
			}
		}

		$this->render('generator',array());

	}



}//https://github.com/yiisoft/yii.git