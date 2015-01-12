<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->pageTitle   = Yii::app()->name . ' - Login';
$this->breadcrumbs = array(
	'Login',
);
?>
<div class="logo-container text-center">
	<?php echo CHtml::image('images/logo.png', Yii::app()->name, array('title'=>Yii::app()->name));?>
</div>

<div class="col-md-offset-4 col-md-4">
	<?php $form = $this->beginWidget( 'CActiveForm', array(
		'id'                     => 'login-form',
		'enableClientValidation' => true,
		'clientOptions'          => array(
			'validateOnSubmit' => true,
		),
		'htmlOptions'            => array(
			'class' => "form-horizontal",
			'role'  => "form"
		)
	) ); ?>

	<div class="form-group">
		<?php echo $form->labelEx( $model, 'username' ); ?>
		<?php echo $form->textField( $model, 'username', array( 'class' => "form-control" ) ); ?>
		<?php echo $form->error( $model, 'username' ); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx( $model, 'password' ); ?>
		<?php echo $form->passwordField( $model, 'password', array( 'class' => "form-control" ) ); ?>
		<?php echo $form->error( $model, 'password' ); ?>
	</div>

	<div class="form-group rememberMe">
		<?php echo $form->checkBox( $model, 'rememberMe' ); ?>
		<?php echo $form->label( $model, 'rememberMe' ); ?>
		<?php echo $form->error( $model, 'rememberMe' ); ?>
	</div>

	<div class="row">
	<?php echo CHtml::submitButton( 'Login', array( 'class' => 'btn btn-primary' ) ); ?>
	</div>

	<?php $this->endWidget(); ?>
	<!-- form -->
</div>