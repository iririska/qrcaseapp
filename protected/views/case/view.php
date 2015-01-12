<?php
/* @var $this CaseController */
/* @var $model WorkflowType */
?>

<?php
$this->breadcrumbs=array(
	'Workflow Types'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List WorkflowType', 'url'=>array('index')),
	array('label'=>'Create WorkflowType', 'url'=>array('create')),
	array('label'=>'Update WorkflowType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete WorkflowType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage WorkflowType', 'url'=>array('admin')),
);
?>

<h1>View WorkflowType #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'title',
		'created',
		'modified',
	),
)); ?>