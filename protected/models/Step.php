<?php

/**
 * This is the model class for table "Step".
 *
 * The followings are the available columns in table 'Step':
 * @property integer $id
 * @property integer $workflow_id
 * @property integer $priority
 * @property string $title
 * @property string $description
 * @property string $progress
 * @property string $date_start
 * @property string $date_end
 * @property integer $status
 * @property string $created
 *
 * The followings are the available model relations:
 * @property Note[] $notes
 * @property Workflow $workflow
 */
class Step extends CActiveRecord
{
	const COMPLETED = 4; //should be equal to the index of `Completed` status
	const ATTORNEYATTN = 5; //should be equal to the index of `Attorney Attn` status

	public $progressSetup = array(
		array(
			'name' => 'Not started',
			'color' => '',
			'progress' => ''
		),
		array(
			'name' => 'Started',
			'color' => 'bg-info',
			'progress' => 'info'
		),
		array(
			'name' => 'In progress',
	        'color' => 'bg-warning',
	        'progress' => 'warning'
        ),
		array(
			'name' => 'Problem',
			'color' => 'bg-danger',
			'progress' => 'danger'
		),
		array(
			'name' => 'Completed',
			'color' => 'bg-success',
			'progress' => 'success',
		),
		array(
			'name' => 'Attorney Attn',
			'color' => 'bg-primary',
			'progress' => 'primary',
		),
	);

	public static $prioritySetup = array(
		array(
			'name' => 'None',
		),
		array(
			'name' => 'Low',
		),
		array(
			'name' => 'Medium',
		),
		array(
			'name' => 'High',
		),
	);
    
    public $getColor = array(
        'none' => '#9FC6E7',
        'low' => '#7BD148',
        'medium' => '#FFAD46',
        'high' => '#FA573C'
    );

        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Step';
	}

	public function behaviors(){
		return array(
			'ZPrepareDateBehavior' => array(
				'class' => 'application.extensions.behaviors.ZPrepareDateBehavior',
				'toSave' => array(
					'date_start' => 'datetime',
					'date_end' => 'datetime',

				),
				'toOutput' => array(
					'date_start' => 'm/d/Y H:i:s',
					'date_end' => 'm/d/Y H:i:s',
					'created' => 'm/d/Y H:i:s',
					'updated' => 'm/d/Y H:i:s',
				),
			),

			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'updated',
			)
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('workflow_id, title, date_start, date_end', 'required'),
			array('workflow_id, status, progress', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('priority', 'in', 'range'=>self::getPriorityEnum()),
			array('id, workflow_id, priority, title, date_start, date_end, status, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'notes' => array(self::HAS_MANY, 'Note', 'step_id'),
			'workflow' => array(self::BELONGS_TO, 'Workflow', 'workflow_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'workflow_id' => 'Workflow',
			'priority' => 'Priority',
			'title' => 'Title',
			'description' => 'Description',
			'progress' => 'Progress',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
			'status' => 'Status',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('workflow_id',$this->workflow_id);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('progress',$this->progress,true);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Step the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeValidate() {
		if (!empty($this->created)) 
            $this->created = date('Y-m-d H:i:s', strtotime($this->created));
		return parent::beforeValidate();
	}


	public function getStatusColor(){
		return $this->progressSetup[$this->status]['color'];
	}

	public function getStatusName(){
		return $this->progressSetup[$this->status]['name'];
	}

	public function getStatusProgress(){
		return $this->progressSetup[$this->status]['progress'];
	}

	public function getPriorityForList(){
		$_priorities = array();
		foreach (self::$prioritySetup as $i=>$_priority ) {
			$_priorities[ mb_strtolower($_priority['name']) ] = $_priority['name'];
		}
		return $_priorities;
	}

	public static function getPriorityEnum(){
		$_priorities = array();
		foreach (self::$prioritySetup as $i=>$_priority ) {
			$_priorities[$i] = mb_convert_case($_priority['name'], MB_CASE_LOWER);
		}
		return $_priorities;
	}


	public function getStatusForList(){
		$_statuses = array();
		foreach ($this->progressSetup as $i=>$_status ) {
			$_statuses[$i] = $_status['name'];
		}
		return $_statuses;
	}

	public function getNotesProvider(){
		return new CActiveDataProvider( 'Note',
			array(
				'criteria'      => array(
					'condition' => " step_id=:step_id ",
					'params' => array(':step_id'=>$this->id),
					'order'     => 'created DESC',
					//'with'      => array( 'author' ),
				),
				/*'countCriteria' => array(
					'condition' => " client_id in ('". implode("', '", $_clients_ids) ."')",
					// 'order' and 'with' clauses have no meaning for the count query
				),
				'pagination'    => array(
					'pageSize' => 1,
				),*/
			)
		);
	}

	public function markCompleted(){
		$this->status = self::COMPLETED;
		$this->progress = 100;
		$this->saveAttributes(array('status', 'progress'));
		return true;
	}

	/**
	 * get integer index of priority, e.g. 'Low' => 1
	*/
	public function getPriorityIndex(){
		$_priorities = self::getPriorityEnum();
		foreach ( self::$prioritySetup as $i=>$_p ) {
			if (mb_strtolower($_p['name']) == $this->priority) return $i;
		}
	}

	/**
	 * get integer index of priority, e.g. 'Low' => 1
	*/
	public function getPriorityString($index, $lowercase=false){
		$_priorities = self::$prioritySetup;
		if (empty($_priorities[$index]['name'])) $index = 0;
		return ( $lowercase ) ? mb_strtolower( $_priorities[$index]['name'] ) : $_priorities[ $index ]['name'];
	}
    
    /*
     * method create new event and inser to calendarEvent table
     * return event object(calendarEvent) or false;
     */
    public function createNewEvent($googl_cal_id, $client_id){

        $event = new CalendarEvent();
        
        $event->title = $this->title;
        $event->google_calendar_id = $googl_cal_id;
        $event->google_calendar_event_id = '';
        $event->summary = $this->title;
        $event->client_id = $client_id;
        $event->description = '';
        $event->color = $this->getColor[$this->priority];
        $event->start = date( 'Y-m-d H:i:s', strtotime($this->date_start));
        $event->end = date( 'Y-m-d H:i:s', strtotime($this->date_end));
        
        if($event->save()){
            return $event;
        }
        return false;
    }

}
