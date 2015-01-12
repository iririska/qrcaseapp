<?php
/* @var $this ClientController */
/* @var $model Client */


$this->breadcrumbs = array(
	'Users' => array( 'index' ),
	'Manage',
);

Yii::app()->clientScript->registerScript( 'search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#user-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
" );
?>

	<h1>Manage Clients</h1>

	<div class="col-md-16">
		<?php /*<p>
	    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
	        &lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p> */
		?>

		<?php echo CHtml::link( 'Filter clients', '#', array( 'class' => 'search-button btn' ) ); ?>
		<div class="search-form" style="display:none">
			<?php $this->renderPartial( '_search', array(
				'model' => $model,
			) ); ?>
		</div>
		<!-- search-form -->
	</div>

	<br>

	<div class="col-md-16 clearfix">
		<?php echo CHtml::link( 'Add Client', array( 'client/create' ), array( 'class' => 'btn btn-success pull-right' ) ); ?>
	</div>

	<br>

<?php $this->widget( '\TbGridView', array(
	'id'                    => 'user-grid',
	'dataProvider'          => $model->search(),
	'filter'                => null,//$model,
	'type'         => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
	'rowCssClassExpression' => '$data->color',
	'columns'               => array(
		//'id',
		array(
			'name'    => 'email',
			'header'  => 'Email',
			'type'    => 'raw',
			'value'   => 'CHtml::link($data->getFullName(), Yii::app()->controller->createUrl("workflow/view", array("id" => $data->current_workflow->id, "c" => $data->id)))',
			'visible' => true,
		),
		//'email',
		//'password',
		//'role',
		/*array(
			'name'    => 'created',
			'header'  => 'Created',
			'type'    => 'raw',
			'value'   => 'date(Yii::app()->params["fullDateFormat"], strtotime($data->created))',
			'visible' => true,
		),
		array(
			'name'    => 'updated',
			'header'  => 'Updated',
			'type'    => 'raw',
			'value'   => '(empty($data->updated)?"-":date(Yii::app()->params["fullDateFormat"], strtotime($data->updated)))',
			'visible' => true,
		),
		array(
			'name'    => 'last_logged',
			'header'  => 'Last activity',
			'type'    => 'raw',
			'value'   => '(empty($data->last_logged)?"-":date(Yii::app()->params["fullDateFormat"], strtotime($data->last_logged)))',
			'visible' => true,
		),
		array(
			'name'    => 'status',
			'header'  => 'Status',
			'type'    => 'raw',
			'value'   => '$data->statusName',
			'visible' => true,
		),
		'firstname',
		'lastname',
		'phone',
		'phone2',
		'address',*/
		//'case_type',
		array(
			'header'      => '',
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template'    => /*{workspace}*/' {update} {delete}',
			'htmlOptions' => array( 'style' => 'text-align: center; white-space: nowrap' ),
			'buttons'     => array(
				/*'view'   => array(
					'label'    => '<span class="icon-tasks"></span>',
					// text label of the button
					'url'      => 'Yii::app()->controller->createUrl("view",array("id"=>$data->id))',
					'imageUrl' => false,
					'options'  => array( 'class' => 'btn btn-warning', 'title' => 'Media' ),
					'click'    => '',
				),*/
				/*'workspace' => array(
					'icon'    => \TbHtml::ICON_LIST_ALT,     // text label of the button
					'url'      => 'Yii::app()->controller->createUrl("workflow/view", array("id" => $data->current_workflow->id, "c" => $data->id))',
					'label' => 'Workspace',
					//'imageUrl' => false,
					//'options'  => array( 'class' => 'btn btn-success show-modal modal-big', 'title' => 'Workspace' ),
					//'click'    => '',
				),*/
				'update' => array(
					//'label'    => '<span class="icon-edit"></span>',     // text label of the button
					'url'      => 'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
					//'imageUrl' => false,
					//'options'  => array( 'class' => 'btn btn-primary show-modal', 'title' => 'Edit' ),
					//'click'    => '',
				),
				'delete' => array(
					//'label'    => '<span class="icon-trash"></span>',
					// text label of the button
					//'url'      => 'Yii::app()->controller->createUrl("delete",array("id"=>$data->id))',
					//'imageUrl' => false,
					//'options'  => array( 'class' => 'delete btn btn-danger'),
				),
			),
		),
	),
) ); ?>