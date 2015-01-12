<?php
/**
 */

class AuthItemForm extends CFormModel {
	public $user;

	public $assignment;

	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('AuthModule.main', 'System name'),
			'description' => Yii::t('AuthModule.main', 'Description'),
			'bizrule' => Yii::t('AuthModule.main', 'Business rule'),
			'data' => Yii::t('AuthModule.main', 'Data'),
			'type' => Yii::t('AuthModule.main', 'Type'),
		);
	}

	/**
	 * Returns the validation rules for attributes.
	 * @return array validation rules.
	 */
	public function rules()
	{
		return array(
			array('users, type', 'required'),
			array('name', 'required', 'on' => 'create'),
			array('name', 'length', 'max' => 64),
		);
	}

}