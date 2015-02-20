<?php
/* @var $this AttorneyActionsController */
/* @var $model AttorneyActions */

$this->breadcrumbs=array(
	'Attorney Actions'=>array('index'),
	'Manage',
);
?>

<fieldset>
	<legend>Manage Attorney Actions</legend>

<?php
    $this->widget( '\TbGridView', array(
		'id'                    => 'user-grid',
		'dataProvider'          => $model->searchUAction(),
		'filter'                => null,//$model,
		'rowCssClassExpression' => '$data->status == 0 ? " danger" : ""',
		'pagerCssClass'         => 'list-pager',
		'pager'                 => array(
			'class' => '\TbPager',
		),
		'type'                  => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
		'columns'               => array(
			'id',
			//'text',
			array(
				'name'   => 'client_id',
				'header' => 'Client',
				'type'   => 'raw',
				'value'  => 'CHtml::link("{$data->client->firstname} {$data->client->lastname} {$data->client->email}", array("workflow/view", "id"=>$data->client->current_workflow->id, "c"=>$data->client->id))',
			),
			array(
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
			/*array(
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
				'header'   => '',
				'class'    => 'bootstrap.widgets.TbButtonColumn',
				'template' => '{delete}',
				//'class'=>'bootstrap.widgets.btnDropdown',
			),
		),
	));
    ?>
</fieldset>