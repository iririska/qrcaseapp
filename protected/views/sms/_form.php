<?php
/**
 * @var $this SmsController
 * @var $form TbActiveForm
 * @var $model Sms
 */

?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'sms-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'afterValidate'=>
				'js:
			function(form,data,hasError) {
		        if(!hasError){
					$.ajax({
						"type":"POST",
						"url": form.attr("action"),
						"data":form.serialize(),
						"dataType":"json",
						"beforeSend":function(){
							$("#sms-alert").remove();
							$("#add-sms-submit").button("loading");
						}
					}).done(function ( data, textStatus, jqXHR ) {
						var alertstr = \'<div class="alert alert-\'+data.status+\' text-center" role="alert" id="sms-alert" style="margin-top: 5px;">\'+
				        \'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\'+
				        data.message+
				        \'</div>\';

						if(data.status == \'error\'){
							$("#add-sms-submit").before(alertstr);
							/*setTimeout(function(){
								$("#sms-alert").slideUp(function(){$(this).remove()});
							}, 5000);*/
						}else{
							$("#sms-form").before(alertstr);
							$("#sms-form").remove();
					        setTimeout(function(){
					            $("#sms-alert").slideUp(function(){$(this).remove()});
					            $(".bs-example-modal-lg").modal("hide");
					        }, 4000);
				        }
					}).always(function(data, textStatus, jqXHR) {
						$("#add-sms-submit").button("reset");
					})
		        }
		    }'),
	) ); ?>


	<?php echo $form->errorSummary( $model ); ?>

	<?php echo $form->textFieldControlGroup( $model, 'subject', array( 'span' => 8, 'maxlength' => 255 ) ); ?>
	<?php echo $form->textAreaControlGroup( $model, 'text', array( 'span' => 8, 'rows'=>3 ) ); ?>
	<?php echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 4, 'maxlength' => 128 ) ); ?>
	<?php echo $form->textFieldControlGroup( $model, 'phone', array( 
					'help'=>'Example: +15555555555',
					'span' => 4, 'maxlength' => 45 ) ); ?>
	<?php echo $form->dropDownListControlGroup( $model, 'carrier_code', $model->CarriersList, array( 'span' => 4 )); ?>
	
	<div class="modal-footer">
		<?php		
		echo TbHtml::submitButton( 'Send', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_DEFAULT,
			'id' => 'add-sms-submit'
		) ); 
		echo TbHtml::Button( 'Cancel', array( 
				'size'  => TbHtml::BUTTON_SIZE_DEFAULT,
				'class'=> (Yii::app()->request->isAjaxRequest)?'close-modal':''
			) 
		);
		?>
	</div>

	<?php $this->endWidget(); ?>

</div>

