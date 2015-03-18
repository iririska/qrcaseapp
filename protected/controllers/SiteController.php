<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: site/page&view=FileName
			/*'page'=>array(
				'class'=>'CViewAction',
			),*/
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
				'actions'=>array('index', 'view', 'create','update'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		if (Yii::app()->user->isGuest) {
			//TODO refactor this later
			$this->redirect(array('site/login'));
			Yii::app()->end();
		}
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact() {
		$model=new ContactForm;
		if(isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];
			if($model->validate()) {
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		if(Yii::app()->user->isGuest) {
            $model=new LoginForm('standartLogin');
            // if it is ajax validation request
            if(isset($_POST['ajax']) && $_POST['ajax']==='login-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if(isset($_POST['LoginForm'])) {
                $model->attributes=$_POST['LoginForm'];
                // validate user input and redirect to the previous page if valid
                if($model->validate() && $model->login())
                    $this->redirect(Yii::app()->user->returnUrl);
            }
            // display the login form
            $this->render('login',array('model'=>$model));
        } else {
            $this->redirect('/');
        }
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
    
    /**
	 * Displays the register page
	 */
    public function actionRegister() {
        if (Yii::app()->user->isGuest)  {
            $model = new User('register');
            $this->performAjaxValidation($model);
            if (isset($_POST['User'])) {
                $model->attributes=$_POST['User'];
                if ($model->save()) {
                    //send activation letter with activation key
                    $message = '<table class="w580" width="580" cellpadding="0" cellspacing="0" border="0">
                    <tbody><tr>
                        <td class="w580" width="580" style="padding-top:20px;">
                            <p align="left" class="article-title" style="font-size:16px; color:#666; line-height:22px;">
                                Congratulations! You are almost ready to start. First, please activate your account.
                            </p>
                            <div align="center" class="article-content" style="padding-top:20px; padding-bottom:20px; font-size:16px; color:#666; line-height:22px;">
                                <a href="'.Yii::app()->createAbsoluteUrl('site/activate', array('email' => $model->email,'hash' => $model->hash)).'" style="text-decoration: none; padding: 10px 40px; color: #fff; font-family: Arial,Helvetica,sans-serif; font-size: 16px; background-color: #2d2f34; border-color: #18191c; font-weight: normal; display:inline-block;">
                                        Activate Account
                                </a>
                            </div>      
                        </td>
                    </tr></tbody></table>';
                    User::sendMail($model->email, 'Activate your account.', $message);

                    //Create an alert to the user, which appears after a redirect
                    Controller::addAlert('register','<strong>Well done!</strong> To the specified e-mail was sent to you with further instructions.');
                    $this->redirect('/site/login');
                } else {
                    throw new CHttpException(403, 'Failed to add to the database.');
                }
            }

            $this->render('register',array(
                'model'=>$model,
            ));
        } else {
            $this->redirect(Yii::app()->user->returnUrl);
        }
    }
    
    public function actionActivate($email, $hash) {
        if (!empty($email) && !empty($hash)) {
            $user = User::model()->findByAttributes(array('email' => $email, 'hash' => $hash));
            if ($user) {
                if ($user->status == 1) {
                    //Create an alert to the user, which appears after a redirect
                    Controller::addAlert('activate','Your account is already active.', 'info');
                    if(!Yii::app()->user->isGuest && $user->id == Yii::app()->user->id)
                        $this->redirect('/');
                    elseif(!Yii::app()->user->isGuest) {
                        Yii::app()->user->logout();
                    }
                } elseif ($user->status == 0) {
                    $user->status = 1;
                    $user->save();
                    //Create an alert to the user, which appears after a redirect
                    Controller::addAlert('activate','<strong>Congratulations</strong>, your account has been activated.  Welcome!');
                }
                $this->redirect('/site/login');
            }
        }
        throw new CHttpException(403, 'Wrong path activate your account.');
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel[] $models  the model to be validated
     * @return boolean false if validation wasn't needed.
     */
    protected function performAjaxValidation($models) {
        if(isset($_POST['ajax']) && ($_POST['ajax']==='register-form')) {
            echo CActiveForm::validate($models);
            Yii::app()->end();
        }
        return false;
    }
}