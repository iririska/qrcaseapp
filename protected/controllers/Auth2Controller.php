<?php

class Auth2Controller extends Controller
{
    //The method keeps refresh token in the session if it has been transferred
    //it is can be called Yii::app()->user->refreshToken
    protected function setRefreshToken($token) {
        $access_token = json_decode($token);
        if(isset($access_token->refresh_token)) {
            Yii::app()->user->setState('refreshToken', $access_token->refresh_token);
            return true;
        }
        return false;
    }

    public function actionGoogleoauth2(){
		if(Yii::app()->user->isGuest) {
            $client = $this->GoogleClientConf(true);
            if (isset(Yii::app()->user->token) && Yii::app()->user->token) {
                
                $this->setRefreshToken(Yii::app()->user->token);                
                $client->setAccessToken(Yii::app()->user->token);
                $service = new Google_Service_Oauth2($client);
                $userinfo = $service->userinfo->get();
                
                $user = User::model()->find('LOWER(email)=?',array(strtolower($userinfo->email)));
                //if new user
                if($user === null){
                    $user = new User();
                    $user->email = $userinfo->email;
                    $user->password = '';
                    $user->firstname = $userinfo->givenName;
                    $user->lastname = $userinfo->familyName;
                }
                $user->session_data = Yii::app()->user->token;
                
                //save user info data if if was empty
                if(empty($user->user_data))
                    $user->user_data = json_encode($userinfo);
                
                //refresh token save in session from BD or in BD from session
                if(isset(Yii::app()->user->refreshToken))
                    $user->refresh_token = Yii::app()->user->refreshToken;
                elseif(!empty($user->refresh_token))
                    Yii::app()->user->setState('refreshToken', $user->refresh_token);
                
                if($user->save()){
                    $model=new LoginForm;
                    $model->username = $user->email;
                    $model->password = '';
                    if($model->validate() && $model->login())
                        $this->redirect('/');
                    else
                        echo 'error';
                }

            } else {
                $redirect_uri = $client->createAuthUrl();
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            }
        } else {
            $this->redirect('/');
        }
    }
    
    
    public function actionOauth2callback() {
        $client = $this->GoogleClientConf(true);  
        
        if(isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $access_token = $client->getAccessToken();
            Yii::app()->user->setState('token', $access_token); //can call Yii::app()->user->token;
            $this->redirect('/auth2/googleoauth2');
            
        } elseif(isset($_GET['error'])) {
            if($_GET['error'] == 'access_denied')
                Controller::addAlert('access_denied','<strong>Access denied.</strong> The resource owner or authorization server denied the request.', 'error');
            else
                Controller::addAlert($_GET['error'],'<strong>Error state: '.$_GET['error'].'.</strong>', 'error');
            
        } else {
            Controller::addAlert('oauth2_error','<strong>Uknown Error.</strong>', 'error');
        }
        $this->redirect('/site/login');
    }
    

    
}
