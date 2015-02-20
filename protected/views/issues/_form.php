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
        <fieldset>

            <p class="note">Fields with <span class="required">*</span> are required.</p>

            <div class="form-group">
                <label class="col-sm-2 control-label required" for=<?php echo $model->getAttributeLabel('client_id');?>"><?php echo $model->getAttributeLabel('client_id');?><span class="required">*</span></label>
                <div class="col-md-9">
                    <?php
                    $this->widget(
                        'booster.widgets.TbSelect2',
                        array(
                            'asDropDownList' => true,
                            'model' => $model,
                            'attribute' => 'client_id',
                            'form' => $form,
                            'options' => array(
                                //'tags' => array(/*'clever', 'is', 'better', 'clevertech'*/),
                                'placeholder' => 'Type the name or email of client to select',
                                'width' => '100%',
                                'allowClear' => true,
                                //'tokenSeparators' => array(',', ' ')
                            ),
                            'data' => Client::getMyClients(),
                            'htmlOptions' => array(
                                'multiple' => false,
                            )
                        )
                    );
                    ?>
                </div>
            </div>

            <?php
            echo $form->textAreaControlGroup( $model, 'text', array( 'span' => 9 ) );
            ?>
        </fieldset>
        
        <?php 
        echo TbHtml::formActions(array(
            TbHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('color' => TbHtml::BUTTON_COLOR_PRIMARY)),
            TbHtml::link('Cancel',
                array('issues/admin'),
                array('class' => 'btn btn-default')
            )
        )); 
       
        $this->endWidget(); ?>

</div><!-- form -->