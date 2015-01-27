<?php
/**
 * @var $this Controller
 * @var Client $data
 */

$qr_image = $this->getQRImage(
    Yii::app()->createAbsoluteUrl( "workflow/view", array(
        "id" => $data->current_workflow->id,
        "c"  => $data->id
    ) )
);
?>
<!--						Client info        -->

<address style="position: relative">
	<div class="text-center qr-image">
		<img class="small to-enlarge" src="<?php echo $qr_image?>">
        <a class="qr-print glyphicon glyphicon-print" href="<?php echo Yii::app()->createUrl('qr/print', array("id" => $data->current_workflow->id, "c"  => $data->id));?>" target="_qr" title="Click for printable version"></a>
	</div>
	<strong>Email:</strong> <?php echo $data->email; ?><br>
	<strong>Address:</strong> <?php echo $data->address; ?><br>
	<abbr title="Phone"><strong>Phone:</strong></abbr> <?php echo $data->phone; ?><br>
	<abbr title="Phone"><strong>Phone #2:</strong></abbr> <?php echo $data->phone2; ?><br>
</address>