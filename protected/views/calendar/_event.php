<?php
/**
 * @var TbActiveForm $form
 * @var CalendarEvent $event
 */

//init default values
if ($event->isNewRecord && empty($_POST['CalendarEvent'])) {
	$event->start = date('Y-m-d 00:00:01');
	//$event->end = date('Y-m-d 23:59:59');
}
?>
<div class="form">

	<?php $form = $this->beginWidget( 'booster.widgets.TbActiveForm', array(
		'id'                   => 'calendar-event-form',
		//'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		'action' => ($event->isNewRecord) ? Yii::app()->createUrl('calendar/addevent') : Yii::app()->createUrl('calendar/updateevent',array('id'=>$event->id)),
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		//'action'               => array( 'note/create', 's' => $event->step_id),
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'afterValidate'=>
				'js:
			function(form,data,hasError) {
				console.log("ERROR: " + hasError);
		        if(!hasError)
		        {
					$.ajax(
					{
						"type":"POST",
						"url": form.attr("action"),
						"data":form.serialize(),
						"dataType":"json",
						"beforeSend":function(){
							$("#add-calendar-event-submit").button("loading");
						}
					}).done(function ( data, textStatus, jqXHR ) {
						console.log("DONE");
			            $("#myModal").modal("hide");
			            $("#calendar").fullCalendar( "refetchEvents" );

					}).always(function(data, textStatus, jqXHR) {
						$("#add-calendar-event-submit").button("reset");
						console.log("DONE ALWAYS");
					})
		        }
		    }'),
	) ); ?>

	<?php echo $form->errorSummary( $event ); ?>

	<?php
	echo $form->dropDownListGroup( $event, 'client_id', array(
		'label' => 'Client',
		'widgetOptions' => array(
			'data' => CHtml::listData( Client::model()->findAll(), 'id', 'email' )
		)
	)); ?>

	<?php echo $form->textFieldGroup( $event, 'title' ); ?>

	<?php echo $form->dateTimePickerGroup($event, 'start', array( 'groupOptions' => array('style'=>'width: 40%; display: inline-block') ) ); ?>

	<?php echo $form->dateTimePickerGroup($event, 'end', array( 'groupOptions' => array('style'=>'width: 40%;  display: inline-block') ) ); ?>

	<?php echo $form->checkBoxGroup($event, 'allDay', array('type'=>TbActiveForm::TYPE_INLINE) ); ?>

	<?php //echo $form->textAreaGroup( $event, 'summary'); ?>

	<?php echo $form->textAreaGroup( $event, 'description', array(
		'widgetOptions' => array(
			'htmlOptions' => array(
				'rows' => 5
			)
		)
	)); ?>

	<?php echo $form->dropDownListGroup( $event, 'color', array(
		'label' => 'Priority',
		'widgetOptions' => array(
			'data' => array(
				'green'=>'Low',
				'blue'=>'Medium',
				'red'=>'High',
			)
		)
	)); ?>

	<div class="form-actions">
		<?php
		echo TbHtml::link( 'Cancel', array( 'client/admin' ), array(
			'style' => 'margin-right: 5em;',
			'class'=> ((Yii::app()->request->isAjaxRequest)?'close-modal':''),
			'data-dismiss'=>'modal'
		));

		echo TbHtml::submitButton( $event->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
			'id' => 'add-calendar-event-submit',
		));
		?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->