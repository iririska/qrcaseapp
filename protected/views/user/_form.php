<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="form">
    <?php 
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'user-form',
        'enableAjaxValidation'=>true,
        'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
        'htmlOptions' => array(),
    ));
    ?>

        <p class="help-block">Fields with <span class="required">*</span> are required.</p>

        <?php 
        echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 4, 'maxlength' => 128 ) );
        if ($model->isNewRecord)
            echo $form->passwordFieldControlGroup( $model, 'password', array( 'span' => 4, 'maxlength' => 255 ) );

        if(Yii::app()->user->isAdmin)
            echo $form->dropDownListControlGroup( $model, 'role', CHtml::listData( AuthItem::model()->findAll("type='2' AND name != 'superadmin'"), 'name', 'description'), array( 'span' => 2 ) );
        else {
            echo $form->dropDownListControlGroup( $model, 'role', CHtml::listData( AuthItem::model()->findAll("name = 'user'"), 'name', 'description'), array( 'span' => 2 ) );
        }

        echo $form->dropDownListControlGroup( $model, 'status', array(
                '1' => 'Enabled',
                '2' => 'Disabled'
            ), array( 'span' => 2 ) ); ?>

     <?php 
    echo TbHtml::formActions(array(
        TbHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('color' => TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::link('Cancel',
            array('user/admin'),
            array('class' => 'btn btn-default')
        )
    )); 
    ?>

	<?php $this->endWidget(); ?>

</div><!-- form -->