<?php
/* @var $this CaseController */
/* @var $model WorkflowType */
?>

<?php
$this->breadcrumbs = array(
	'Workflow Types' => array( 'index' ),
	'Create',
);
?>

<?php $this->renderPartial( '_form', array(
	'model' => $model,
	'steps' => $steps,
) ); ?>
