<?php

/**
 * CFormatter extended with extra formatting routines
 * Class ZFormatter
 */
class ZFormatter extends CFormatter {

	public $truncateTextFormat = array('length' => 50, 'etc' => ' ...', 'charset'=>'UTF-8', 'break_words' => false, 'middle' => false);

	/**
	 *
	 * Text formatter shortening long texts and displaying the full text
	 * as the span title.
	 *
	 * To be used in GridViews for instance.
	 *
	 * @param string $value
	 *
	 * @return string  Encoded and possibly html formatted string ('span' if the text is long).
	 */
	public function formatShortText( $value ) {
		if ( strlen( $value ) > $this->shortTextLimit ) {
			$retval = CHtml::tag( 'span', array( 'title' => $value ), CHtml::encode( mb_substr( $value, 0, $this->shortTextLimit - 3, Yii::app()->charset ) . '...' ) );
		} else {
			$retval = CHtml::encode( $value );
		}

		return $retval;
	}

	public function formatTruncateText($string){

		if ($this->truncateTextFormat['length'] == 0) return '';
		$length = $this->truncateTextFormat['length'];

		if (mb_strlen($string) > $length) {
			$length -= min($length, mb_strlen($this->truncateTextFormat['etc']));
			if (!$this->truncateTextFormat['break_words'] && !$this->truncateTextFormat['middle']) {
				$string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length+1, $this->truncateTextFormat['charset']));
			}
			if(!$this->truncateTextFormat['middle']) {
				return mb_substr($string, 0, $length, $this->truncateTextFormat['charset']) . $this->truncateTextFormat['etc'];
			} else {
				return mb_substr($string, 0, $length/2, $this->truncateTextFormat['charset']) . $this->truncateTextFormat['etc'] . mb_substr($string, -$length/2, (mb_strlen($string)-$length/2), $this->truncateTextFormat['charset']);
			}
		} else {
			return $string;
		}
	}
}