<?php
/**
 * Timeline plugin used: http://almende.github.io/chap-links-library/js/timeline/doc/#Example
 *
 * @var $this WorkflowController
 * @var $model Workflow
 * @var $note ClientNote
 * @var $note_form TbActiveForm
 */

$this->breadcrumbs = array(
	'Workflows' => array( 'index' ),
	$model->id,
);

?>

	<div class="row">
		<div class="col-md-8">
			<h1><?php echo $model->client->fullname; ?></h1>

			<!--			Progress Bar -->
			<?php
			$overall_progress = $model->getOverallProgress();
			if ( $overall_progress < 100 ) {
				$overall_progress_class = 'progress-bar-warning active';
			} else {
				$overall_progress_class = 'progress-bar-success';
			}
			?>
			<div class="progress progress-total">
				<div class="progress-bar <?php echo $overall_progress_class; ?>  progress-bar-striped "
				     role="progressbar" aria-valuenow="<?php echo $overall_progress; ?>" aria-valuemin="0"
				     aria-valuemax="100"
				     style="width: <?php echo (int) $overall_progress; ?>%;"><?php echo $overall_progress; ?>%
				</div>
			</div>
			<!--			Progress Bar END -->

            <div class="row">
                <div class="col-md-5 text-left btn-group action-buttons panel-group">
                    <a class="btn btn-success phone-button" title="Phone" data-activity="phone" data-placeholderText="Phonecall to client">phone</a>
                    <a class="btn btn-success voicemail-button" title="voicemail" data-activity="voice"  data-placeholderText="Voicemail to client">voice</a>
                    <a class="btn btn-success email-button" title="email" data-activity="email" data-placeholderText="Email to client">email</a>
                </div>

                <div class="col-md-7 pull-right btn-group action-buttons">
                    <a class="btn btn-danger btn-primary">Dropbox</a>
                    <a class="btn btn-danger btn-success">Google Drive</a>
                    <a class="btn btn-danger btn-danger">Doctini</a>
                </div>
                <div class="clearfix visible-xs-block"></div>
                <div class="col-md-12">
                	<?php 
						echo TbHtml::linkButton('Send Text', 
							array(
								'color' => TbHtml::BUTTON_COLOR_INFO,
								'size'  => TbHtml::BUTTON_SIZE_SM,
								'class' => 'js_send_sms',
								'url' => array('sms/create')
							)
						);
                	?>
                </div>
            </div>
		</div>

		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-body">
					<?php $this->renderPartial('/_partials/_client_info', array('data'=>$model->client));
					$dates = $model->getDates();

					echo sprintf("<strong>Start date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_start'])));
					echo sprintf("<strong>End date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_end'])));
					?>

				</div>
			</div>
		</div>
	</div>

	<fieldset>
		<legend>Client Notes</legend>
		<div class="panel-group js-expandable" id="client-notes-accordion">
		<div class="panel panel-primary">
			<div class="panel-heading"  data-toggle="collapse" data-parent="#client-notes-accordion" href="#collapse-add-client-note">Add Client Note <small>[click to expand/collapse]</small></div>
			<div class="panel-body panel-collapse collapse" id="collapse-add-client-note">
				<?php $note_form = $this->beginWidget( '\TbActiveForm', array(
					'id'                   => 'client-note-form',
					//'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
					'enableClientValidation' => true,
					'enableAjaxValidation' => true,
					'action'               => array( 'note/create', 's' => $model->client->id, 'clientnote'=>1 ),
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
								"url":$("#client-note-form").attr("action"),
								"data":form.serialize(),
								"dataType":"json",
								"beforeSend":function(){
									$("#client-note-alert").remove();
									$("#add-client-note-submit").button("loading");
								}
							}).done(function ( data, textStatus, jqXHR ) {
		                        $("#add-client-note-submit").after(
						        \'<div class="alert alert-\'+data.status+\' text-center" role="alert" id="client-note-alert" style="margin-top: 5px;">\'+
						        \'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\'+
						        data.message+
						        \'</div>\');
						        $("#client-note-form textarea").val("");
						        setTimeout(function(){
						            $("#client-note-alert").slideUp(function(){$(this).remove()});
						        }, 3000);

						        $.fn.yiiListView.update("yw0");

						        $("#clients-notes-count").text( parseInt($("#clients-notes-count").text())+1 );
							}).always(function(data, textStatus, jqXHR) {
								$("#add-client-note-submit").button("reset");
							})
				        }
				    }'),
					) ); ?>
					<?php
					echo $note_form->hiddenField( $note, 'activity_type', array('value'=>''));
					echo $note_form->textAreaControlGroup( $note, 'content', array(
						'rows'        => 3,
						'placeholder' => Yii::t( 'app', 'Enter your notes here' ),
						'label'       => false,
					) );
					?>
					<div class="form-actions text-right">
						<?php
						//echo CHtml::hiddenField('ajax', 'client-note-form');
						echo TbHtml::submitButton( 'Add Note', array(
							'color' => TbHtml::BUTTON_COLOR_SUCCESS,
							'size'  => TbHtml::BUTTON_SIZE_DEFAULT,
							'id' => 'add-client-note-submit',
							'data-loading-text' => "Loading...",
						) ); ?>
					</div>
					<?php $this->endWidget(); ?>
				</div>
			</div>
			<!--		ADD CLIENT NOTE EOF // -->

			<!--		CLIENT NOTES LIST  -->
			<div class="panel panel-default">
			<div class="panel-heading" data-toggle="collapse" data-parent="#client-notes-accordion" href="#collapse-client-notes">Client
				Notes (<span id="clients-notes-count"><?php echo count($model->client->notes)?></span>)  <small>[click to expand/collapse]</small>
			</div>
			<div class="panel-body panel-collapse collapse" id="collapse-client-notes">
				<div class="list-group">
					<?php $this->widget( '\TbListView', array(
						'dataProvider' => $model->client->getNotesProvider(),
						'itemView'     => '//note/_viewitem',
						'viewData' => array('type'=>'clientnote'),
						'emptyText' => 'No client notes added yet',
					) );
					?>
					</div>
				</div>
			</div>
		</div>
		<!--		CLIENT NOTES LIST EOF // -->
	</fieldset>

