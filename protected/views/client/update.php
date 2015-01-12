<?php
/* @var $this ClientController */
/* @var $model User */
?>

<?php
$this->breadcrumbs = array(
	'Users'    => array( 'index' ),
	$model->id => array( 'view', 'id' => $model->id ),
	'Update',
);

?>

	<h1>Modify Client: <?php echo $model->fullname; ?></h1>

<?php $this->renderPartial( '_form', array( 'model' => $model ) ); ?>