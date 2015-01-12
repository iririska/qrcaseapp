<?php

/**
 * This is the model class for table "Workflow".
 *
 * The followings are the available columns in table 'Workflow':
 * @property integer $id
 * @property integer $client_id
 * @property integer $case_type
 *
 * The followings are the available model relations:
 * @property Step[] $steps
 * @property WorkflowType $caseType
 * @property User $client
 */
class Workflow extends CActiveRecord {

	public function behaviors() {
		return array(
			'CTimestampBehavior' => array(
				'class'           => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'updated',
			)
		);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'Workflow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array( 'client_id, case_type', 'required' ),
			array( 'client_id, case_type', 'numerical', 'integerOnly' => true ),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array( 'id, client_id, case_type', 'safe', 'on' => 'search' ),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'steps'     => array( self::HAS_MANY, 'Step', 'workflow_id', 'order' => 'priority DESC' ),
			'documents' => array( self::HAS_MANY, 'Document', 'workflow_id', 'order' => 'document_name DESC' ),
			'caseType'  => array( self::BELONGS_TO, 'WorkflowType', 'case_type' ),
			'client'    => array( self::BELONGS_TO, 'Client', 'client_id' ),
			'notes'     => array( self::HAS_MANY, 'Note', 'step_id', 'through' => 'steps' ),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id'        => 'ID',
			'client_id' => 'Client',
			'case_type' => 'Case Type',
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
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare( 'id', $this->id );
		$criteria->compare( 'client_id', $this->client_id );
		$criteria->compare( 'case_type', $this->case_type );

		return new CActiveDataProvider( $this, array(
			'criteria' => $criteria,
		) );
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return Workflow the static model class
	 */
	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function afterSave() {
		/* @var Step[] $steps */
		$steps = WorkflowStepsByType::model()->findAll( "case_type = :case", array( ':case' => $this->case_type ) );
		if ( ! empty( $steps ) ) {
			$i = 0;
			foreach ( $steps as $template_step ) {

				$_step              = new Step();
				$_step->attributes  = $template_step->attributes;
				$_step->workflow_id = $this->id;
				$_step->date_start  = date( 'Y-m-d H:i:s', strtotime( "+$i week" ) ); // 1 week between start and end of step
				$i ++;
				$_step->date_end = date( 'Y-m-d H:i:s', strtotime( "+$i week" ) );
				$_step->created  = date( 'Y-m-d H:i:s' );
				$_step->updated  = date( 'Y-m-d H:i:s' );
				$_step->save( false ); // do not run validation for step model as this is a stub and we already provided all predefined data
			}
		}

		/* @var WorkflowType $workflow_template */
		$workflow_template = WorkflowType::model()->findByPk( $this->case_type );

		if ( ! empty( $workflow_template->document_list_template_id ) ) {
			$documents = DocumentTemplate::model()->findAll( "document_list_id = :document_list", array( ':document_list' => $workflow_template->document_list_template_id ) );

			if ( ! empty( $documents ) ) {
				foreach ( $documents as $document_template ) {
					$_document              = new Document();
					$_document->attributes  = $document_template->attributes;
					$_document->workflow_id = $this->id;
					$_document->created     = date( 'Y-m-d H:i:s' );
					$_document->updated     = date( 'Y-m-d H:i:s' );
					$_document->save( false ); // do not run validation for step model as this is a stub and we already provided all predefined data
				}
			}
		}

		parent::afterSave();
	}

	public function getOverallProgress( $with_steps = false ) {
		$count    = 0;
		$progress = 0;
		$steps    = array();
		foreach ( $this->steps as $step ) {
			$progress += (int) $step->progress;
			$count ++;
			$steps[ $step->id ] = (int) $step->progress;
		}

		if ( $with_steps ) {
			$result = array(
				'total' => ( $count > 0 ) ? floor( $progress / $count ) : 0,
				'steps' => $steps,
			);
		} else {
			$result = ( $count > 0 ) ? floor( $progress / $count ) : 0;
		}

		return $result;


	}

	public function defaultScope() {
		if ( Yii::app()->user->getIsAdmin() ) {
			return array(//'condition' => "role='abiturients'"
			);
		} else {
			return array(
				'with' => 'client'
			);
		}
	}

	public function getDates() {
		return Yii::app()->db->createCommand( "SELECT min(date_start) as date_start, max(date_end) as date_end FROM Step WHERE workflow_id=:wid" )->queryRow( true, array( ':wid' => $this->id ) );
	}

	public function getDocumentsProvider() {
		return new CActiveDataProvider( 'Document',
			array(
				'criteria' => array(
					'condition' => " workflow_id=:workflow_id ",
					'params'    => array( ':workflow_id' => $this->id ),
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
}