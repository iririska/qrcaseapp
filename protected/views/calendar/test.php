<?php
/**
 * @var CalendarController $this
 * @var CalendarEvent $event
 */
?>
<style type="text/css">
    #create_cal{cursor: pointer;}
    .client-dropdown{margin-bottom: 15px;}
</style>

<div class="row client-dropdown">
    <div class="col-md-12">
<?php
$client_string =
    //CJavaScript::encode(
        Yii::app()->createUrl("calendar/view", array('client'=>'@'))
    //)
    ;
$js = <<<JS
    function(data) {
       document.location = "{$client_string}".replace(/%40/, data.val);
    }
JS;
        $this->widget('booster.widgets.TbSelect2', array(
            'asDropDownList' => true,
            'val' => $client_id,
            'name' => 'client',
            'options' => array(
                //'tags' => array(/*'clever', 'is', 'better', 'clevertech'*/),
                'placeholder' => 'Type the name or email of client to select',
                'width' => '40%',
                'allowClear' => true,
                //'tokenSeparators' => array(',', ' ')
            ),
            'events' => array(
                'select2-selecting' => 'js:'.$js
            ),
            'data' => Client::getMyClients('fullnamewithemail'),
            'htmlOptions' => array(
                'multiple' => false,
            )
        ));
?>
    </div>
</div>

<div id='calendar'></div>

<?php
$add_calendar_url = Yii::app()->createAbsoluteUrl('/calendar/createcalendar');
$get_events_url = Yii::app()->createUrl('calendar/getevents');
$update_event_url = Yii::app()->createUrl('calendar/updateevent');
$add_event_url = Yii::app()->createUrl('calendar/addevent');
$delete_event_url = Yii::app()->createUrl('calendar/deleteevent');

$script = <<<SCRIPT
        
$(document).ready(function(){
    $('#create_cal').click(function(){
        $('#myModal2').modal('show');
    });
    $('#create_cal_btn').click(function(){
        $('#myModal2 form').submit();
    });
});        

$.fullCalendar._removeEvent = function(event) {
	$.post('$delete_event_url&id='+event.id, function(){
		$("#calendar").fullCalendar( "refetchEvents" );
	});
	return false;
}

$.fullCalendar._eventChange = function(event){
	var _start = event.start.format(),
		_end = (event.end) ? event.end.format() : null;
    /*if (!confirm("Do the change?")) {
        revertFunc();
    } else*/
    {

        $.ajax({
          url: '$update_event_url&id=' + event.id,
          data: { 'CalendarEvent[start]':_start, 'CalendarEvent[end]':_end },
          type: 'POST',
          dataType: 'json', 
          beforeSend: function ( xhr ) {
            //xhr.overrideMimeType("text/plain; charset=x-user-defined");
          },
          cache: false

        }).done(function ( data, textStatus, jqXHR ) {

        }).fail(function(jqXHR, textStatus, errorThrown) {

        }).always(function(jqXHR, textStatus, errorThrown) {

        });

    }
}

$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		eventLimit: true, // allow "more" link when too many events
		events: {
			url: '$get_events_url',
			error: function() {
				//$('#script-warning').show();
			}
		},
		eventClick: function(event) {
			console.log('eventClick');
			var self = $(window.event.target);

			if (self.hasClass('remove-event')) {
				$(self).popover('destroy');
				$.fullCalendar._removeEvent(event);
			} else {
				$('#myModal .modal-header h4').text('Update event');
				$('#myModal .modal-body').load('$update_event_url' + '&id=' + event.id);
				$('#myModal').modal('show');
			}
		},

		dayClick: function(date, jsEvent, view) {
			console.log('dayClick');
			$('#myModal .modal-body').load('$add_event_url', function(){
				$('#myModal :text[name*="start"]').val(date.format('YYYY-MM-DD HH:mm'));
			});
			$('#myModal .modal-header h4').text('Add event');
	        $('#myModal').modal('show');
	    },

	    eventResize: function( event, delta, revertFunc, jsEvent, ui, view ) {
	    	$.fullCalendar._eventChange(event);
	    },

	    eventDrop: function(event, delta, revertFunc) {
			$.fullCalendar._eventChange(event);
	    },

	    eventRender: function(event, element) {
	    	var _title = '';
	    	element.after('<a class="remove-event">&times;</a>')
	        element.attr({'data-toggle':"popover", 'title':event.start.format('MMMM D, YYYY hh:mmA'), 'data-content': '<strong>'+event.title+'</strong><br>' + event.description.substring(0,50)+'...'});
	    },

	    eventAfterAllRender: function( view ){
	    	$('[data-toggle="popover"]').popover({
	    		container: 'body',
	    		trigger: 'hover',
	    		html: true
	    	})
	    },

		loading: function(bool) {
			$('#loading').toggle(bool);
		}
	});


SCRIPT;
Yii::app()->clientScript->registerScript('fullcalendar-init', $script,  CClientScript::POS_READY );

?>
<?php $this->widget('bootstrap.widgets.TbModal', array(
	'id'=>'myModal',
	'header' =>  ($event->isNewRecord)?'Add Event':'Edit Event',
	'content' => '',
	'footer' => false,
)); 

//modal window add calendar to client
$this->renderPartial('_form_add_cal', array('add_calendar_url' => $add_calendar_url, 'user_id' => $user_id,'client' => isset($client_id) ? Client::model()->findByPk($client_id) : null)); ?>