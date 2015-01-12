<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'password-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		'action' => Yii::app()->createUrl('user/updatepassword', array('id'=>$model->id)),
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'afterValidate'=>
				'js: function(form,data,hasError) {
		        if(!hasError)
		        {
					$.ajax(
					{
					"type":"POST",
					"url":$("#password-form").attr("action"),
					"data":form.serialize(),
					"dataType":"json",
					"beforeSend":function(){ $("#change-password-submit").button("Adding..")},
					"success":function(data)
					    {
					        $("#change-password-submit").after(
					        \'<div class="alert alert-\'+data.status+\' text-center" role="alert" id="change-password-alert" style="margin-top: 5px;">\'+
					        \'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\'+
					        data.message+
					        \'</div>\');
					        setTimeout(function(){
					        	$("#change-password-alert").slideUp(function(){$(this).remove()});
					        }, 3000);
					    }
					});
		        }
		    }'
		),
	) );
	?>

	<?php echo $form->errorSummary( $model ); ?>
	<?php
		echo $form->passwordFieldControlGroup( $model, 'password', array( 'span' => 4, 'maxlength' => 255, 'value'=>'' ) );
	?>

	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('user/admin'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
			'id' => 'change-password-submit',
		) );

		?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->