<?php
/* @var $this WorkflowController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
	'Clients',
);
?>
<fieldset>
    <legend>Cases</legend>

        <div class="row">
            <div class="col-md-12" style="margin-bottom: 15px;">
                <?php
                echo TbHtml::linkButton( 'Add Client', array(
                    'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                    'size'  => TbHtml::BUTTON_SIZE_LARGE,
                    'class' => 'pull-right'
                ) );
                ?>
            </div>
        </div>

    <div class="row">
        <div class="col-md-12">
            <?php $this->widget( '\TbListView', array(
                'dataProvider' => $dataProvider,
                'itemView'     => '_view',
            ) ); ?>
        </div>
    </div>
</fieldset>