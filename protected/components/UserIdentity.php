<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{    
    /**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
    
	private $_id;
    public $user;

	public function authenticate()
	{
		/* @var User $user */
		$this->user = User::model()->find('LOWER(email)=?',array(strtolower($this->username)));
		if($this->user === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
        elseif(empty($this->password) && empty(Yii::app()->user->token) && !$this->user->validatePassword($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
        elseif($this->user->status == User::STATUS_NEW) {
            $this->errorCode = User::ERR_INACTIVE;
        } else {
			$this->_id=$this->user->id;
			$this->username=$this->user->email;
			$this->errorCode=self::ERROR_NONE;
			Yii::app()->user->setState('__isAdmin', $this->user->role == 'superadmin');
		}

		return $this->errorCode==self::ERROR_NONE;
	}

	public function getId()
	{
		return $this->_id;
	}
}