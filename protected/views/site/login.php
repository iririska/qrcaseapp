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
	<?php echo CHtml::image('/images/logo.png', Yii::app()->name, array('title'=>Yii::app()->name));?>
</div>

<div class="col-md-offset-4 col-md-4">
   <?php 
   foreach(Yii::app()->user->getFlashes() as $key => $message) {?>
        <div class="alert alert-<?php echo $key; ?> in alert-block fade">
            <a href="#" class="close" data-dismiss="alert" type="button">x</a>
            <?php echo $message; ?>
        </div>
    <?php 
   } 


    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'login-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
            /*'validateOnChange'=>true,
            'validateOnType'=>true,*/  
        ),
        'layout' => TbHtml::FORM_LAYOUT_VERTICAL,
        'htmlOptions' => array(),
    ));
    ?>

    <fieldset>
        <?php
        echo $form->textFieldControlGroup($model, 'username');
        echo $form->passwordFieldControlGroup($model, 'password');
        echo $form->checkBoxControlGroup($model, 'rememberMe');
        ?>
    </fieldset>

	<?php 
    echo TbHtml::formActions(array(
        TbHtml::submitButton('Login', array('class' => 'btn btn-success')),
        TbHtml::link('Sing up',
            array('site/register'),
            array('class' => 'pull-right btn btn-default')
        )
    )); 
    ?>
    
	<?php $this->endWidget(); ?>

</div>