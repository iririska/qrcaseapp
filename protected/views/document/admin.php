<?php
/* @var $this DocumentController */
/* @var $model Document */

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
<fieldset>
	<legend>Documents List templates</legend>
    <div class="row">
        <div class="col-md-3 col-md-offset-9">
            <?php echo CHtml::link('Add Documents List', array('document/create'), array( 'class' => 'btn btn-success pull-right' ) ); ?>
        </div>
    </div>


<?php
$this->widget('\TbGridView', array(
	'id'            => 'documentlist-grid',
	'type'          => 'striped bordered',
	'dataProvider'  => $model->search(),
	'filter'        => null,
		//$model,
	'pagerCssClass' => 'list-pager',
	'pager'         => array(
		'class' => '\TbPager',
	),
	//'type'         => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
	'columns'      => array(
		'id',
		array(
			'class'=>'booster.widgets.TbRelationalColumn',
			'name' => 'title',
			'url' => $this->createUrl('document/listdocuments'),
			'value'=> 'CHtml::encode($data->title)',
			/*'afterAjaxUpdate' => 'js:function(tr,rowid,data){
                bootbox.alert("I have afterAjax events too! This will only happen once for row with id: "+rowid);
            }'*/
		),
		array(
			'name'   => 'author',
			'header' => 'Creator',
			'type'   => 'raw',
			'value'  => 'CHtml::encode($data->creator->email)',
		),
		array(
			'name'   => 'created',
			'header' => 'Date Created',
			'type'   => 'raw',
			'value'  => '!empty($data->created)?date(Yii::app()->params["fullDateFormat"], strtotime($data->created)):""',
		),
		array(
			'header'      => '',
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template'    => '{update} {delete}',
			//'class'=>'bootstrap.widgets.btnDropdown',
		),
	),
) );
?>
</fieldset>
