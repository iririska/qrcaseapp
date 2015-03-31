<?php

/**
 * This is the model class for table "CalendarEvent".
 *
 * The followings are the available columns in table 'CalendarEvent':
 * @property integer $id
 * @property string $title
 * @property string $summary
 * @property string $description
 * @property string $color
 * @property string $start
 * @property string $end
 * @property string $eventDate
 * @property string $location
 * @property string $client_id
 * @property string $step_id
 * @property string $google_calendar_id
 * @property string $google_calendar_event_id
 * @property string $created
 * @property string $updated
 */
class CalendarEvent extends CActiveRecord
{
	//public $client_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'CalendarEvent';
	}

	public function behaviors(){
		return array(
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
			array('title, client_id', 'required'),
			array('title, summary, location', 'length', 'max'=>255),
			array('color', 'length', 'max'=>50),
			array('description, start, end, eventDate, updated, client_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, summary, client_id, step_id, description, color, start, end, eventDate, location, created, updated', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'summary' => 'Summary',
			'description' => 'Description',
			'color' => 'Color',
			'start' => 'Start',
			'end' => 'End',
			'eventDate' => 'Event Date',
			'location' => 'Location',
			'created' => 'Created',
			'updated' => 'Updated',
            'client_id' => 'client_id',
            'step_id' => 'step_id'
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('eventDate',$this->eventDate,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
        $criteria->compare('client_id',$this->client_id,true);     
        $criteria->compare('step_id',$this->step_id,true);             

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CalendarEvent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}    
    
    /*
     * method add new event to Calendar
     * $gClient = Google_Client() with all params
     * $google_calendar_id - ****@group.calendar.google.com
     * return google_calendar_event_id or false
     */
    public function createEvent($gClient, $google_calendar_id){
        if(empty($this->google_calendar_id))
            $this->google_calendar_id = $google_calendar_id;
        $service = new Google_Service_Calendar($gClient);
        
        $event = new Google_Service_Calendar_Event();
        $event->setSummary( $this->title );
        $event->setLocation( $this->location );
        $event->setDescription( $this->description );

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime( date('c', strtotime($this->start)) ); //$start->setDateTime('2011-06-03T10:00:00.000-07:00');
        $start->setTimeZone( 'America/Los_Angeles' );
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        if (!empty($this->end))
            $end->setDateTime( date('c', strtotime($this->end)) ); //$end->setDateTime('2011-06-03T10:25:00.000-07:00');
        else
            $end->setDateTime( date('c', strtotime($this->start)+24*3600) );
        $end->setTimeZone( 'America/Los_Angeles' );
        $event->setEnd( $end );

        //$attendee1 = new Google_Service_Calendar_EventAttendee();
        //$attendee1->setEmail('attendeeEmail');
        // ...
       // $attendees = array($attendee1,
                           // ...
        //                  );
        //$event->attendees = $attendees;

        $createdEvent = $service->events->insert($this->google_calendar_id, $event);
        if($id = $createdEvent->getId()){
            $this->google_calendar_event_id = $id;
            if($this->save())
                return $id;
        }
        return false;
    }
}
