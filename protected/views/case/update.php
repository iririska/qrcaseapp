<?php
/* @var $this CaseController
 * @var $model WorkflowType
 * @var $steps Step[]
 */
?>

<?php
$this->breadcrumbs=array(
	'Workflow Types'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

?>
<fieldset>
	<legend>Update WorkflowType <?php echo $model->id; ?></legend>

    <?php $this->renderPartial('_form', array('model'=>$model, 'steps' => $steps)); ?>
</fieldset>