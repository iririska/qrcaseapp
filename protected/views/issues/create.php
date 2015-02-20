<?php
/* @var $this IssuesController */
/* @var $model OutstandingIssues */

$this->breadcrumbs=array(
	'Outstanding Issues'=>array('index'),
	'Create',
);
?>
<fieldset>
	<legend>Add Issue</legend>

    <?php $this->renderPartial('_form', array('model'=>$model)); ?>
</fieldset>