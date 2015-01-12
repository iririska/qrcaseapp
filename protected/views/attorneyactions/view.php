<?php
/* @var $this AttorneyActionsController */
/* @var $model AttorneyActions */

$this->breadcrumbs=array(
	'Attorney Actions'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AttorneyActions', 'url'=>array('index')),
	array('label'=>'Create AttorneyActions', 'url'=>array('create')),
	array('label'=>'Update AttorneyActions', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AttorneyActions', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AttorneyActions', 'url'=>array('admin')),
);
?>

<h1>View AttorneyActions #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'client_id',
		'workflow_id',
		'step_id',
		'author',
		'status',
		'created',
		'updated',
	),
)); ?>
