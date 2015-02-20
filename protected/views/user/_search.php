<?php
/* @var $this UserController */
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

        echo $form->textFieldControlGroup( $model, 'id', array( 'span' => 5 ) );
        echo $form->textFieldControlGroup( $model, 'email', array( 'span' => 5, 'maxlength' => 128 ) );
        echo $form->textFieldControlGroup( $model, 'role', array( 'span' => 5, 'maxlength' => 45 ) );
        echo $form->textFieldControlGroup( $model, 'status', array( 'span' => 5 ) ); 

        
        echo TbHtml::formActions(array(
            TbHtml::submitButton( 'Search', array( 'color' => TbHtml::BUTTON_COLOR_PRIMARY, ) )
        )); 
        
        $this->endWidget(); ?>

</div><!-- search-form -->