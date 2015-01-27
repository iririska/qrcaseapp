<?php

/**
 * This is the model class for table "Document".
 *
 * The followings are the available columns in table 'Document':
 * @property integer $id
 * @property integer $document_list_id
 * @property string $document_name
 * @property string $document_link
 * @property string $created
 * @property string $updated
 * @property string $workflow_id
 *
 * The followings are the available model relations:
 * @property DocumentList $documentList
 */
class Document extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Document';
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

			array('document_list_id', 'required', 'on'=>'create'),
			array('document_name, document_link', 'required'),
			array('document_list_id', 'numerical', 'integerOnly'=>true),
			array('document_name,document_link_type', 'length', 'max'=>255),
			array('document_link', 'url'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, document_list_id, document_name, document_link, created, updated', 'safe', 'on'=>'search'),
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
			'documentList' => array(self::BELONGS_TO, 'DocumentList', 'document_list_id'),
			'workflow' =>  array(self::BELONGS_TO, 'Workflow', 'workflow_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'document_list_id' => 'Document List',
			'document_name' => 'Document Name',
			'document_link' => 'Document Link',
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
		$criteria->compare('document_list_id',$this->document_list_id);
		$criteria->compare('document_name',$this->document_name,true);
		$criteria->compare('document_link',$this->document_link,true);
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
	 * @return Document the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
