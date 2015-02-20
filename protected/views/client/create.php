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
<fieldset>
	<legend>Add Client</legend>
    <?php $this->renderPartial('_form', array('model'=>$model)); ?>
</fieldset>