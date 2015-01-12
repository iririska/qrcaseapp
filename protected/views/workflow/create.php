<?php
/* @var $this WorkflowController */
/* @var $model Workflow */

$this->breadcrumbs=array(
	'Workflows'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Workflow', 'url'=>array('index')),
	array('label'=>'Manage Workflow', 'url'=>array('admin')),
);
?>

<h1>Add Workflow</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>