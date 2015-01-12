<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'user-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation' => false,
	) );
	?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary( $model ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 4, 'maxlength' => 128 ) ); ?>

	<?php
	if ($model->isNewRecord) {
		echo $form->passwordFieldControlGroup( $model, 'password', array( 'span' => 4, 'maxlength' => 255 ) );
	}
	?>

	<?php
	echo $form->dropDownListControlGroup( $model, 'role', CHtml::listData( AuthItem::model()->findAll("type='2'"), 'name', 'description'), array( 'span' => 2 ) ); ?>

	<?php echo $form->dropDownListControlGroup( $model, 'status', array(
			'1' => 'Enabled',
			'2' => 'Disabled'
		), array( 'span' => 2 ) ); ?>

	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('user/admin'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) );

		?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->