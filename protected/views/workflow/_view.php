<?php
/* @var $this WorkflowController
 * @var $data Workflow
 */
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title"><?php echo Chtml::link($data->client->fullname, array('client/view', 'id'=>$data->client->id)); ?></h2>
		<?php
		echo TbHtml::link('Go to the case', array('workflow/view', 'id'=>$data->id), array('class'=>'btn btn-success text-right'));
		?>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-7">
				<!-- Nav tabs -->
				<ul class="nav nav-pills" role="tablist">
					<li class="active"><a href="#home<?php echo $data->id;?>" role="tab" data-toggle="tab">Info</a></li>
					<li><a href="#notes<?php echo $data->id;?>" role="tab" data-toggle="tab">Client Notes</a></li>
					<?php /* <li><a href="#history<?php echo $data->id;?>" role="tab" data-toggle="tab">History</a></li> */ ?>
					<li>
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							Actions <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><?php echo CHtml::link('View', array('workflow/view', 'id'=>$data->id, 'c'=>$data->client->id));?></li>
							<li><?php echo CHtml::link('Add Step', array('step/create', 'wid'=>$data->id));?></li>
						</ul>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane active" id="home<?php echo $data->id;?>">
						<?php $this->renderPartial('/_partials/_client_info', array('data'=>$data->client));

						$dates = $data->getDates();

						echo sprintf("<strong>Start date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_start'])));
						echo sprintf("<strong>End date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_end'])));

						//echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r(urlencode('http://ctcommerce.com/CRM/index.php?r=workflow/view&id=33&c=32'), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

						?>
					</div>
					<div class="tab-pane" id="notes<?php echo $data->id;?>">
						<!--						Client notes        -->
						<?php
						/** @var ClientNote $clientnote */
						foreach ( $data->client->notes as $clientnote ) { ?>
							<blockquote>
								<p class="smal"><?php echo CHtml::encode($clientnote->content)?></p>
								<footer><?php echo date(Yii::app()->params["fullDateFormat"], strtotime($clientnote->created));?> &nbsp; <cite title="<?php echo $clientnote->creator->email?>"><?php echo $clientnote->creator->email?></cite></footer>
							</blockquote>
							<hr>
						<?php
						}
						?>
					</div>
					<div class="tab-pane" id="history<?php echo $data->id;?>">
						<!--						History data         -->
					</div>
				</div>

			</div>
			<div class="col-md-5">
				<?php
				$overall_progress = $data->getOverallProgress();
				if ( $overall_progress < 100 ) {
					$overall_progress_class = 'progress-bar-warning active';
				} else {
					$overall_progress_class = 'progress-bar-success';
				}
				?>
				<div class="progress">
					<div class="progress-bar <?php echo $overall_progress_class; ?>  progress-bar-striped "
					     role="progressbar" aria-valuenow="<?php echo $overall_progress; ?>" aria-valuemin="0"
					     aria-valuemax="100"
					     style="width: <?php echo (int) $overall_progress; ?>%;"><?php echo $overall_progress; ?>%
					</div>
				</div>

				<div class="list-group">
					<?php
					/* @property Step[] $steps */
					foreach ( $data->steps as $i => $step ) {
						$_nr       = $i + 1;
						$_title    = CHtml::encode( $step->title );
						$_progress = 'progress-bar-' . $step->getStatusProgress(); //progress bar color
						$_percent  = $step->progress; // progress %
						$_active   = $step->progress == 100 ? '' : 'active';
						?>
						<a href="<?php echo Yii::app()->createUrl('step/view', array('id'=>$step->id));?>" class="list-group-item" data-step="<?php echo $step->id ?>">
							<strong><?php echo $_nr ?></strong>. <?php echo $_title; ?><br>

							<div class="progress x-thin">
								<div
									class="progress-bar <?php echo $_progress; ?>  progress-bar-striped <?php echo $_active ?>"
									role="progressbar" aria-valuenow="<?php echo $_percent; ?>" aria-valuemin="0"
									aria-valuemax="100"
									style="width: <?php echo (int) $_percent; ?>%;"><?php echo $_percent; ?>%
								</div>
							</div>
						</a>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
