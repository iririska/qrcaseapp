<div class="col-md-offset-3 col-md-6">
    
    <?php 
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'register-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
        'layout' => TbHtml::FORM_LAYOUT_VERTICAL,
        'htmlOptions' => array(),
    ));
    ?>
        <fieldset>
            <p class="help-block">Fields with <span class="required">*</span> are required.</p>
            <?php 
            echo $form->textFieldControlGroup($model, 'firstname');
            echo $form->textFieldControlGroup($model, 'lastname');
            echo $form->textFieldControlGroup($model, 'email', array('help' => 'On this email will be sent activation letter.'));
            echo $form->passwordFieldControlGroup($model, 'password');
            echo $form->passwordFieldControlGroup($model, 'repeatPassword');
            ?>
        </fieldset>
    
        <?php 
        echo TbHtml::formActions(array(
            TbHtml::submitButton('Register', array('color' => TbHtml::BUTTON_COLOR_PRIMARY)),
            TbHtml::resetButton('Reset'),
            TbHtml::link('Back',
                array('site/login'),
                array('class' => 'pull-right btn btn-default')
            )
        )); 
        ?>
    
    <?php $this->endWidget(); ?>
    
</div>