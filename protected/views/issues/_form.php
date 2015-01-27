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

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="form-group">
        <label class="col-sm-2 control-label required" for=<?php echo $model->getAttributeLabel('client_id');?>"><?php echo $model->getAttributeLabel('client_id');?><span class="required">*</span></label>
        <div class="col-md-9">
            <?php
            $this->widget(
                'booster.widgets.TbSelect2',
                array(
                    'asDropDownList' => true,
                    'model' => $model,
                    'attribute' => 'client_id',
                    'form' => $form,
                    'options' => array(
                        //'tags' => array(/*'clever', 'is', 'better', 'clevertech'*/),
                        'placeholder' => 'Type the name or email of client to select',
                        'width' => '100%',
                        'allowClear' => true,
                        //'tokenSeparators' => array(',', ' ')
                    ),
                    'data' => Client::getMyClients(),
                    'htmlOptions' => array(
                        'multiple' => false,
                    )
                )
            );
            ?>
        </div>
    </div>

    <?php
    echo $form->textAreaControlGroup( $model, 'text', array( 'span' => 9 ) );
    ?>

	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('issues/admin'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) );

		?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->