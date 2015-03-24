<?php
$header = 'Create Calendar to client: '.$client->fullnamewithemail;
$footer = TbHtml::formActions(array(
    TbHtml::Button('Create', array(
        'color' => TbHtml::BUTTON_COLOR_PRIMARY,
        'size' => TbHtml::BUTTON_SIZE_DEFAULT,
        'buttonType' => 'ajaxSubmit',
        'id' => 'create_cal_btn'
        //'data-dismiss' => 'modal'
    )),
    TbHtml::Button( 'Cancel', array( 
        'size' => TbHtml::BUTTON_SIZE_DEFAULT,
        'class' => 'close-modal',
        'data-dismiss' => 'modal'
    ))
));

$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id' => 'myModal2',
    'header' => $header,
    'footer' => $footer,
    'htmlOptions' => array(
         'style' => 'display: none;',
    ),
 ));

        


$_afterValidate = <<<AFTERVALIDATE
			function(form,data,hasError) {
		        if(!hasError) {
					$.ajax( {
						"type":"POST",
						"url": form.attr("action"),
						"data":form.serialize(),
						"dataType":"json",
						"beforeSend":function(){
							$("#step-cal-alert").remove();
							$("#create_cal_btn").button("loading");
						}
					}).done(function ( data, textStatus, jqXHR ) {
                        $("#create_cal_btn").before(
                            '<div class="alert alert-'+data.status+' text-center" role="alert" id="step-cal-alert" style="margin-top: 5px;">'+
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
                            data.message+
                            '</div>'
                        ).hide();
                        setTimeout(function(){
                            $("#step-cal-alert").slideUp(function(){
                                $(this).remove()}
                            );
                            $("#myModal2").modal("hide");
                            window.location.href = "http://qrcaseapp.com/calendar/view"; 
                        }, 2000);
				        
					}).always(function(data, textStatus, jqXHR) {
						$("#create_cal_btn").button("reset");
					})
		        }
		    }
AFTERVALIDATE;
 

if(empty($_POST['Calendars'])) {
    $calendar = new Calendars();
}
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'newcal-form',
        'action' => $add_calendar_url,
        'enableAjaxValidation'=>true,
        //'enableClientValidation'=>true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
            'afterValidate' => 'js: ' . $_afterValidate,
        ),
        'layout' => TbHtml::FORM_LAYOUT_VERTICAL,
        'htmlOptions' => array(),
    ));
    
    echo $form->textFieldControlGroup($calendar, 'name', array( 'span' => 8 , 'value' => 'QRCase '.$client->fullnamewithemail));
    echo $form->hiddenField($calendar, 'client_id', array( 'type' => 'hidden', 'value' => $client->id));
    echo $form->hiddenField($calendar, 'user_id', array( 'type' => 'hidden', 'value' => $user_id));
    
    $this->endWidget();
$this->endWidget();
?>
