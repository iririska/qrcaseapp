<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="wide form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
		'action' => Yii::app()->createUrl( $this->route ),
		'method' => 'get',
	) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'id', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 5, 'maxlength' => 128 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'role', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'status', array( 'span' => 5 ) ); ?>

	<div class="form-actions">
		<?php echo TbHtml::submitButton( 'Search', array( 'color' => TbHtml::BUTTON_COLOR_PRIMARY, ) ); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- search-form -->