<?php
/* @var $this NoteController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Notes',
);

$this->menu=array(
	array('label'=>'Create Note','url'=>array('create')),
	array('label'=>'Manage Note','url'=>array('admin')),
);
?>

<h1>Notes</h1>

<?php $this->widget('\TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>