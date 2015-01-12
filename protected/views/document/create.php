<?php
/**
 * @var $this DocumentController
 * @var $model OutstandingIssues
 * @var $list DocumentListTemplate
 * @var $documents DocumentTemplate
 */


$this->breadcrumbs = array(
	'Document Templates' => array( 'admin' ),
	'Create',
);
?>

	<h1>Create Document List template</h1>

<?php $this->renderPartial( '_form',
	array(
		'list'      => $list,
		'documents' => $documents,
	)
);
