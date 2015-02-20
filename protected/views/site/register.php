<?php
/* @var $this SiteController */
/* @var $model User */
/* @var $form CActiveForm */

$this->pageTitle   = Yii::app()->name . ' - Register';
$this->breadcrumbs = array(
	'Register',
);
?>
<div class="logo-container text-center">
	<?php echo CHtml::image('/images/logo.png', Yii::app()->name, array('title'=>Yii::app()->name));?>
</div>
<?php 
if(Yii::app()->user->hasFlash('success')):?>
    <div class="col-md-offset-3 col-md-6">
        <div class="alert alert-success in alert-block fade">
            <a href="#" class="close" data-dismiss="alert" type="button">x</a>
            <?php echo Yii::app()->user->getFlash('success'); ?>
        </div>
    </div>
<?php 
endif; 
$this->renderPartial('_register_form', array('model'=>$model)); 
?>
