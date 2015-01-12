<?php
/* @var $this AttorneyActionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Attorney Actions',
);

$this->menu=array(
	array('label'=>'Create AttorneyActions', 'url'=>array('create')),
	array('label'=>'Manage AttorneyActions', 'url'=>array('admin')),
);
?>

<h1>Attorney Actions</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
