<?php
/**
 * @var $this DocumentController
 * @var DocumentTemplate[] $documents
 */

if (empty($documents)) {
	echo '<div style="text-align: center">No documents in this list</div>';
} else {
	echo '<ol style="float: right;">';
	/* @var $document DocumentTemplate */
	foreach ( (array) $documents as $document ) {
		echo sprintf( '<li><a target="_blank" href="%s" class="%s">%s</a></li>', $document->document_link, $document->document_link_type, $document->document_name );
	}
	echo '</ol>';
}