<?php
/* @var $this IssuesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Outstanding Issues',
);

$this->menu=array(
	array('label'=>'Create OutstandingIssues', 'url'=>array('create')),
	array('label'=>'Manage OutstandingIssues', 'url'=>array('admin')),
);
?>

<h1>Outstanding Issues</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