<fieldset>
	<legend>Workflow <?php
		echo TbHtml::linkButton(
			'Add Step',
			array(
				'url' => array( 'step/create', 'wid' => $model->id ),
				'icon' => TbHtml::ICON_PLUS,
				'color' => TbHtml::BUTTON_COLOR_SUCCESS,
				'size' => TbHtml::BUTTON_SIZE_XS,
				'class' => 'pull-right',
			)
		);
		?></legend>


	<?php /*<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="js/timeline.js"></script>
	<link rel="stylesheet" type="text/css" href="css/timeline.css">


	<script type="text/javascript">
		var timeline;

		google.load("visualization", "1");

		// Set callback to run when API is loaded
		google.setOnLoadCallback(drawVisualization);

		// Called when the Visualization API is loaded.
		function drawVisualization() {
			// Create and populate a data table.
			var data = new google.visualization.DataTable();
			data.addColumn('datetime', 'start');
			data.addColumn('datetime', 'end');
			data.addColumn('string', 'content');
			data.addColumn('string', 'group');
			//data.addColumn('string', 'type');

			data.addRows([
				<?php
				/* @var Step $step */
				foreach ( $model->steps as $i => $step ) {
					$_nr       = $i + 1;
					$_title    = CHtml::encode( $step->title );
					$_progress = 'progress-bar-' . $step->getStatusProgress(); //progress bar color
					$_percent  = $step->progress; // progress %
					$_active   = $step->progress == 100 ? '' : 'active';
					$_anchor = "#step-$i-data";
					$_start = date(Yii::app()->params['shortDateFormat'], strtotime($step->date_start));
					$_end = date(Yii::app()->params['shortDateFormat'], strtotime($step->date_end));
					$_step_profress_identity = 'progress-total-'.$step->id;
					$content = <<<content
						<a href="$_anchor">
							Step $_nr / $_start - $_end
							<div class="progress $_step_profress_identity">
								<div class="progress-bar $_progress  progress-bar-striped $_active" role="progressbar" aria-valuenow="$_percent" aria-valuemin="0" aria-valuemax="100" style="width: $_percent%;">
									$_percent% $_title
								</div>
							</div>
						</a>
content;

					echo $content;


					/*echo sprintf(
							"\n[new Date(%s, %s, %s), new Date(%s, %s, %s), %s, '%s']",

							date('Y', strtotime($step->date_start)),
							date('m', strtotime($step->date_start))-1,
							date('d', strtotime($step->date_start)),

							date('Y', strtotime($step->date_end)),
							date('m', strtotime($step->date_end))-1,
							date('d', strtotime($step->date_end)),

							CJavaScript::encode($content),

							"Step #{$_nr}" //group

						) ;
					if ($i < count($model->steps)-1) {echo ",";}
					echo '   // ' . $step->id . ': ' . date('Y-m-d', strtotime($step->date_start)). ' ' . date('Y-m-d', strtotime($step->date_end));
					echo "\n";*/
				}

				$_all_statuses = $step->progressSetup;

	/*
				?>
			]);

			// specify options
			var options = {
				"width": "100%",
				"height": "auto",
				"style": "box",
				"editable": false,
				"showCurrentTime": false
			};

			// Instantiate our timeline object.
			timeline = new links.Timeline(document.getElementById('timesheet'), options);
			links.events.addListener(timeline, 'ready', readyHandler);
			links.events.addListener(timeline, 'select', selectHandler);

			function readyHandler() {
				$('#timeline-loader').fadeOut(500);
			}

			function selectHandler() {
				var sel = timeline.getSelection();
				if (sel.length) {
					if (sel[0].row != undefined) {
						var row = sel[0].row;
						document.location.hash = 'step-' + row + '-data';
						console.log( "event " + row + " selected" );
					}
				}

			}

			// Draw our timeline with the created data and options
			timeline.draw(data);
		}
</script>

	<div id="timeline-container">
		<div id="timesheet" style="width: 100%; margin-bottom: 20px;"></div>
		<div id="timeline-loader"><span class="helper"></span><?php echo CHtml::image('/images/loader.gif');?></div>
	</div>
 */ ?>

	<p class="help-block text-center">
	<?php
	if (!empty($_all_statuses)) {
		foreach ( $_all_statuses as $_status_color ) {
			echo "<span class=\"progress-bar-{$_status_color['progress']}\" style=\"border: 1px solid #000; width: 10px; height: 10px; display: inline-block;\"></span> - {$_status_color['name']} &nbsp;&nbsp;&nbsp;";
		}

	}
	?>
	</p>

