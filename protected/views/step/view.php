<?php
/* @var $this StepController */
/* @var $model Step */

$this->breadcrumbs=array(
	'Steps'=>array('index'),
	$model->id,
);
?>

<h1><?php echo $model->title; ?></h1>



<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'workflow_id',
		'priority',
		'date_start',
		'date_end',
		'status',
		'created',
	),
));

  ?>
