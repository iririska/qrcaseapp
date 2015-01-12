<?php
/* @var $this AttorneyActionsController */
/* @var $model AttorneyActions */

$this->breadcrumbs=array(
	'Attorney Actions'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AttorneyActions', 'url'=>array('index')),
	array('label'=>'Create AttorneyActions', 'url'=>array('create')),
	array('label'=>'View AttorneyActions', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AttorneyActions', 'url'=>array('admin')),
);
?>

<h1>Update AttorneyActions <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>