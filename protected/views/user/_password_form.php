<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="form">

	<?php 
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'password-form',
        'enableClientValidation' => true,
		'enableAjaxValidation' => true,
        'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
        'htmlOptions' => array(),
        'action' => Yii::app()->createUrl('user/updatepassword', array('id'=>$model->id)),
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
    ));
        
		echo $form->passwordFieldControlGroup( $model, 'password', array( 'span' => 4, 'maxlength' => 255, 'value'=>'' ) );

        echo TbHtml::formActions(array(
            TbHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('color' => TbHtml::BUTTON_COLOR_PRIMARY,'id' => 'change-password-submit',)),
            TbHtml::link('Cancel',
                array('user/admin'),
                array('class' => 'btn btn-default')
            )
        )); 
        
        $this->endWidget(); ?>

</div><!-- form -->