<?php
/* @var $this CaseController */
/* @var $model WorkflowType */
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
        echo $form->textFieldControlGroup($model,'title',array('span'=>5,'maxlength'=>255));
        echo $form->textFieldControlGroup($model,'created',array('span'=>5));
        echo $form->textFieldControlGroup($model,'modified',array('span'=>5)); 
        
        echo TbHtml::formActions(array(
            TbHtml::submitButton( 'Search', array( 'color' => TbHtml::BUTTON_COLOR_PRIMARY, ) )
        )); 
        
        $this->endWidget();?>

</div><!-- search-form -->