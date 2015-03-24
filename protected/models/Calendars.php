<?php

/**
 * This is the model class for table "calendars".
 *
 * The followings are the available columns in table 'calendars':
 * @property integer $id
 * @property integer $user_id
 * @property integer $client_id
 * @property string $google_calendar_id
 * @property string $created
 */
class Calendars extends CActiveRecord
{
    //name calendar
    public $name;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Calendars';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, client_id, name', 'required'),
			array('user_id, client_id', 'numerical', 'integerOnly'=>true),
            array('client_id', 'clientCalendar'),
			array('google_calendar_id', 'length', 'max'=>225),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, client_id, google_calendar_id, created', 'safe', 'on'=>'search'),
		);
	}
    
    public function clientCalendar($attribute,$params) {
        if(!$this->hasErrors() && $this->$attribute) {
            $calendar = self::model()->findByAttributes(array('client_id' => $this->$attribute));
            if ($calendar)
                $this->addError($attribute,'That client already has a calendar.');
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'Id Owner',
			'client_id' => 'Id Client',
			'google_calendar_id' => 'Google Calendar',
            'name' => 'Calendar Name'
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('google_calendar_id',$this->google_calendar_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Calendars the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    protected function beforeValidate(){
        /*if ($this->isNewRecord){
            $this->user_id = Yii::app()->user->id;
        }*/
        return parent::beforeValidate();
    }
    
    protected function beforeSave(){
        if ($this->isNewRecord){
            $this->created = date('Y-m-d H:i:s');
        }
        return parent::beforeSave();
    }

    /*
     * method created Calendar
     * $gClient = Google_Client() with all params
     * return google_calendar_id or false
     */
    public function createCalendar($gClient){
        $service = new Google_Service_Calendar($gClient);
        
        $calendar = new Google_Service_Calendar_Calendar();
        $calendar->setSummary($this->name);
        $calendar->setTimeZone('America/Los_Angeles');
        $createdCalendar = $service->calendars->insert($calendar);
        
        if($id = $createdCalendar->getId())
            return $id;
        return false;
    }
    
    
    
}
