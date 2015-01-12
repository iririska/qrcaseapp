<?php
/* @var $this UserController
 * @var  array $users
 * @var  array $clients
 * @var  array $assignments
 */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array( 'admin' ),
	'Assign Users to Clients',
);

Yii::app()->clientScript->registerCssFile( 'css/bootstrap.vertical-tabs.css' );
?>
	<h1>Assign Users to Clients</h1>

	<p class="help-block">
		Select client tab, check users checkboxes and click `Assign` button
	</p>

<?php if(Yii::app()->user->hasFlash('assign')) { ?>

	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<?php echo Yii::app()->user->getFlash('assign'); ?>
	</div>
	<script>
		setTimeout(function(){
			$('.alert').slideUp(function(){ $(this).remove()});
		}, 3000);
		clearTimeout();
	</script>

<?php } ?>

<?php $form = $this->beginWidget( '\TbActiveForm', array(
	'id'                   => 'user-form',
	'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation' => false,
) );
?>

	<div class="row">
		<div class="col-xs-3"> <!-- required for floating -->
			<!-- Nav tabs -->
			<ul class="nav nav-tabs tabs-left">
				<?php
				$active = true;
				foreach ( $clients as $client_id => $client_name ) {
					?>
					<li class="<?php echo $active ? 'active' : ''; ?>">
						<a href="#<?php echo "client-$client_id"; ?>" data-toggle="tab"><?php echo $client_name; ?></a>
					</li>
					<?php
					$active = false;
				} ?>
			</ul>
		</div>

		<div class="col-xs-9">
			<!-- Tab panes -->
			<div class="tab-content">
				<?php
				$active = true;
				foreach ( $clients as $client_id => $client_name ) {
					?>
					<div class="tab-pane <?php echo $active ? 'active' : ''; ?>" id="<?php echo "client-$client_id"; ?>">
						<?php
						echo sprintf( '<h4>Assign users to <em>%s</em></h4>', CHtml::encode( $client_name ) );
						?>
						<div>
							<div class="btn-group">
								<button type="button" class="btn btn-default check_users" data-cid="<?php echo $client_id?>" data-type="toggle">Toggle</button>
								<button type="button" class="btn btn-default check_users" data-cid="<?php echo $client_id;?>" data-type="check">Select All</button>
								<button type="button" class="btn btn-default check_users" data-cid="<?php echo $client_id;?>" data-type="uncheck">Deselect All</button>
							</div>
						</div>
						<?php

						echo '<br>'.CHtml::checkBoxList( "ClientUser[$client_id]", $assignments[$client_id], $users );
						?>
					</div>
					<?php
					$active = false;
				} ?>
			</div>
		</div>

	</div>
	<hr>
	<div class="col-md-5">
		<?php
		echo TbHtml::link('Reset', array('user/assign'), array('style'=>'margin-right: 5em;'));

		echo TbHtml::submitButton( 'Assign', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) );

		?>
	</div>

<?php $this->endWidget(); ?>

<script>
	$('.check_users').on('click', function(e){
		e.preventDefault();
		el = $(this);
		$(':checkbox[name*="ClientUser['+el.data('cid')+']"]').each(function(){
			switch (el.data('type')) {
				case 'check': $(this).attr('checked', true); break;
				case 'uncheck': $(this).attr('checked', false); break;
				case 'toggle':
				default: 	$(this).attr('checked', !$(this).attr('checked')); break;
			}
		});
	});
</script>