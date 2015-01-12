<?php
/**
 * @var $this QRController
 * @var TbForm $form
 */
?>

<?php
$this->breadcrumbs = array(
	'Workflow Types' => array( 'index' ),
	'Create',
);
?>

<h1>Generate QR-code</h1>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'qr-generator-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation' => false,
	) ); ?>

	<div class="row">
		<div class="col-md-8">
			<?php
			echo \TbHtml::textAreaControlGroup( 'data', '', array('rows'=>4, 'id'=>'qr-code-data') );
			?>
			<p class="help-block">Enter your data into the field above. After submitting your QR code will apear on the right side</p>

			<div class="form-actions">

				<?php
				echo TbHtml::submitButton( 'Generate', array(
					'color' => TbHtml::BUTTON_COLOR_PRIMARY,
					'size'  => TbHtml::BUTTON_SIZE_LARGE,
					'id'    => 'qr-generator-submit',
					'data-loading-text' => "Generating...",
				) ); ?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="help-block text-center" style="margin: 3em 0" id="qr-image-container">Generated QR-code image</div>
					<input class="form-control" type="text" id="qr-image-url" placeholder="URL of the generated QR-code image" style="width: 100%" onClick="this.select();">
				</div>
			</div>
		</div>
	</div>



	<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
	$('#qr-generator-form').on('submit', function () {
		var frm = $('#qr-generator-form');
		var btn = $('#qr-generator-submit');
		if ( $('#qr-code-data').val() == '') {
			showError(
				'Data cannot be empty',
				'#qr-code-data'
			);
			return false;
		}
		$.ajax({
			url: frm.attr('action'),
			data: frm.serialize(),
			type: 'POST', // 'POST' | 'GET'
			dataType: 'json', //xml, html, script, json, jsonp, text
			cache: false,
			beforeSend: function (xhr) {
				btn.button('loading');
			}
		}).done(function (data, textStatus, jqXHR) {
			switch (data.status) {
				case 'success':
					$('#qr-image-container').html(data.image);
					$('#qr-image-url').val(data.url);
					break;
				case 'error':
					showError(
						data.message,
						'#qr-code-data'
					)
					break;
				default:
					break;
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {

		}).always(function (jqXHR, textStatus, errorThrown) {
			btn.button('reset');
		});

		return false;
	});

	function showError(message, insertafter) {
		clearTimeout();
		$(insertafter).before(
			'<div id="qr-code-error" class="alert alert-danger alert-dismissible" role="alert">\
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
				message+
			'</div>'
		);
		setTimeout(function(){
			$('#qr-code-error').slideUp(300, function(){
				$(this).remove();
			})
		}, 2000);
	}

</script>