<?php

/**
 * This is the model class for table "sms".
 *
 * The followings are the available columns in table 'sms':
 * @property integer $id
 * @property string $email
 * @property string $phone
 * @property string $carrier_code
 * @property string $subject
 * @property string $text
 * @property string $date_send
 */
class Sms extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Sms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, phone, carrier_code, subject, text, date_send', 'required'),
			array('email', 'length', 'max'=>128),
			array('phone, carrier_code', 'length', 'max'=>45),
			array('subject, text', 'length', 'max'=>255),
			array('email', 'email'),
			array('carrier_code', 'checkCarrierCode'),
			array('phone', 'match', 'pattern' => '/^(?:\+?[0-9]{0,2})?((?:\s{0,1}\({0,1})[0-9]{3}(?:(\){0,1}\s{0,1})|\.{0,1})[0-9]{3}(?:\s{0,1}|\.{0,1}|\-{0,1})[0-9]{4})$/'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, email, phone, carrier_code, subject, text, date_send', 'safe', 'on'=>'search'),
		);
	}

	public function checkCarrierCode($attribute) {
       	if(!in_array($this->getAttribute($attribute), $this->CarriersArray)){
       		$this->addError($attribute, "Carriers is incorrect.");
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
			'email' => 'Email',
			'phone' => 'Phone',
			'carrier_code' => 'Carrier Code',
			'subject' => 'Subject',
			'text' => 'Text',
			'date_send' => 'Date',
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
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('carrier_code',$this->carrier_code,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('date_send',$this->date_send,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Sms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeValidate(){
		if ($this->isNewRecord) 
			$this->date_send = date('Y-m-d H:i:s');
		return true;
	}

	public function getCarriersArray(){
		return array_keys(etsGateway::$carriers);
	}

	public function getSmsCountries(){
		$countriesKey = array();
		foreach(etsGateway::$countries as $k => $v){
			$countriesKey[$k] = $v['name'];
		}
		return $countriesKey;
		//return Array ( [au] => Australia [br] => Brazil [ca] => Canada [fr] => France [uk] => United Kingdom [us] => United States )
	}

	public function getCarriersList(){
		$carriersList = array();
		foreach ($this->SmsCountries as $key => $value) {
			$carriersList[$value] = array();
			foreach(etsGateway::$carriers as $k => $v){
				if(etsGateway::$carriers[$k]['country'] == $key){
					$carriersList[$value][$k]=etsGateway::$carriers[$k]['name'];
				}
			}

		}
		return $carriersList;
	}	
}
