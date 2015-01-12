<?php

/**
 * This is the model class for table "AttorneyActions".
 *
 * The followings are the available columns in table 'AttorneyActions':
 * @property integer $id
 * @property integer $client_id
 * @property integer $workflow_id
 * @property integer $step_id
 * @property integer $author
 * @property integer $status
 * @property string $created
 * @property string $updated
 */
class AttorneyActions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AttorneyActions';
	}


	public function behaviors(){
		return array(
			'ZPrepareDateBehavior' => array(
				'class' => 'application.extensions.behaviors.ZPrepareDateBehavior',
				'toSave' => array(
					'date_start' => 'datetime',
					'date_end' => 'datetime',

				),
				'toOutput' => array(
					'date_start' => 'm/d/Y',
					'date_end' => 'm/d/Y',
					'created' => 'm/d/Y H:i:s',
					'updated' => 'm/d/Y H:i:s',
				),
			),

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
			array('client_id, workflow_id, author', 'required'),
			array('client_id, workflow_id, step_id, author, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, client_id, workflow_id, step_id, author, status, created, updated', 'safe', 'on'=>'search'),
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
			'creator' => array(self::BELONGS_TO, 'User', 'author'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'workflow' => array(self::BELONGS_TO, 'Workflow', 'workflow_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'workflow_id' => 'Workflow',
			'step_id' => 'Step',
			'author' => 'Author',
			'status' => '1 - active, 0 - inactive',
			'created' => 'Created',
			'updated' => 'Updated',
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
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('workflow_id',$this->workflow_id);
		$criteria->compare('step_id',$this->step_id);
		$criteria->compare('author',$this->author);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AttorneyActions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
