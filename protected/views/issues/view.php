<?php
/* @var $this IssuesController */
/* @var $model OutstandingIssues */

$this->breadcrumbs=array(
	'Outstanding Issues'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List OutstandingIssues', 'url'=>array('index')),
	array('label'=>'Create OutstandingIssues', 'url'=>array('create')),
	array('label'=>'Update OutstandingIssues', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete OutstandingIssues', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage OutstandingIssues', 'url'=>array('admin')),
);
?>

<h1>View OutstandingIssues #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'text',
		'author',
		'status',
		'created',
		'updated',
	),
)); ?>
