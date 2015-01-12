<?php
/* @var $this ClientController */
/* @var $model User */
?>

<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);
?>

<h1>Add Client</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>