<?php
/* @var $this SiteController
 * @var $error array
 * @var $code string
 */

$this->pageTitle   = Yii::app()->name . ' - Error';
$this->breadcrumbs = array(
	'Error',
);

// if this is custom error e.g. custom-400
// then 1) $code = 400
//      2) do note encode message
// otherwise - encode $message as Yii does note prepare $message on built-in exceptions
if ( strpos( $code, 'custom-' ) !== false ) {
	$code = str_replace('custom-','', $code);
} else {
	$message = CHtml::encode($message);
}
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
	<?php
	echo $message;
	?>
</div>