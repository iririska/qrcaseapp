<?php
/**
 * @var CalendarController $this
 * @var CalendarEvent $event
 */
?>
<div id='calendar'></div>

<?php
$get_events_url = Yii::app()->createUrl('calendar/getevents');
$update_event_url = Yii::app()->createUrl('calendar/updateevent');
$add_event_url = Yii::app()->createUrl('calendar/addevent');
$delete_event_url = Yii::app()->createUrl('calendar/deleteevent');
$script = <<<SCRIPT

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
          type: 'POST', // 'POST' | 'GET'
          dataType: 'json', //xml, html, script, json, jsonp, text
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

)); ?>


<!--<iframe src="https://www.google.com/calendar/embed?title=Calendar&amp;showTitle=0&amp;showCalendars=0&amp;mode=WEEK&amp;height=600&amp;wkst=1&amp;bgcolor=%23ffffff&amp;src=j0tnd4lr49cbcptcc4nlqfas6s%40group.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FNew_York" style=" border-width:0 " width="100%" height="750" frameborder="0" scrolling="no"></iframe>-->
