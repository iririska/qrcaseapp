<?php
/** @var $this ClientController
 * @var $model User
 * @var $form TbActiveForm
 */

//prepare date field for display
if (Controller::validateDate($model->dob) || Controller::validateDate($model->dob, 'Y-m-d')) {
    $model->dob = date('m/d/Y', strtotime($model->dob));
} else {
    $model->dob = '';
}
?>

    <div class="form">

        <?php

        $form = $this->beginWidget( 'booster.widgets.TbActiveForm', array(
            'id'                   => 'client-form',
            'type' => 'horizontal',
            //'action' => ($event->isNewRecord) ? Yii::app()->createUrl('calendar/addevent') : Yii::app()->createUrl('calendar/updateevent',array('id'=>$event->id)),
            // Please note: When you enable ajax validation, make sure the corresponding
            // controller action is handling ajax validation correctly.
            // There is a call to performAjaxValidation() commented in generated controller code.
            // See class documentation of CActiveForm for details on this.
            'enableClientValidation' => true,
            'enableAjaxValidation' => false
            )
        );
        
        echo $form->textFieldGroup($model, 'email', array( 'wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->textFieldGroup($model, 'firstname', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->textFieldGroup($model, 'lastname', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->telFieldGroup($model, 'phone', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->telFieldGroup($model, 'phone2', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->textFieldGroup($model, 'address', array('wrapperHtmlOptions' => array('class'=>'col-md-9')));
        echo $form->textFieldGroup($model, 'ssn', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        echo $form->datePickerGroup($model, 'dob', array( 'options'=>array(), 'wrapperHtmlOptions' => array('class'=>'col-md-2')));
        
        echo $form->dropDownListGroup($model, 'gender', array(
            'widgetOptions' => array(
                'data' => array(
                    'm' => 'Male',
                    'f' => 'Female'
                ),
            ),
            'wrapperHtmlOptions' => array('class'=>'col-md-2'))
        );
       
        echo $form->textFieldGroup($model, 'driver_license', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); 
        echo $form->textFieldGroup($model, 'case_nr', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));
        
        echo $form->dropDownListGroup(
            $model,
            'status',
            array(
                'widgetOptions' => array(
                    'data' => array(
                        '1' => 'Active',
                        '0' => 'Disabled'
                    )
                ),
                'wrapperHtmlOptions' => array('class'=>'col-md-2')
            )
        );
        
        echo $form->dropDownListGroup(
            $model,
            'case_type',
            array(
                'widgetOptions' => array(
                    'data' => CHtml::listData(WorkflowType::model()->findAll(), 'id', 'title')
                ),
                'wrapperHtmlOptions' => array('class'=>'col-md-3'), 'disabled' => !$model->isNewRecord
            )
        ); 
        
        echo $form->dropDownListGroup(
            $model,
            'document_list_id',
            array(
                'widgetOptions' => array(
                    'data' => CHtml::listData(DocumentListTemplate::model()->findAll(), 'id', 'title')
                ),
                'wrapperHtmlOptions' => array('class'=>'col-md-5'), 'disabled' => !$model->isNewRecord
            )
        ); 
        

        if (!$model->getIsNewRecord()) {
            echo $form->checkboxGroup($model, 'change_case_type', array('id' => 'change_case_type', 'hint' => 'Tick this checkbox if you want to change Case Type for this client.<br> Please be aware that changing Case Type will erase previous Case Type data'));
        }
        
        echo TbHtml::formActions(array(
            TbHtml::link( 'Cancel', array( 'client/admin' ), array( 'class' => 'btn btn-default' ) ),
            TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
                'class' => 'btn btn-primary'
            ))
        )); 
        
        $this->endWidget(); ?>

    </div><!-- form -->
    <br/><br/>