<?php
/* @var $this CaseController */
/* @var $model WorkflowType */


$this->breadcrumbs=array(
	'Workflow Types'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#workflow-type-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Case Types</h1>
	<div class="col-md-9">
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
	</div>
	<div class="col-md-3">
		<?php echo CHtml::link( 'Add Case Type', array( 'case/create' ), array( 'class' => 'btn btn-success pull-right' ) ); ?>
	</div>

<?php $this->widget('\TbGridView',array(
	'id'=>'workflow-type-grid',
	'dataProvider'=>$model->search(),
	'filter'                => null,//$model,
	'type'         => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
	'columns'=>array(
		//'id',
		array(
			'name'    => 'title',
			//'header'  => 'Title',
			'type'    => 'raw',
			'value'   => '$data->title . sprintf(" <em style=\"color:%s\"> / %s steps</em>", (count($data->template_steps)>0?"rgba(0,0,0,0.4)":"rgba(255,0,0,0.4)"), count($data->template_steps) ) ',
			'visible' => true,
		),
		array(
			'name'    => 'created',
			'header'  => 'Created',
			'type'    => 'raw',
			'value'   => 'date(Yii::app()->params["fullDateFormat"], strtotime($data->created))',
			'visible' => true,
		),
		array(
			'name'    => 'modified',
			'header'  => 'Updated',
			'type'    => 'raw',
			'value'   => '(empty($data->updated)?"-":date(Yii::app()->params["fullDateFormat"], strtotime($data->modified)))',
			'visible' => true,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'    => '{update} {delete}',
		),
	),
)); ?>