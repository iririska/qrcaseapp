<?php
/* @var $this UserController */
/* @var $model User */
?>

<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);

/*$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Manage User', 'url'=>array('admin')),
);*/
?>
<fieldset>
	<legend>Add User</legend>

    <?php $this->renderPartial('_form', array('model'=>$model)); ?>
</fieldset>