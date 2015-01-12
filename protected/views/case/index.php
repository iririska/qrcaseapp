<?php
/* @var $this CaseController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Workflow Types',
);

$this->menu=array(
	array('label'=>'Create WorkflowType','url'=>array('create')),
	array('label'=>'Manage WorkflowType','url'=>array('admin')),
);
?>

<h1>Workflow Types</h1>

<?php $this->widget('\TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>