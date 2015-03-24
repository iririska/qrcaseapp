<?php
/**
 * @var integer $client_id
 */
?>
<style type="text/css" media="screen">
    html, body{
        margin:0px;
        padding:0px;
        height:100%;
        overflow:hidden;
    }

    table td, table th {
        padding:0;
    }

    #scheduler_here {
        min-height: 200px;
    }

    [class*="dhx"] {
        box-sizing: content-box !important;
        -moz-box-sizing: content-box !important;
    }

    body > .container,
    body > .container > .span-19,
    body > .container > .span-19 #content {
        height: 100%;
    }
    body > .container > .span-19 #content {
        height: calc(100% - 150px);
        border: 1px solid #c4c4c4;
        margin: 0;
        padding: 0;
    }

    .client-dropdown {
        margin: 0;
    }

    .client-dropdown .col-md-19 {
        padding: 10px;
    }

</style>

<div class="row  client-dropdown">
    <div class="col-md-19">
        <?php

        //echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r($client_id, 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>';

        $client_string =
            //CJavaScript::encode(
                Yii::app()->createUrl("calendar/view", array('c'=>'@'))
            //)
            ;

        $this->widget(
            'booster.widgets.TbSelect2',
            array(
                'asDropDownList' => true,
                'val' => $client_id,
                'name' => 'client',
                'options' => array(
                    //'tags' => array(/*'clever', 'is', 'better', 'clevertech'*/),
                    'placeholder' => 'Type the name or email of client to select',
                    'width' => '40%',
                    'allowClear' => true,
                    //'tokenSeparators' => array(',', ' ')
                ),
                'events' => array(
                    'select2-selecting' =>
'js:'.
<<<JS
    function(data) {
       document.location = "{$client_string}".replace(/%40/, data.val);
    }
JS

                ),
                'data' => Client::getMyClients('fullnamewithemail'),
                'htmlOptions' => array(
                    'multiple' => false,
                )
            )
        );

        /*$this->widget(
            'booster.widgets.TbTypeahead',
            array(
                'name' => 'demo-typeahead',
                'datasets' => array(
                    'source' => Client::getMyClients(),
                ),
                'options' => array(
                    'hint' => true,
                    'highlight' => true,
                    'minLength' => 1,
                    'valueKey' => 'id',
                    'displayKey' => 'label'
                ),
                'htmlOptions' => array(
                    'placeholder' => 'Type the name or email of client to select',
                    'style' => 'width: 100%',
                )
            )
        );*/
        ?>
    </div>
</div>

<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height: inherit'>
    <div class="dhx_cal_navline">
        <div class="dhx_cal_prev_button">&nbsp;</div>
        <div class="dhx_cal_next_button">&nbsp;</div>
        <div class="dhx_cal_today_button"></div>
        <div class="dhx_cal_date"></div>
        <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
        <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
        <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>

        <div class="dhx_cal_tab" name="year_tab" style="right:270px;"></div>
        <div class="dhx_cal_tab" name="week_agenda_tab" style="right:330px;"></div>
        <?php /*<div class="dhx_cal_tab" name="agenda_tab" style="right:280px;"></div>*/ ?>
    </div>
    <div class="dhx_cal_header">
    </div>
    <div class="dhx_cal_data">
    </div>
</div>

<script>

    function init() {
        scheduler.config.xml_date="%Y-%m-%d %H:%i";

        scheduler.locale.labels.year_tab ="Year";
        scheduler.locale.labels.agenda_tab="Agenda";

        scheduler.config.year_x = 2; //2 months in a row
        scheduler.config.year_y = 3; //3 months in a column
        scheduler.skin = "flat";
        scheduler.init('scheduler_here',new Date(<?php echo date('Y') .", ".(date('m')-1).", ".date('d');?>),"week");
        scheduler.load("<?php echo Yii::app()->createUrl("calendar/getevents", array('c'=>25))?> ", "json");

        var dp =  new dataProcessor("<?php echo Yii::app()->createUrl("calendar/getevents", array('c'=>25))?> ");
        dp.init(scheduler);

        dp.setTransactionMode("POST", false);
    }

    $(document).ready(function(){
       init();
    });
</script>