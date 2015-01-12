<?php
/* @var $this WorkflowController
 * @var $data Workflow
 *
 */
?>

<div class="list-group">
	<a href="<?php echo Yii::app()->createUrl('workflow/view', array('id'=>$data->id));?>" class="list-group-item">
		<h4 class="list-group-item-heading"><?php echo $data->client->fullname; ?></h4>
		<p class="list-group-item-text">
				<?php
				$dates = $data->getDates();

				echo sprintf("<strong>Start date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_start'])));
				echo sprintf("<strong>End date:</strong> %s <br>", date(Yii::app()->params['shortDateFormat'], strtotime($dates['date_end'])));

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

		</p>
	</a>
</div>