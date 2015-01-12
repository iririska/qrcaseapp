<?php
/* @var $this NoteController */
/* @var $model Note */
/* @var $form TbActiveForm */
?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'step-note-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		//'action'               => array( 'note/create', 's' => $model->step_id),
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'afterValidate'=>
				'js:
			function(form,data,hasError) {
		        if(!hasError)
		        {
					$.ajax(
					{
						"type":"POST",
						"url": form.attr("action"),
						"data":form.serialize(),
						"dataType":"json",
						"beforeSend":function(){
							$("#step-note-alert").remove();
							$("#add-step-note-submit").button("loading");
						}
					}).done(function ( data, textStatus, jqXHR ) {
                        $("#add-step-note-submit").after(
				        \'<div class="alert alert-\'+data.status+\' text-center" role="alert" id="step-note-alert" style="margin-top: 5px;">\'+
				        \'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\'+
				        data.message+
				        \'</div>\');
				        setTimeout(function(){
				            $("#step-note-alert").slideUp(function(){$(this).remove()});
				            $(".bs-example-modal-lg").modal("hide");
				        }, 1000);
				        try {
				        	$.fn.yiiListView.update("notes-'.$model->step_id.'");

				        } catch(e) {}
					}).always(function(data, textStatus, jqXHR) {
						$("#add-step-note-submit").button("reset");
					})
		        }
		    }'),
	) ); ?>

	<?php echo $form->errorSummary( $model ); ?>

	<?php echo $form->textAreaControlGroup( $model, 'content', array( 'span' => 10, 'rows'=>10 ) ); ?>

	<div class="form-actions">
		<?php
		//echo TbHtml::hiddenField('step_id');
		echo TbHtml::link( 'Cancel', array( 'client/admin' ), array( 'style' => 'margin-right: 5em;', 'class'=> ((Yii::app()->request->isAjaxRequest)?'close-modal':'') ) );

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
			'id' => 'add-step-note-submit'
		) ); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->