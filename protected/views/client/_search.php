<?php
/* @var $this ClientController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="wide form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'action' => Yii::app()->createUrl( $this->route ),
		'method' => 'get',
	) ); ?>

	<?php //echo $form->textFieldControlGroup( $model, 'id', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 5, 'maxlength' => 128 ) ); ?>

	<?php //echo $form->textFieldControlGroup( $model, 'role', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'created', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'updated', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'last_logged', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'status', array( 'span' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'firstname', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'lastname', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'phone', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'phone2', array( 'span' => 5, 'maxlength' => 45 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'address', array( 'span' => 5, 'maxlength' => 255 ) ); ?>

	<?php //echo $form->textFieldControlGroup( $model, 'case_type', array( 'span' => 5 ) ); ?>

	<div class="form-actions">
		<?php echo TbHtml::submitButton( 'Search', array( 'color' => TbHtml::BUTTON_COLOR_PRIMARY, ) ); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- search-form -->