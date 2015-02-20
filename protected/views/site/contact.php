<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle=Yii::app()->name . ' - Contact Us';
$this->breadcrumbs=array(
	'Contact',
);
?>

<h1>Contact Us</h1>

<?php if(Yii::app()->user->hasFlash('contact')): ?>

    <div class="alert alert-success in alert-block fade">
        <a href="#" class="close" data-dismiss="alert" type="button">x</a>
        <?php echo Yii::app()->user->getFlash('contact'); ?>
    </div>

<?php else: ?>

<p>
If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
</p>

<div class="form">

    <?php 
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'contact-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
        'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
        'htmlOptions' => array(),
    ));
    ?>
    <fieldset>
        <p class="help-block">Fields with <span class="required">*</span> are required.</p>
        <?php 
        echo $form->textFieldControlGroup($model, 'name', array( 'span' => 5 ));
        echo $form->textFieldControlGroup($model, 'email', array( 'span' => 5 ));
        echo $form->textFieldControlGroup($model, 'subject', array( 'span' => 8, 'maxlength'=>128 ));
        echo $form->textAreaControlGroup($model, 'body', array( 'span' => 8, 'rows'=>5 ));
        
        if(CCaptcha::checkRequirements()): ?>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-5">
                    <?php $this->widget('CCaptcha');?>
                </div>
            </div>
            <?php echo $form->textFieldControlGroup($model,'verifyCode', 
                array( 'span' => 5, 'help' => 'Please enter the letters as they are shown in the image above.<br/>Letters are not case-sensitive.' )
            );?>
        <?php endif; ?>
    </fieldset>
    <?php 
    echo TbHtml::formActions(array(
        TbHtml::submitButton('Submit', array('color' => TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::resetButton('Reset'),
    )); 
    ?>


<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>