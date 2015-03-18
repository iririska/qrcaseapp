<?php

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $created
 * @property string $updated
 * @property string $last_logged
 * @property string $hash
 * @property integer $status
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property string $phone2
 * @property string $address
 * @property integer $case_type
 * @property integer $parent_id
 * @property string $refresh_token
 * @property string $session_data
 * @property string $user_data
 *
 * The followings are the available model relations:
 * @property Step[] $steps
 * @property Workflow[] $workflows
 */
class User extends CActiveRecord
{
    // user status
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    
    // Set custom error
    const ERR_INACTIVE = 'INACTIVE';
    
    // user role
    const ADMIN = 'admin';
    const USER = 'user';
    
    const ATTORNEY = 'Attorney';
    const PARALEGAL = 'Paralegal';

    // Array lists keys with values
    public static $userRole = array(
        self::ADMIN,
        self::USER,
    );
    
    public $repeatPassword;

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'User';
	}

	public function behaviors(){
		return array(
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'updated',
			)
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('repeatPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match', 'on' => 'register, updatePassword'),
            array('password', 'required', 'on' => 'createUser'),
			array('email, parent_id, role', 'required'),
			array('password', 'safe', 'on'=>'update'),
            array('password', 'length', 'min'=>4, 'on' => 'register, updatepassword'),
            array('password, repeatPassword', 'required', 'on' => 'register, updatepassword'),
			array('status, parent_id', 'numerical', 'integerOnly'=>true),
			array('role', 'in', 'range' => self::$userRole),
            array('role', 'length', 'max'=>64),
			array('email', 'length', 'max'=>128),
			array('email', 'unique'),
			array('email', 'email'),
			array('email, role', 'safe', 'on'=>'updatepassword'),
			array('password, address', 'length', 'max'=>255),
			//array('role, firstname, lastname, phone, phone2', 'length', 'max'=>45),
			array('created, updated, last_logged', 'safe'),
			array('id, email, role, created, updated, last_logged, status, parent_id', 'safe', 'on'=>'search, searchAccountUsers'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'paralegal' =>  array(self::HAS_MANY, 'User', 'parent_id', 'condition' => 'role=\''.self::USER.'\''),
            'attorney' =>  array(self::HAS_MANY, 'User', 'parent_id', 'condition' => '`attorney`.`role`=\''.self::ADMIN.'\''),
            //'attorney_paralegal' => array(self::HAS_MANY, 'User', array('id' => 'parent_id'), 'through' => 'attorney', 'condition' => '`attorney_paralegal`.`role`=\''.self::USER.'\''),
            
            'clients' => array(self::HAS_MANY, 'Client','creator_id'),
            'client_attorney' => array(self::HAS_MANY, 'Client', array('id' => 'creator_id'), 'through' => 'attorney'),
            //'client_attorney_paralegal' => array(self::HAS_MANY, 'Client', array('id' => 'creator_id'), 'through' => 'attorney_paralegal'),
            
            //'client_user' =>  array(self::HAS_MANY, 'ClientUser', 'user_id'),
			//'assigned_clients'=>array(self::HAS_MANY, 'Client', array('client_id'=>'id'), 'through'=>'client_user'),
			'steps' => array(self::HAS_MANY, 'Step', 'user_id'),
			'workflows' => array(self::HAS_MANY, 'Workflow', 'client_id'),
            'issues' => array(self::HAS_MANY, 'OutstandingIssues','author'), 
            'issues_attorney' => array(self::HAS_MANY, 'OutstandingIssues', array('id'=>'author'), 'through' => 'attorney'),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'email' => 'Email',
			'password' => 'Password',
			'role' => 'Role',
			'created' => 'Created',
			'updated' => 'Updated',
			'last_logged' => 'Last Logged',
			'status' => 'Status',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'phone' => 'Phone',
			'phone2' => 'Phone2',
			'address' => 'Address',
            'parent_id' => 'Parent',
            'refresh_token' => 'Refresh Token',
			'session_data' => 'Session Data',
			'user_data' => 'User Data',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('last_logged',$this->last_logged,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('address',$this->address,true);
        $criteria->compare('parent_id',$this->parent_id,true);
        $criteria->compare('refresh_token',$this->refresh_token,true);
		$criteria->compare('session_data',$this->session_data,true);
		$criteria->compare('user_data',$this->user_data,true);
        
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
    public function searchAccountUsers() {
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('address',$this->address,true);
        $criteria->compare('parent_id',$this->parent_id,true);
        $ids = $this->manageUsersIds;
        if(!empty($ids)) 
            $criteria->addcondition("id IN(".$ids.")");
        else
            $criteria->compare('id',0);
        
        return new CActiveDataProvider( $this,
            array(
                'criteria'      => $criteria,
                'countCriteria' => $criteria,
                'pagination'    => array(
                    'pageSize' => 10,
                ),
            )
        );
	}
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function validatePassword($password)
	{
		return CPasswordHelper::verifyPassword($password,$this->password);
	}

	public function hashPassword($password)
	{
		return CPasswordHelper::hashPassword($password);
	}
    
    protected function beforeValidate() {
        if ($this->isNewRecord) {
            if ($this->scenario == 'register') {
                $this->role = self::ADMIN;
                $this->parent_id = 0;
                $this->status = 0;
            } elseif(Yii::app()->user->isGuest && isset(Yii::app()->user->token)) {
                $this->role = self::ADMIN;
                $this->parent_id = 0;
                $this->status = 1;
            } elseif(!Yii::app()->user->isGuest && $this->scenario == 'createUser'){
                $this->role = self::USER;
                $this->parent_id = Yii::app()->user->id;
                $this->status = 1;
            }
        }
        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord && !empty($this->password))
            $this->password = $this->hashPassword($this->password);

		if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s');
            if($this->scenario == 'register') {
                $this->hash = sha1($this->email.$this->created);
            }
        } 
		return parent::beforeSave();
	}
    
    protected function afterSave()
    {
        if ($this->isNewRecord) {
            $role = new AuthAssignment();
            $role->itemname = $this->role;
            $role->userid   = $this->id;
            if (!$role->save()) 
                return false;
        }
        return parent::afterSave();
    }

    public function getAllIssues($status = 'all') {
        $st_condition = '';
        if($status != 'all')
            $st_condition = ' AND status = '.$status;
        $issues = null;
 		$_clients_ids = implode("', '", array_keys(Client::getMyClients()));
        if(!empty($_clients_ids))
            $issues = OutstandingIssues::model()->findAll('client_id IN(\''.$_clients_ids.'\')'.$st_condition);
        return $issues;
    }
    
    public function getAllAction($status = 'all') {
        $st_condition = '';
        if($status != 'all')
            $st_condition = ' AND status = '.$status;
        $issues = null;
 		$_clients_ids = implode("', '", array_keys(Client::getMyClients()));
        if(!empty($_clients_ids))
            $issues = AttorneyActions::model()->findAll('client_id IN(\''.$_clients_ids.'\')'.$st_condition);
        return $issues;
    }
    
    public function getManageUsersIds() {
        $manage = array();
        $user = User::model()->findByPk(Yii::app()->user->id);
        $manage += CHtml::listData($user->paralegal, 'id', 'parent_id');
        $ids = implode(',', array_keys($manage));
        return $ids;
    }
    
    public function  getParent() {
        if(!$this->parent_id)
            return false;
        $user = User::model()->findByPk($this->parent_id);
        return $user;
    }
    
    public static function sendMail($to, $subject, $message, $from = 'admin@admin.com'){
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From:" . $from;
        mail($to, $subject, $message, $headers);
    }
    
    public function getEmailWithRole() {
        return $this->email.' ('.AuthItem::model()->find("name='$this->role'")->description.')';
    }
}
