<?php
/* @var $this StepController */
/* @var $model Step */
/* @var $form TbActiveForm */

CHtml::$requiredCss = 'red';
CHtml::$errorCss = 'error';
CHtml::$errorMessageCss = 'error';
CHtml::$afterRequiredLabel = ' <span class="red">*</span>';

?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'create-step-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
	) );
	?>

	<?php echo $form->errorSummary( $model ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'title', array( 'span' => 8, 'maxlength' => 128 ) ); ?>

	<?php
	echo $form->dropDownListControlGroup( $model, 'priority', $model->getPriorityForList(), array( 'span' => 2 ) );
	?>

	<?php echo $form->textAreaControlGroup( $model, 'description', array( 'span' => 8, 'rows' => 5 ) ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'progress', array( 'span' => 2, 'rows' => 5 ) ); ?>

	<?php

	// Date start

	$datePicker = $this->widget(
		'booster.widgets.TbDatePicker',
		array(
			'model' => $model,
			'attribute' => 'date_start',
			'htmlOptions' => array('class'=>'form-control'),
		),
		true
	);

	echo TbHtml::customActiveControlGroup($datePicker, $model, 'date_start'
		,
		array(
			'labelOptions'=>array(
				'class'=>'col-md-2'
			),
			'controlOptions'=>array(
				'span'=>2,
			),
		)
	);

	?>


	<?php

	// Date end

	$datePicker = $this->widget(
		'booster.widgets.TbDatePicker',
		array(
			'model' => $model,
			'attribute' => 'date_end',
			'htmlOptions' => array('class'=>'form-control'),
		),
		true
	);

	echo TbHtml::customActiveControlGroup($datePicker, $model, 'date_end'
		,
		array(
			'labelOptions'=>array(
				'class'=>'col-md-2'
			),
			'controlOptions'=>array(
				'span'=>2,
			),
		)
	);

	?>

	<?php
	echo $form->dropDownListControlGroup( $model, 'status', $model->getStatusForList(), array( 'span' => 3 ) );
	?>


	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('/'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) );

		?>
	</div>



	<?php $this->endWidget(); ?>

</div><!-- form -->