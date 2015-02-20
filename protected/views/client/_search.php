<?php
/* @var $this ClientController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<div class="wide form">

	<?php 
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'method' => 'get',
        'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
        'action' => Yii::app()->createUrl( $this->route ),
        'htmlOptions' => array(),
        
    ));
        echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 5, 'maxlength' => 128 ) );
        //echo $form->textFieldControlGroup( $model, 'role', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'created', array( 'span' => 5 ) );
        echo $form->textFieldControlGroup( $model, 'updated', array( 'span' => 5 ) );
        echo $form->textFieldControlGroup( $model, 'last_logged', array( 'span' => 5 ) );
        echo $form->textFieldControlGroup( $model, 'status', array( 'span' => 5 ) );
        echo $form->textFieldControlGroup( $model, 'firstname', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'lastname', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'phone', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'phone2', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'address', array( 'span' => 5, 'maxlength' => 255 ) );
        //echo $form->textFieldControlGroup( $model, 'case_type', array( 'span' => 5 ) );
        
        echo TbHtml::formActions(array(
            TbHtml::submitButton( 'Search', array( 'color' => TbHtml::BUTTON_COLOR_PRIMARY, ) )
        ));
        
        $this->endWidget(); ?>

</div><!-- search-form -->