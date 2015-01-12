<?php
/* @var $this IssuesController */
/* @var $model OutstandingIssues */

$this->breadcrumbs=array(
	'Outstanding Issues'=>array('index'),
	'Create',
);
?>

<h1>Add Issue</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>