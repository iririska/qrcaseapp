<?php
/* @var $this IssuesController */
/* @var $model OutstandingIssues */

$this->breadcrumbs=array(
	'Outstanding Issues'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#outstanding-issues-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Outstanding Issues</h1>

	<div class="col-md-3 col-md-offset-9">
		<?php echo CHtml::link('Add Issue', array('issues/create'), array( 'class' => 'btn btn-success pull-right' ) ); ?>
	</div>
<?php

$this->widget('booster.widgets.TbExtendedGridView',
	array(
	'id'           => 'user-grid',
	'dataProvider' => $model->search(),
	'filter'       => null,//$model,
	'rowCssClassExpression' => '$data->status == 0 ? " danger" : ""',
	'pagerCssClass' => 'list-pager',
	'pager' => array(
		'class' => '\TbPager',
	),
	'type'         => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
	'columns'      => array(
		'id',
		array(
			'class'=>'booster.widgets.TbRelationalColumn',
			'name' => 'title',
			'url' => $this->createUrl('issues/content'),
			'value'=> 'CHtml::encode(Yii::app()->format->truncateText( $data->text ))',
			/*'afterAjaxUpdate' => 'js:function(tr,rowid,data){
                bootbox.alert("I have afterAjax events too! This will only happen once for row with id: "+rowid);
            }'*/
		),
		array(
			'name'   => 'client_id',
			'header' => 'Client',
			'type'   => 'raw',
			'value'  => 'CHtml::link("{$data->client->firstname} {$data->client->lastname} / {$data->client->email}", array("workflow/view", "id"=>$data->client->current_workflow->id, "c"=>$data->client->id))',
		),
		/*array(
			'name'   => 'author',
			'header' => 'Creator',
			'type'   => 'raw',
			'value'  => '$data->creator->email',
		),
		array(
			'name'   => 'status',
			'header' => 'Status',
			'type'   => 'raw',
			'value'  => '$data->status==1?"Active":"Disabled"',
		),
		array(
			'name'   => 'created',
			'header' => 'Date Created',
			'type'   => 'raw',
			'value'  => '!empty($data->created)?date(Yii::app()->params["fullDateFormat"], strtotime($data->created)):""',
		),
		array(
			'name'   => 'updated',
			'header' => 'Date Updated',
			'type'   => 'raw',
			'value'  => '!empty($data->updated)?date(Yii::app()->params["fullDateFormat"], strtotime($data->updated)):""',
		),*/
		array(
			'header'      => '',
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template'    => '{update} {delete}',
			//'class'=>'bootstrap.widgets.btnDropdown',
		),
	),
) );
