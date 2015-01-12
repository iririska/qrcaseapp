<?php
/* @var $this ClientController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'client-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation' => false,
	) ); ?>

	<?php echo $form->errorSummary( $model ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 5, 'maxlength' => 128 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'firstname', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'lastname', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'phone', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'phone2', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'address', array( 'span' => 5, 'maxlength' => 255 ) ); ?>

	<?php echo $form->dropDownListControlGroup( $model, 'status', array(
			'1' => 'Active',
			'0' => 'Disabled'
		), array( 'span' => 3 ) );
	?>

	<?php echo $form->dropDownListControlGroup( $model, 'case_type', CHtml::listData( WorkflowType::model()->findAll(), 'id', 'title' ), array( 'span' => 3, 'disabled'=>!$model->isNewRecord ) ); ?>

	<?php if (!$model->isNewRecord) echo $form->checkBoxControlGroup( $model, 'change_case_type', array('id'=>'change_case_type', 'help'=>'Tick this checkbox if you want to change Case Type for this client.<br> Please be aware that changing Case Type will erase previous Case Type data')); ?>

	<?php echo $form->textFieldControlGroup( $model, 'google_calendar_id', array( 'span' => 5, 'maxlength' => 255, 'help'=>'Calendar ID can be found in calendar properties and must have be a string of type <em>r1hnd4kr49dbcp6c3n4l8fasjs@group.calendar.google.com</em>' ) ); ?>

	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('client/admin'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) ); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<?php
Yii::app()->clientScript->registerScript('enlarge-qr',
	<<<SCRIPT
$('#change_case_type').on('change', function(){
	$('select[name*="case_type"').attr('disabled', !$(this).attr('checked'));
});
SCRIPT
	,
	CClientScript::POS_END

);