<!--		STEPS -->

	<?php

	//pull statuses for dropdown
	$_statuses = Step::model()->progressSetup;
	$_priorities = Step::getPriorityEnum();

	foreach ( $model->steps as $i => $step ) {
	/* @var Step $step */
		$_progress = 'progress-bar-' . $step->getStatusProgress(); //progress bar color
		$_percent  = $step->progress; // progress %
		$_active   = $step->progress == 100 ? '' : 'active';
		$_anchor = "#step-$i-data";
		$_step_profress_identity = 'progress-total-'.$step->id;
		$_step_progress = <<<content
					<div class="progress x-thin $_step_profress_identity">
						<div class="progress-bar $_progress  progress-bar-striped $_active" role="progressbar" aria-valuenow="$_percent" aria-valuemin="0" aria-valuemax="100" style="width: $_percent%;">
							$_percent%
						</div>
					</div>
content;

		//generate status dropdown data
		$_statuses_dropdown_items = array();
		foreach ( $_statuses as $k=>$_status_data ) {
			$_statuses_dropdown_items[] = array(
				'label' =>  $_status_data['name'],
				'url' => Yii::app()->createUrl('step/setstatus', array('status'=>$k, 'step'=>$step->id)),
				'linkOptions' => array(
					'class'=>'step-status-ajax ' . $_status_data['color'] ,
				)
			);
		}

		?>
	<div class="panel panel-default step-panel" id="<?php echo sprintf("step-%s-data", $i);?>">
		<div class="panel-heading">
			<h3 class="panel-title"><?php
				echo sprintf( 'Step #%s: %s', $i + 1, $step->title );
				echo $_step_progress;
				echo '<span class="help-block"> Priority: '.mb_convert_case($step->priority, MB_CASE_TITLE).'</span>';
				?></h3>
			<?php
			echo TbHtml::buttonGroup(
				array(
					array(
						'label'      => '<b class="caret"></b>',
						'url'        => array(
							'step/setdates',
							'step' => $step->id,
						),
						'class'      => 'step-dates-ajax',
						'color'      => TbHtml::BUTTON_COLOR_DANGER,
						'size'       => TbHtml::BUTTON_SIZE_SM,
						'icon'       => TbHtml::ICON_CALENDAR,
						'data-start' => date( 'm/d/y H:i:s', strtotime($step->date_start) ),
						'data-end'   => date( 'm/d/y H:i:s', strtotime($step->date_end) ),
					),
					array(
						'label'=>'Action',
						'items'=> array(
							array(
								'label' =>
									'<div style="width: 150px; display: inline-block; position: relative; top: -10px; margin-right: 10px;">' .
									sprintf('Progress: <input type="text" id="progress-%s" style="border:0; color:#f6931f; font-weight:bold; background: transparent; width: 50px; " value="%s" />', $step->id, $step->progress ) .
									$this->widget(
										'zii.widgets.jui.CJuiSliderInput',
										array(
											'name'        => 'slider_basic_' . $step->id,
											'value'       => $step->progress,// default selection
											'event'       => 'change',
											'options'     => array(
												'min'   => 0, //minimum value for slider input
												'max'   => 100, // maximum value for slider input
												// on slider change event
												'slide' => 'js:function(event,ui){$("#progress-'.$step->id.'").val(ui.value);}',
											),
											// slider css options
											'htmlOptions' => array(
												//'style' => 'width:200px;'
											),
										),
										true
									)
									.'</div>
									<div style="width: 20px; display: inline-block;">'.
									TbHtml::button('', array(
										'color' => TbHtml::BUTTON_COLOR_SUCCESS,
										'size'  => TbHtml::BUTTON_SIZE_SM,
										'icon' => TbHtml::ICON_OK,
										'class' => 'step-progress-ajax-submit',
										'data-loading-text' => '...'
									))
									.'</div>'
								,


								'url' => Yii::app()->createUrl('step/setprogress', array('step'=>$step->id)),
								'linkOptions' => array(
									'class'=>'step-progress-ajax',
									'onclick'=>'return false;',
								)
							),
							'---',
							array(
								'label' =>
									'<div style="width: 150px; display: inline-block; position: relative; top: -10px; margin-right: 10px;">Priority:' .

									TbHtml::dropDownList('priority', $step->getPriorityIndex($step->priority), $_priorities, array( 'id'=>'priority-'.$step->id, 'style'=>'width: 100px; margin-bottom: 5px;' ) ) .
									$this->widget(
										'zii.widgets.jui.CJuiSliderInput',
										array(
											'name'        => 'slider_basic_priority' . $step->id,
											'value'       => $step->getPriorityIndex($step->priority),// default selection
											'event'       => 'change',
											'options'     => array(
												'min'   => 0, //minimum value for slider input
												'max'   => 'js:parseInt($("#priority-'.$step->id.' option").length)-1', // maximum value for slider input
												'range' =>  "min",
												// on slider change event
												'slide' => 'js:function(event,ui){
													//console.log($("#priority-'.$step->id.'").selectedIndex);
													$("#priority-'.$step->id.'").val(ui.value);
													console.log(ui.value-1);
												}',
											),
											// slider css options
											'htmlOptions' => array(
												//'style' => 'width:200px;'
											),
										),
										true
									)
									.'</div>
									<div style="width: 20px; display: inline-block;">'.
									TbHtml::button('', array(
										'color' => TbHtml::BUTTON_COLOR_SUCCESS,
										'size'  => TbHtml::BUTTON_SIZE_SM,
										'icon' => TbHtml::ICON_OK,
										'class' => 'step-priority-ajax-submit',
										'data-loading-text' => '...'
									))
									.'</div>'
								,


								'url' => Yii::app()->createUrl('step/setpriority', array('step'=>$step->id)),
								'linkOptions' => array(
									'class'=>'step-priority-ajax',
									'onclick'=>'return false;',
								)
							),
							'---',

							array(
								'label' => 'Modify step',
								'url' => Yii::app()->createUrl('step/update', array('id'=>$step->id)),
								'icon' => TbHtml::ICON_EDIT,
							),

							'---',

							array(
								'label' =>
									TbHtml::beginForm(
										Yii::app()->createUrl('step/delete', array('step'=>$step->id))
										,
										'post',
										array(
											'onsubmit'=>"return confirm('Are you sure you want to delete this step?')"
										)
									) .
									TbHtml::hiddenField('returnUrl', Yii::app()->createUrl('workflow/view', array('id'=>$model->id, 'c'=>$model->client_id ))) .
							        TbHtml::  submitButton('Remove this step', array(
								        'icon' => TbHtml::ICON_REMOVE_CIRCLE,
								        'color' => TbHtml::BUTTON_COLOR_LINK,
								        'class' => 'text-left',
								        'style' => 'color:#333333; padding:0',
							        )) .
							        TbHtml::endForm()
								,//'Remove this step',
								//'url' => Yii::app()->createUrl('step/delete', array('step'=>$step->id, 'w'=>$model->id, 'c'=>$model->client_id)),
								'linkOptions' => array(
									'class'=>'step-remove-ajax',
								),

							),
						),
						'color' => TbHtml::BUTTON_COLOR_WARNING,
						'size'  => TbHtml::BUTTON_SIZE_SM,
						'icon' => TbHtml::ICON_COG,
					),


					array(
						'label'=>'Status',
						'items'=>$_statuses_dropdown_items,
						'color' => TbHtml::BUTTON_COLOR_PRIMARY,
						'size'  => TbHtml::BUTTON_SIZE_SM,
						'icon' => TbHtml::ICON_INFO_SIGN,
					),

					array(
						'label' => 'Add Note',
						'color' => TbHtml::BUTTON_COLOR_INFO,
						'size'  => TbHtml::BUTTON_SIZE_SM,
						'icon' => TbHtml::ICON_PLUS,
						'class' => 'add-note',
						'url' => array(
							'note/create',
							's' => $step->id,
						),
					),

					array(
						'label' => 'Mark completed',
						'color' => TbHtml::BUTTON_COLOR_SUCCESS,
						'size'  => TbHtml::BUTTON_SIZE_SM,
						'icon' => TbHtml::ICON_OK,
						'class' => 'step-finish-ajax',
						'data-loading-text' => 'Updating ...',
						'url' => array(
							'step/finish',
							'step' => $step->id,
						),
					)
				),
				array(
					//'groupOptions' => array(
						'class' => 'pull-right step-actions'
					//)
				)
			);
			?>
		</div>
		<div class="panel-body <?php echo $step->statuscolor ?> panel-id-<?php echo $step->id;?>">
			<?php $this->widget( '\TbListView', array(
				'dataProvider' => $step->getNotesProvider(),
				'itemView'     => '//note/_viewitem',
				'viewData' => array('type'=>'note'),
				'id'=>'notes-'.$step->id,
			) );
			?>
		</div>
	</div>
