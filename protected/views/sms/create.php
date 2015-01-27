<?php
/**
 * @var $this SmsController
 * @var $model Sms
 */

$this->breadcrumbs=array(
	'Sms'=>array('index'),
	'Create',
);
?>

<h1>Send Text (SMS)</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>