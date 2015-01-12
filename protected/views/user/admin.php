<?php
/* @var $this UserController */
/* @var $model User */


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

	<h1>Manage Users</h1>

	<div class="col-md-9">
		<?php /*
		<p>
			You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
				&lt;&gt;</b>
			or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
		</p> */ ?>

		<?php echo CHtml::link( 'Filter users', '#', array( 'class' => 'search-button btn' ) ); ?>
		<div class="search-form" style="display:none">
			<?php $this->renderPartial( '_search', array(
				'model' => $model,
			) ); ?>
		</div>
		<!-- search-form -->
	</div>
	<div class="col-md-3">
		<?php echo CHtml::link('Add User', array('user/create'), array( 'class' => 'btn btn-success pull-right' ) ); ?>
	</div>

<?php $this->widget( '\TbGridView', array(
	'id'           => 'user-grid',
	'dataProvider' => $model->search(),
	'filter'       => null,//$model,
	'type'         => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
	'columns'      => array(
		'id',
		'email',
		array(
			'name'   => 'role',
			'header' => 'User Type',
			'type'   => 'raw',
			'value'  => 'CHtml::encode($data->role=="admin"?"Attorney":"Paralegal")',
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
		),

		array(
			'name'   => 'last_logged',
			'header' => 'Last Logged',
			'type'   => 'raw',
			'value'  => '!empty($data->last_logged)?date(Yii::app()->params["fullDateFormat"], strtotime($data->last_logged)):""',
		),
		/*

		'status',
		'firstname',
		'lastname',
		'phone',
		'phone2',
		'address',
		'case_type',
		*/
		array(
			'header'      => '',
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template'    => '{update} {delete}',
			//'class'=>'bootstrap.widgets.btnDropdown',
		),
	),
) ); ?>