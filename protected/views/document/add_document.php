<?php
/**
 * @var $this DocumentController
 * @var $model Document
 */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);
?>

<h1>Add Document</h1>

<?php $this->renderPartial('_add', array('model'=>$model)); ?>