<?php
/**
 * @var $this CaseController
 * @var $list DocumentListTemplate
 * @var $form TbActiveForm
 * @var $documents DocumentTemplate[]
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

	<?php echo $form->errorSummary( $list ); ?>

	<fieldset>
		<legend>Case Type</legend>
		<?php echo $form->textFieldControlGroup( $list, 'title', array( 'span' => 12, 'maxlength' => 255, 'label'=>false, 'placeholder'=>'Document List Template name' ) ); ?>
	</fieldset>

	<fieldset>
		<legend>Predefined Documents</legend>

		<div data-bind="foreach: documents">
			<div class="step-panel panel panel-default">
				<div class="panel-heading">
					<span data-bind='text: "Document #"+($index()+1)'></span>
					<a class="close"
					   data-bind='attr:{id: "remove-"+$index()}, visible: $index()>0,  click: $root.removeDocument'>
						<small>&times; remove document</small>
					</a>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-sm-2 control-label required"
						       data-bind='attr:{for: "DocumentTemplate_"+$index()+"_document_name"}'>Document Name <span
								class="required">*</span></label>

						<div class="col-md-10"><input maxlength="255" controlwidthclass="col-sm-10"
						                             class="form-control"
						                             type="text"
						                             placeholder="f.e. Civil Case"
						                             data-bind='value: document_name, attr: {name: "DocumentTemplate["+$index()+"][document_name]", id: "DocumentTemplate_"+$index()+"_document_name" }'>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label required"
						       data-bind='attr:{for: "DocumentTemplate_"+$index()+"_document_link"}'>Document Link <span
								class="required">*</span></label>

						<div class="col-md-10"><input maxlength="255" controlwidthclass="col-sm-10"
						                             class="form-control"
						                             type="text"
						                             placeholder="https://www.dropbox.com/s/s7d4f65d4fs65d4fsg5/case.docx?dl=0"
						                             data-bind='value: document_link, attr: {name: "DocumentTemplate["+$index()+"][document_link]", id: "DocumentTemplate_"+$index()+"_document_link" }'>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label required"
						       data-bind='attr:{for: "DocumentTemplate_"+$index()+"_document_link_type"}'>Document Link Type<span
								class="required">*</span></label>

						<div class="col-md-10">
							<select class="form-control" data-bind='attr: {name: "DocumentTemplate["+$index()+"][document_link_type]", id: "DocumentTemplate_"+$index()+"_document_link_type" }'>
								<option value="dropbox" data-bind='attr: {selected: document_link_type=="dropbox"}'>Dropbox</option>
								<option value="gdrive" data-bind='attr: {selected: document_link_type=="gdrive"}'>Google Drive</option>
								<option value="doctini" data-bind='attr: {selected: document_link_type=="doctini"}'>Doctini</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pull-right">
			<?php
			echo TbHtml::button( 'Add Document', array(
				'color'     => TbHtml::BUTTON_COLOR_SUCCESS,
				'size'      => TbHtml::BUTTON_SIZE_XS,
				'data-bind' => 'click: addDocument',
			) );
			?>
		</div>
	</fieldset>

	<div class="form-actions">

		<?php
		echo TbHtml::link( 'Cancel', array( 'document/admin' ), array( 'style' => 'margin-right: 5em;' ) );

		echo TbHtml::submitButton( $list->isNewRecord ? 'Create' : 'Save', array(
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

	var DocumentModel = function (documents) {
		var self = this;

		self.documents = ko.observableArray(ko.utils.arrayMap(documents, function (document) {
			return {
				document_name: document.document_name,
				document_link: document.document_link,
				document_link_type: document.document_link_type
			};
		}));

		self.addDocument = function () {
			self.documents.push({
				document_name: "",
				document_link: "",
				document_link_type: "",
				enableRemove: false
			});
		};

		self.removeDocument = function (document) {
			self.documents.remove(document);
		};
	}; // function

	ko.applyBindings(new DocumentModel([
		<?php
		foreach ( $documents as $i => $document ) {
			echo json_encode(array(
				'document_name' => $document->document_name,
				'document_link' => $document->document_link,
				'document_link_type' => $document->document_link_type,
			));
			if ($i < count($documents)-1) {echo ",\n";}
		}?>
	]));
</script>