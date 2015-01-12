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

	<h1>Add Case Type</h1>

<?php $this->renderPartial( '_form', array(
	'model' => $model,
	'steps' => $steps,
) ); ?>