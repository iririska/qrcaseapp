<?php
/* @var $this AttorneyActionsController */
/* @var $model AttorneyActions */

$this->breadcrumbs=array(
	'Attorney Actions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AttorneyActions', 'url'=>array('index')),
	array('label'=>'Manage AttorneyActions', 'url'=>array('admin')),
);
?>

<h1>Create AttorneyActions</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>