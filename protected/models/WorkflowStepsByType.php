<?php

/**
 * This is the model class for table "WorkflowStepsByType".
 *
 * The followings are the available columns in table 'WorkflowStepsByType':
 * @property integer $id
 * @property string $title
 * @property string $created
 * @property integer $priority
 * @property integer $case_type
 * @property string $modified
 *
 * The followings are the available model relations:
 * @property WorkflowType $caseType
 */
class WorkflowStepsByType extends CActiveRecord
{

	/**
	 * @see also /models/Step.php
	 */
	public $prioritySetup = array(
		array(
			'name' => 'None',
		),
		array(
			'name' => 'Low',
		),
		array(
			'name' => 'Medium',
		),
		array(
			'name' => 'High',
		),
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'WorkflowStepsByType';
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
			array('title, priority, case_type', 'required'),
			array('case_type', 'numerical', 'integerOnly'=>true),
			array('priority', 'in', 'range'=>$this->getPriorityEnum()),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, created, priority, case_type, modified', 'safe', 'on'=>'search'),
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
			'caseType' => array(self::BELONGS_TO, 'WorkflowType', 'case_type'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Step Title',
			'created' => 'Created',
			'priority' => 'Priority',
			'case_type' => 'Case Type',
			'modified' => 'Modified',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('case_type',$this->case_type);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkflowStepsByType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getPriorityEnum(){
		$_priorities = array();
		foreach ($this->prioritySetup as $i=>$_priority ) {
			$_priorities[$i] = mb_convert_case($_priority['name'], MB_CASE_LOWER);
		}
		return $_priorities;
	}

}
