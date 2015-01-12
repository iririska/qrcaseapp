<?php

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 * @property integer $id
 * @property string $email
 * @property string $created
 * @property string $updated
 * @property string $last_logged
 * @property integer $status
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property string $phone2
 * @property string $address
 * @property string $ssn
 * @property string $dob
 * @property string $gender
 * @property string $driver_license
 * @property integer $change_case_type
 * @property string $google_calendar_id
 * @property integer $case_type
 *
 * The followings are the available model relations:
 * @property Step[] $steps
 * @property Workflow[] $workflows
 * @property Workflow $current_workflow
 * @property ClientNote[] $notes
 */
class Client extends CActiveRecord
{
	public $change_case_type = 0;

	private $statusNames = array(
		0 => 'Disabled',
		1 => 'Active',
	);

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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, case_type, firstname, lastname, phone', 'required'),
			array('status, case_type', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>128),
			array('email', 'unique'),
			array('address', 'length', 'max'=>255),
			array('firstname, lastname, phone, phone2', 'length', 'max'=>45),

			array('ssn', 'length', 'max'=>16),

			array('dob', 'type', 'type' => 'date', 'message' => '{attribute}: is not a date!', 'dateFormat' => 'MM-dd-yyy'),

			array('gender', 'in', 'range'=>array('m','f')),

			array('driver_license', 'length', 'max'=>16),

			array('created, updated, last_logged, google_calendar_id, change_case_type', 'safe'),
			array('id, email, created, updated, last_logged, status, firstname, lastname, phone, phone2, address, case_type', 'safe', 'on'=>'search'),
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
			'steps' => array(self::HAS_MANY, 'Step', 'user_id'),
			'workflows' => array(self::HAS_MANY, 'Workflow', 'client_id'),
			'current_workflow' => array(self::HAS_ONE, 'Workflow', 'client_id'),
			'notes' => array(self::HAS_MANY, 'ClientNote', 'client_id'),
			'client_user' => array(self::HAS_MANY,'ClientUser','client_id'),
			'assigned_users'=>array(self::HAS_MANY, 'User', array('id'=>'user_id'), 'through'=>'client_user'),
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
			'created' => 'Created',
			'updated' => 'Updated',
			'last_logged' => 'Last Logged',
			'status' => 'Status',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'phone' => 'Phone',
			'phone2' => 'Phone2',
			'address' => 'Address',
			'address' => 'Address',
			'address' => 'Address',
			'address' => 'Address',
			'address' => 'Address',
			'case_type' => 'Case Type',
			'change_case_type' => 'Change Case Type',
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
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('last_logged',$this->last_logged,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('case_type',$this->case_type);

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

	/**
	 * String representation of client's status
	 * @return string
	 */
	public function getStatusName(){
		return Yii::t('app', $this->statusNames[$this->status]);
	}

	/**
	 * Class for grid view
	 * @return mixed
	 */
	public function getColor() {
		switch ($this->status) {
			case 0: return 'bg-danger'; break; //Disabled
			default: return ''; break;
		}
	}

	public function afterSave(){
		parent::afterSave();
	}

	public function getFullName(){
		return "$this->firstname $this->lastname";
	}

	public function getNotesProvider(){
		return new CActiveDataProvider( 'ClientNote',
			array(
				'criteria'      => array(
					'condition' => " client_id=:client_id ",
					'params' => array(':client_id'=>$this->id),
					'order'     => 'created DESC',
					//'with'      => array( 'author' ),
				),
				/*'countCriteria' => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					// 'order' and 'with' clauses have no meaning for the count query
				),
				'pagination'    => array(
					'pageSize' => 1,
				),*/
			)
		);
	}

	public function getGoogleCalendarId(){
		return $this->google_calendar_id;
	}
}