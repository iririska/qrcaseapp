<?php
/* @var $this WorkflowController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
	'Clients',
);
?>

<h1>Cases</h1>

<?php if (Yii::app()->user->checkAccess('createClient')) { ?>
<div class="row">
	<div class="col-md-12">
		<?php
		echo TbHtml::linkButton( 'Add Client', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
			'class' => 'pull-right'
		) );
		?>
	</div>
</div>
<?php } ?>
<div class="row">
	<div class="col-md-12">
		<?php $this->widget( '\TbListView', array(
			'dataProvider' => $dataProvider,
			'itemView'     => '_view',
		) ); ?>
	</div>
</div>