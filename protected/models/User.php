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
 * @property integer $status
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property string $phone2
 * @property string $address
 * @property integer $case_type
 *
 * The followings are the available model relations:
 * @property Step[] $steps
 * @property Workflow[] $workflows
 */
class User extends CActiveRecord
{
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
			array('email,password,role', 'required'),
			array('password', 'safe', 'on'=>'update'),
			array('status', 'numerical', 'integerOnly'=>true),

			array('password', 'length', 'min'=>4),

			array('email', 'length', 'max'=>128),
			array('email', 'unique'),
			array('email', 'email'),

			array('email, role', 'safe', 'on'=>'updatepassword'),

			array('password, address', 'length', 'max'=>255),
			//array('role, firstname, lastname, phone, phone2', 'length', 'max'=>45),
			array('created, updated, last_logged', 'safe'),

			array('id, email, role, created, updated, last_logged, status', 'safe', 'on'=>'search'),
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
			'client_user' =>  array(self::HAS_MANY, 'ClientUser', 'user_id'),
			'assigned_clients'=>array(self::HAS_MANY, 'Client', array('client_id'=>'id'), 'through'=>'client_user'),
			'steps' => array(self::HAS_MANY, 'Step', 'user_id'),
			'workflows' => array(self::HAS_MANY, 'Workflow', 'client_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
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
	public function search()
	{
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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

	public function beforeSave(){
		$this->password = CPasswordHelper::hashPassword($this->password);
		if ($this->isNewRecord) $this->created = date('Y-m-d H:i:s');
		return true;
	}

	public function getAssignedClientsIDs(){
		$_clients_ids = array();

		//if not admin then select assigned Client only
		if (!Yii::app()->user->getIsAdmin()) {
			foreach ( $this->assigned_clients as $_client ) {
				$_clients_ids[] = $_client->id;
			}
		} else {
		//otherwise select all Client
			foreach ( Client::model()->findAll() as $_client ) {
				$_clients_ids[] = $_client->id;
			}
		}

		return $_clients_ids;
	}
}
