<?php
/**
 * @var $this Controller
 * @var Client $data
 */

?>
<!--						Client info        -->

<address style="position: relative">
	<div class="text-center qr-image">
		<img class="small to-enlarge" title="click to enlarge" src="<?php echo $this->getQRImage(
			Yii::app()->createAbsoluteUrl( "workflow/view", array(
				"id" => $data->current_workflow->id,
				"c"  => $data->id
			) )
		);?>">
	</div>
	<strong>Email:</strong> <?php echo $data->email; ?><br>
	<strong>Address:</strong> <?php echo $data->address; ?><br>
	<abbr title="Phone"><strong>Phone:</strong></abbr> <?php echo $data->phone; ?><br>
	<abbr title="Phone"><strong>Phone #2:</strong></abbr> <?php echo $data->phone2; ?><br>
</address>

<?php ob_start(); ?>
	<!-- QR popup -->
	<img id="qr-popup"/>
	<form action=""></form>
	<input type="text" name>
	<!-- QR popup END -->
<?php $_html = ob_get_clean();
$_html = preg_replace('/\n+/', "", $_html);


Yii::app()->clientScript->registerScript('enlarge-qr',
	<<<SCRIPT
$('.to-enlarge').on('click', function(){
	$(".bs-example-modal-lg .modal-title").html('QR code');
	$(".bs-example-modal-lg .modal-body").html('$_html');
	$('#qr-popup').attr('src', $(this).attr('src')).css({display:'block',margin:'2em auto'});
	$(".bs-example-modal-lg").modal('show');
});
SCRIPT
	,
	CClientScript::POS_END

	);