<?php } ?>
<!--		STEPS END-->
</fieldset>


<!--		DOCUMENTS -->
<fieldset>
	<legend>Document list <?php
		echo TbHtml::linkButton(
			'Add Document',
			array(
				'url' => array( 'document/add', 'wid' => $model->id ),
				'icon' => TbHtml::ICON_PLUS,
				'color' => TbHtml::BUTTON_COLOR_SUCCESS,
				'size' => TbHtml::BUTTON_SIZE_XS,
				'class' => 'pull-right js-document-add',
			)
		);
		?></legend>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget( '\TbListView', array(
				'dataProvider' => $model->getDocumentsProvider(),
				'itemView'     => '//document/_viewitem',
				'viewData' => array('listview_id'=>'documents-list'),
				'id'=>'documents-list',
			) );
			?>
		</div>
	</div>
</fieldset>
<!--		DOCUMENTS END -->

<!-- Modal window code -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
			</div>
			<div class="modal-body">

			</div>
		</div>
	</div>
</div>
<!-- Modal window code END -->

<script>
	$(document).ready(function() {
		$('.phone-button, .voicemail-button, .email-button').on('click', function (e) {
			e.preventDefault();
			el = $(this);

			$('#ClientNote_content').val( el.data('placeholdertext') );
			$('#ClientNote_activity_type').val( el.data('activity') );
			if (!$('#collapse-add-client-note').hasClass('in')) $('#collapse-add-client-note').prev().trigger('click');
		});

		$('.add-note, .js-document-add, .js_send_sms').on('click', function (e) {
			e.preventDefault();
			el = $(this);
			$.ajax({
				url: el.attr('href'),
				data: {},
				type: 'get', // 'POST' | 'GET'
				dataType: 'json', //xml, html, script, json, jsonp, text
				beforeSend: function (xhr) {
					//xhr.overrideMimeType("text/plain; charset=x-user-defined");
				},
				cache: true //default: true, false for dataType 'script' and 'jsonp'

			}).done(function (data, textStatus, jqXHR) {
				$(".bs-example-modal-lg .modal-title").html(data.heading);
				$(".bs-example-modal-lg .modal-body").html(data.content);
				$(".bs-example-modal-lg").modal('show');
                if(el.hasClass('js_send_sms')){
					$('#Sms_phone').mask('+99999999999#');
				}
			}).fail(function (jqXHR, textStatus, errorThrown) {

			});

			//console.log($(this).attr('href'));
		});

		$('body')
			.on('click', '.close-modal', function(e){
				e.preventDefault();
				$(".bs-example-modal-lg").modal('hide');
			})
			.on('click', '.step-status-ajax', function(e){
				e.preventDefault();
				var el = $(this);
				$.ajax({
				  url: el.attr('href'),
				  //data: {},
				  type: 'POST', // 'POST' | 'GET'
				  dataType: 'json', //xml, html, script, json, jsonp, text
				  beforeSend: function ( xhr ) {

				  },
				  cache: false
				}).done(function ( data, textStatus, jqXHR ) {
					if (typeof data.status != 'undefined' && data.status == 'success' && typeof data.extra.bg != 'undefined') {

						//STATUS - update progress bars and panel
						updateStatus(data.extra.step, data.extra.bg);

						/*updateStatus('progress-total-'+data.extra.step, data.extra.bg, 'progress-bar');
						updateStatus(el.closest('.panel').find('.panel-body'), data.extra.bg)*/
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {

				}).always(function(jqXHR, textStatus, errorThrown) {

				});

			})
			.on('click', '.step-dates-ajax', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
			})
			.on('click', '.step-finish-ajax', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var el = $(this);
				$.ajax({
				  url: el.attr('href'),
				  //data: {},
				  type: 'POST', // 'POST' | 'GET'
				  dataType: 'json', //xml, html, script, json, jsonp, text
				  beforeSend: function ( xhr ) {
					 el.button('loading');
				  },
				  cache: false
				}).done(function ( data, textStatus, jqXHR ) {
					if (typeof data.status != 'undefined' && data.status == 'success' && typeof data.extra.bg != 'undefined' && typeof data.extra.step != 'undefined') {
						updateStatus(data.extra.step, data.extra.bg);
						updateProgressBar(data.extra.step, 100);
						$('#slider_basic_'+data.extra.step+'_slider').slider( "option", "value", 100 );
						$('#progress-'+data.extra.step).val(100);
					}

				}).fail(function(jqXHR, textStatus, errorThrown) {

				}).always(function(jqXHR, textStatus, errorThrown) {
					el.button('reset');
				});
			})
			.on('click', '.step-progress-ajax-submit', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var el = $(this).closest('.step-progress-ajax'), btn = $(this);

				$.ajax({
				  url: el.attr('href'),
				  data: {progress: el.find(':input').val()},
				  type: 'POST', // 'POST' | 'GET'
				  dataType: 'json', //xml, html, script, json, jsonp, text
				  beforeSend: function ( xhr ) {
					 btn.button('loading');
				  },
				  cache: false
				}).done(function ( data, textStatus, jqXHR ) {
					try {
						if (typeof data.extra != 'undefined' && data.status == 'success') {
							updateProgressBar(0, data.extra.total);
							for (i in data.extra.steps) {
								updateProgressBar(i, data.extra.steps[i]);
							}
						}
					} catch(e){

					}
				}).fail(function(jqXHR, textStatus, errorThrown) {

				}).always(function(jqXHR, textStatus, errorThrown) {
					btn.button('reset');
				});
			})
			.on('click', '.step-priority-ajax-submit', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var el = $(this).closest('.step-priority-ajax'), btn = $(this);

				$.ajax({
				  url: el.attr('href'),
				  data: {priority: el.find('select').val()},
				  type: 'POST', // 'POST' | 'GET'
				  dataType: 'json', //xml, html, script, json, jsonp, text
				  beforeSend: function ( xhr ) {
					 btn.button('loading');
				  },
				  cache: false
				}).done(function ( data, textStatus, jqXHR ) {
					try {
						if (typeof data.extra != 'undefined' && data.status == 'success') {
							updateProgressBar(0, data.extra.total);
							for (i in data.extra.steps) {
								updateProgressBar(i, data.extra.steps[i]);
							}
						}
					} catch(e){

					}
				}).fail(function(jqXHR, textStatus, errorThrown) {

				}).always(function(jqXHR, textStatus, errorThrown) {
					btn.button('reset');
				});
			})
			.on('click', '.item-clientnote, .item-note', function(e){
				e.preventDefault();
				el = $(this);
				$.ajax({
					url: el.attr('href'),
					data: {},
					type: 'get', // 'POST' | 'GET'
					dataType: 'json', //xml, html, script, json, jsonp, text
					beforeSend: function ( xhr ) {
						//xhr.overrideMimeType("text/plain; charset=x-user-defined");
					},
					cache: true //default: true, false for dataType 'script' and 'jsonp'

				}).done(function ( data, textStatus, jqXHR ) {
					$(".bs-example-modal-lg .modal-title").html(data.heading);
					$(".bs-example-modal-lg .modal-body").html(data.content);
					$(".bs-example-modal-lg").modal('show');
				}).fail(function(jqXHR, textStatus, errorThrown) {

				});
			})

			$('.step-dates-ajax').each(function(e){
				var el = $(this);
				el.children('span').html( moment( el.data('start'), 'M/D/YY HH:mm:ss').format('MM/DD/YY') + ' - ' + moment( el.data('end'), 'M/D/YY HH:mm:ss').format('MM/DD/YY') );
				$(this).daterangepicker(
					{
						ranges: {
							'Today': [moment(), moment()],
							'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
							'Last 7 Days': [moment().subtract('days', 6), moment()],
							'Last 30 Days': [moment().subtract('days', 29), moment()],
							'This Month': [moment().startOf('month'), moment().endOf('month')],
							'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
						},
						startDate: moment( el.data('start'), 'M/D/YY HH:mm:ss'),//.subtract('days', 29),
						endDate: moment(el.data('end'), 'M/D/YY HH:mm:ss'),
						applyClass: 'daterange-submit btn-success',
						//timePicker: true
					},
					function (start, end) {
						el.children('span').html(start.format('M/D/YY') + ' - ' + end.format('M/D/YY'));
					}
				).on('apply.daterangepicker', function (ev, picker) {
						$.ajax({
						  url: el.attr('href'),
						  data: {start:picker.startDate.format('YYYY-MM-DD HH:mm:ss'), end:picker.endDate.format('YYYY-MM-DD HH:mm:ss')},
						  type: 'POST',
						  dataType: 'json', //xml, html, script, json, jsonp, text
						  beforeSend: function ( xhr ) {
							  el.button('loading');
						  },
						  cache: false
						}).done(function ( data, textStatus, jqXHR ) {

						}).fail(function(jqXHR, textStatus, errorThrown) {

						}).always(function(jqXHR, textStatus, errorThrown) {
							el.button('reset');
						});

					});
			});

			/*{
				$(".bs-example-modal-lg .modal-title").html('Change dates');
				$(".bs-example-modal-lg .modal-body").load(el.attr('href'));
				$(".bs-example-modal-lg").modal('show');
			});*/
        $('body').on('click', '#documents-list a.delete', function(e){
            e.preventDefault();
            var el = $(this);
            $.ajax({
                type: 'POST',
                url: el.attr('href'),
                //data: el.parents("form").serialize()
                cache: false
            }).done(function ( data, textStatus, jqXHR ) {
                $.fn.yiiListView.update("documents-list");
            });
            return false;
        });
	});

	jQuery.fn.extend({
		stripColorClasses: function() {
			$(this).removeClass(function (index, css) {
				return (css.match(/\b[^\s]+-(success|warning|danger|info|primary)\b/g) || []).join(' ');
			});
			return $(this);
		}
	});

	function updateStatus(step, status){
		step = parseInt(step);
		status = status.replace(/bg-|progress-bar-/, '');

		$('.panel-id-'+step).stripColorClasses().addClass('bg-'+status);
		$('.progress-total-'+step +' .progress-bar').stripColorClasses().addClass('progress-bar-'+status);
	}

	function updateProgressBar(step, val){

		val = Math.abs(parseInt(val));
		val = ((val>100)?100:val);

		if (step == 0) {
			$('.progress-total .progress-bar').css('width', val+'%').text( val + '%' );
		} else {
			$('.progress-total-'+step +' .progress-bar').css('width', val+'%').text( val + '%' );
		}
	}

</script>