<?php
/* @var $this StepController */
/* @var $model Step */

$this->breadcrumbs=array(
	'Steps'=>array('index'),
	'Create',
);
?>

<h1>Add Step</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>