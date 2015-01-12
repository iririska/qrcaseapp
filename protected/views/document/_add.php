<?php
/**
 * @var $this DocumentController
 * @var $form TbActiveForm
 * @var $model Document
 */
?>


<div class="form">

	<?php
	$form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'document-add-form',
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
							$("#add-document-submit").button("loading");
						}
					}).done(function ( data, textStatus, jqXHR ) {
                        $("#add-document-submit").after(
				        \'<div class="alert alert-\'+data.status+\' text-center" role="alert" id="step-note-alert" style="margin-top: 5px;">\'+
				        \'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\'+
				        data.message+
				        \'</div>\');
				        setTimeout(function(){
				            $("#step-note-alert").slideUp(function(){$(this).remove()});
				            $(".bs-example-modal-lg").modal("hide");
				        }, 1000);
				        try {
				        	$.fn.yiiListView.update("documents-list");

				        } catch(e) {}
					}).always(function(data, textStatus, jqXHR) {
						$("#add-document-submit").button("reset");
					})
		        }
		    }'),
	) ); ?>

	<?php
	echo $form->textFieldControlGroup( $model, 'document_name', array( 'span' => 4, 'maxlength' => 255 ) );
	?>

	<?php
	echo $form->textFieldControlGroup( $model, 'document_link', array( 'span' => 4, 'maxlength' => 255 ) );
	?>

	<?php
	echo $form->dropDownListControlGroup( $model, 'document_link_type', array(
		'dropbox'=>'Dropbox',
		'gdrive'=>'Google Drive',
		'doctini'=>'Doctini',
	), array( 'span' => 4 ) );
	?>

	<div class="form-actions">
		<?php
		echo TbHtml::link('Cancel', array('/'), array('style'=>'margin-right: 5em;', 'class'=> (Yii::app()->request->isAjaxRequest)?'close-modal':''));

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
			'id' => 'add-document-submit'
		) );

		?>
	</div>

	<?php $this->endWidget(); ?>
</div>