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
            //'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
            //'action' => ($event->isNewRecord) ? Yii::app()->createUrl('calendar/addevent') : Yii::app()->createUrl('calendar/updateevent',array('id'=>$event->id)),
            // Please note: When you enable ajax validation, make sure the corresponding
            // controller action is handling ajax validation correctly.
            // There is a call to performAjaxValidation() commented in generated controller code.
            // See class documentation of CActiveForm for details on this.
            'enableClientValidation' => true,
            'enableAjaxValidation' => false
            )
        );

        ?>

        <?php 
        if(Yii::app()->user->checkAccess('superadmin') && $model->creator_id == Yii::app()->user->id ){
           $model->creator_id = 0;
           
        }
        if (Yii::app()->user->checkAccess('superadmin')):
            echo $form->dropDownListGroup(
                $model,
                'creator_id',
                array(
                    'widgetOptions' => array(
                        'data' => array('Select Attorney...') + CHtml::listData($user->attorney, 'id', 'email'),
                        'default' => 'eee'
                    ),
                    'wrapperHtmlOptions' => array('class'=>'col-md-4')
                )
            ); 
        endif;
        
        echo $form->textFieldGroup($model, 'email', array( 'wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->textFieldGroup($model, 'firstname', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->textFieldGroup($model, 'lastname', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->telFieldGroup($model, 'phone', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->telFieldGroup($model, 'phone2', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->textFieldGroup($model, 'address', array('wrapperHtmlOptions' => array('class'=>'col-md-9'))); ?>



        <?php echo $form->textFieldGroup($model, 'ssn', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->datePickerGroup($model, 'dob', array( 'options'=>array(), 'wrapperHtmlOptions' => array('class'=>'col-md-2'))); ?>


        <?php echo $form->dropDownListGroup($model, 'gender', array(
            'widgetOptions' => array(
                'data' => array(
                    'm' => 'Male',
                    'f' => 'Female'
                ),
            ),
            'wrapperHtmlOptions' => array('class'=>'col-md-2'))
        );

        ?>

        <?php echo $form->textFieldGroup($model, 'driver_license', array('wrapperHtmlOptions' => array('class'=>'col-md-4'))); ?>

        <?php echo $form->textFieldGroup($model, 'case_nr', array('wrapperHtmlOptions' => array('class'=>'col-md-4')));

        /*
        'ssn' => 'SSN',
                'dob' => 'DOB',
                'gender' => 'Gender',
                'driver_license' => 'Driver License',
                'case_nr' => 'Case #',
        */
        ?>



        <?php echo $form->dropDownListGroup(
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
        ?>

        <?php echo $form->dropDownListGroup(
            $model,
            'case_type',
            array(
                'widgetOptions' => array(
                    'data' => CHtml::listData(WorkflowType::model()->findAll(), 'id', 'title')
                ),
                'wrapperHtmlOptions' => array('class'=>'col-md-3'), 'disabled' => !$model->isNewRecord
            )
        ); ?>

         <?php echo $form->dropDownListGroup(
            $model,
            'document_list_id',
            array(
                'widgetOptions' => array(
                    'data' => CHtml::listData(DocumentListTemplate::model()->findAll(), 'id', 'title')
                ),
                'wrapperHtmlOptions' => array('class'=>'col-md-5'), 'disabled' => !$model->isNewRecord
            )
        ); ?>

        <?php

        if (!$model->getIsNewRecord()) {
            echo $form->checkboxGroup($model, 'change_case_type', array('id' => 'change_case_type', 'hint' => 'Tick this checkbox if you want to change Case Type for this client.<br> Please be aware that changing Case Type will erase previous Case Type data'));
        }

        ?>

        <?php echo $form->textFieldGroup($model, 'google_calendar_id', array(
            'wrapperHtmlOptions' => array('class'=>'col-md-9'),
            'widgetOptions' => array(
                'htmlOptions'=>array('placeholder'=>'Existing Google Calendar ID'),
            ),

            'hint' =>
                'No calendar created yet for this client? <a href="https://www.google.com/calendar/render">Create one</a><br>
                 Calendar ID can be found in calendar properties and must be a string of type <em>r1hnd4kr49dbcp6c3n4l8fasjs@group.calendar.google.com</em><br>
                 <a href="#more-calendar-id" id="more-calendar-info">more info</a>
                '
        )); ?>

        <div class="form-group" id="hint-more-gcal">
            <label class="col-sm-3 control-label">&nbsp;</label>
            <div class="col-md-9 col-sm-9 bg-warning">

                <h4>Create new calendar:</h4>
                <dl>
                    <dt class="gcal-hint-step-label">Step <small>Create calendar</small></dt>
                    <dd>
                        <img src="<?php echo Yii::app()->getBaseUrl()?>/images/gcal-hint/create.jpg">
                    </dd>

                    <dt class="gcal-hint-step-label">Step 2 <small>Adjust settings</small></dt>
                    <dd>
                        <img src="<?php echo Yii::app()->getBaseUrl()?>/images/gcal-hint/settings.jpg">
                        <br>

                        <ul>
                            <li><em>A</em>: name calendar</li>
                            <li>select timezone (optional)</li>
                            <li><em>B</em>: enter following management account: <strong class="text-nowrap"><?php echo Yii::app()->params['googleApiConfig']['service_acc_email']?></strong></li>
                            <li><em>C</em>: select <strong class="text-nowrap">Make changes to events</strong> in dropdown</li>
                            <li>Click <strong>Add Person</strong></li>
                            <li>Click <strong>Create Calendar</strong></li>
                        </ul>
                    </dd>

                </dl>
                <h4>Get calendar ID:</h4>
                <dl>

                    <dt class="gcal-hint-step-label">Step 3 <small>Edit settings of your created calendar</small></dt>
                    <dd>
                        <img src="<?php echo Yii::app()->getBaseUrl()?>/images/gcal-hint/edit_settings.jpg">
                    </dd>

                    <dt class="gcal-hint-step-label">Step 4 <small>Copy calendar ID and paste it into <strong><?php echo $model->getAttributeLabel('google_calendar_id');?></strong> field above</small></dt>
                    <dd>
                        <img src="<?php echo Yii::app()->getBaseUrl()?>/images/gcal-hint/calendar_id.jpg">
                    </dd>
                </dl>

            </div>
        </div>

        <?php
        echo TbHtml::formActions(array(
            TbHtml::link( 'Cancel', array( 'client/admin' ), array( 'class' => 'btn btn-default' ) ),
            TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
                'class' => 'btn btn-primary'
            ))
        )); 
        
        $this->endWidget(); ?>

    </div><!-- form -->
<?php
Yii::app()->clientScript->registerScript('enlarge-qr',
<<<JS

$('#change_case_type').on('change', function(){
	$('select[name*="case_type"').attr('disabled', !$(this).attr('checked'));
});

$('#more-calendar-info').on('click', function(e){
  $('#hint-more-gcal').slideToggle({duration:300, easing: 'swing'});
})
JS
    ,
	CClientScript::POS_END

);