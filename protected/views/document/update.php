<?php
/**
 * @var $this DocumentController
 * @var $model OutstandingIssues
 * @var $list DocumentListTemplate
 * @var $documents DocumentTemplate
 */


$this->breadcrumbs = array(
	'Document Templates' => array( 'admin' ),
	'Update ' . CHtml::encode($list->title),
);
?>

	<h1>Modify Document List `<?php echo CHtml::encode($list->title)?>`</h1>

<?php $this->renderPartial( '_form',
	array(
		'list'      => $list,
		'documents' => $documents,
	)
);
