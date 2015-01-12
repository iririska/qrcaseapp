<?php
/* @var $this NoteController */
/* @var $model Note */
?>

<h1><?php echo date(Yii::app()->params["fullDateFormat"], strtotime($model->created)); ?></h1>

<?php
echo CHtml::encode(
	$model->content
);
/*$this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'author',
		'step_id',
		'content',
		'created',
		'updated',
	),
)); */?>