<?php
/**
 * @var $this CaseController
 * @var $model WorkflowType
 * @var $form TbActiveForm
 * @var $steps WorkflowStepsByType[]
 */
?>

<div class="form">

	<?php $form = $this->beginWidget( '\TbActiveForm', array(
		'id'                   => 'case-type-form',
		'layout'               => TbHtml::FORM_LAYOUT_HORIZONTAL,
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation' => false,
	) ); ?>

	<?php echo $form->errorSummary( $model ); ?>

	<fieldset>
		<legend>Case Type</legend>
		<?php echo $form->textFieldControlGroup( $model, 'title', array( 'span' => 5, 'maxlength' => 255 ) ); ?>
		<?php echo $form->dropDownListControlGroup($model, 'document_list_template_id', CHtml::listData( DocumentListTemplate::model()->findAll(), 'id', 'title'), array( 'span' => 5, 'maxlength' => 255 ) ); ?>
	</fieldset>

	<fieldset>
		<legend>Predefined Steps</legend>
		<?php
		/*$is_first = true;
		foreach ( $steps as $i => $step ) { ?>
		<div class="step-panel panel panel-default" id="<?php echo sprintf('step-%s', $i)?>"  data-bind="foreach: steps">
			<div class="panel-heading"><?php echo sprintf('Step #%s', $i+1);?> <?php echo sprintf('<a class="close %s" id="remove-%s">&times; remove</a>', ($is_first?'hidden':''), $i); $is_first = false;  ?></div>
			<div class="panel-body">
			<?php
			echo $form->textFieldControlGroup( $step, "[$i]title", array( 'span' => 5, 'maxlength' => 255 ) );

			echo $form->textFieldControlGroup( $step, "[$i]priority", array( 'span' => 1, 'maxlength' => 255 ) );
			?>
			</div>
		</div>
		<?php
		} */
		?>
		<div data-bind="foreach: steps">
			<div class="step-panel panel panel-default">
				<div class="panel-heading">
					<span data-bind='text: "Step #"+($index()+1)'></span>
					<a class="close"
					   data-bind='attr:{id: "remove-"+$index()}, visible: $index()>0,  click: $root.removeStep'>
						<small>&times; remove step</small>
					</a>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-sm-2 control-label required"
						       data-bind='attr:{for: "WorkflowStepsByType_"+$index()+"_title"}'>Step Title <span
								class="required">*</span></label>

						<div class="col-md-5"><input maxlength="255" controlwidthclass="col-sm-10"
						                             class="form-control"
						                             type="text"
						                             data-bind='value: title, attr: {name: "WorkflowStepsByType["+$index()+"][title]", id: "WorkflowStepsByType_"+$index()+"_title" }'>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label required"
						       data-bind='attr:{for: "WorkflowStepsByType_"+$index()+"_priority"}'>Priority <span
								class="required">*</span></label>
						<div class="col-md-2 col-sm-2">
							<select class="form-control" data-bind='attr: {name: "WorkflowStepsByType["+$index()+"][priority]", id: "WorkflowStepsByType"+$index()+"_priority" }'>
								<option value="low" data-bind='attr: {selected: priority=="low"}'>Low</option>
								<option value="medium" data-bind='attr: {selected: priority=="medium"}'>Medium</option>
								<option value="high" data-bind='attr: {selected: priority=="high"}'>High</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pull-right">
			<?php
			echo TbHtml::button( 'Add Step', array(
				'color'     => TbHtml::BUTTON_COLOR_SUCCESS,
				'size'      => TbHtml::BUTTON_SIZE_XS,
				'data-bind' => 'click: addStep',
			) );
			?>
		</div>
	</fieldset>

	<div class="form-actions">

		<?php
		echo TbHtml::link( 'Cancel', array( 'case/admin' ), array( 'style' => 'margin-right: 5em;' ) );

		echo TbHtml::submitButton( $model->isNewRecord ? 'Create' : 'Save', array(
			'color' => TbHtml::BUTTON_COLOR_PRIMARY,
			'size'  => TbHtml::BUTTON_SIZE_LARGE,
		) ); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
	$(document).ready(function () {
		$('.panel-heading').on('click', 'a.close', function (e) {
			e.preventDefault();
			$(this).closest('.step-panel').hide(0, function () {
				$(this).remove();
			})
		});
	});

	var StepsModel = function (steps) {
		var self = this;

		self.steps = ko.observableArray(ko.utils.arrayMap(steps, function (step) {
			return {
				title: step.title,
				priority: step.priority
			};
		}));

		self.addStep = function () {
			self.steps.push({
				title: "",
				priority: "",
				enableRemove: false
			});
		};

		self.removeStep = function (step) {
			self.steps.remove(step);
		};
	}; // function

	ko.applyBindings(new StepsModel([
		<?php
		foreach ( $steps as $i => $step ) {
			echo json_encode(array(
				'title' => $step->title,
				'priority' => $step->priority,
			));
			if ($i < count($steps)-1) {echo ",\n";}
		}?>
	]));
</script>