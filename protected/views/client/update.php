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
<fieldset>
	<legend>Modify Client: <?php echo $model->fullname; ?></legend>

    <?php $this->renderPartial( '_form', array( 'model' => $model ) ); ?>
</fieldset>