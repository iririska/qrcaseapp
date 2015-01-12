<?php
/* @var $this IssuesController */
/* @var $model OutstandingIssues */

$this->breadcrumbs=array(
	'Outstanding Issues'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Modify outstanding issue #' . CHtml::encode($model->id),
);
?>

<h1>Modify outstanding issue # <?php echo CHtml::encode($model->id);?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>