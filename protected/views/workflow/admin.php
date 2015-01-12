<?php
/* @var $this WorkflowController */
/* @var $model Workflow */

$this->breadcrumbs=array(
	'Workflows'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Workflow', 'url'=>array('index')),
	array('label'=>'Create Workflow', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#workflow-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Workflows</h1>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'workflow-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'client_id',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
