<?php
/* @var $this UserController */
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
	<legend>Update user data</legend>

	<?php $this->renderPartial( '_form', array( 'model' => $model ) ); ?>
</fieldset>
<br>
<fieldset>
	<legend>Change password</legend>

	<?php $this->renderPartial( '_password_form', array( 'model' => $model ) ); ?>
</fieldset>