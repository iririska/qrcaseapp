<?php
/* @var $this NoteController */
/* @var $model Note */
?>

<?php
$this->breadcrumbs=array(
	'Notes'=>array('index'),
	'Create',
);

?>

<h1>Add Note</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>