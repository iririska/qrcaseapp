<?php
/*
 * ZPrepareDateBehavior will automatically convert input value of any datetime type to mysql datetime format
 * using php strtotime() function
 * e.g. 12/11/2014 -> 2014-11-12 00:00:00
*/

class ZPrepareDateBehavior extends CActiveRecordBehavior {

	/**
	 * @var array The list of attributes to be prepared
	 * example
	 * array(
	 *     'created' => 'date'
	 *     'time' => 'time'
	 * )
	 */
	public $toSave = array();


	/**
	 * @var array The list of attributes to be prepared for output
	 * example
	 * array(
	 *     'created' => 'M d, Y'
	 *     'time' => 'm/d/Y'
	 * )
	 */
	public $toOutput = array();

	public function beforeSave( $event ) {
		if (!empty($this->toSave)) {
			foreach ( $this->toSave as $_attr => $_format ) {
				try {
					$_time = strtotime($this->getOwner()->{$_attr});
					switch ($_format) {
						case 'time':
							$this->getOwner()->{$_attr} = $_time;
							break;
						case 'date':
							$this->getOwner()->{$_attr} = date('Y-m-d', $_time);
							break;
						case 'datetime':
						default:
							$this->getOwner()->{$_attr} = date('Y-m-d H:i:s', $_time);
							break;
					}

				} catch(Exception $e) {

				}
			}
		}
	}

	public function afterFind($event){

		if (!empty($this->toOutput)) {
			foreach ( $this->toOutput as $_attr => $_format ) {
				try {
					$this->getOwner()->{$_attr} = date($_format, strtotime($this->getOwner()->{$_attr}));
				} catch(Exception $e) {

				}
			}
		}
		return true;
	}

}
