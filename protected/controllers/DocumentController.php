<?php

class DocumentController extends Controller {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters() {
		//echo '<pre style="color:red; text-align:left; background:white; white-space: pre-wrap">' . print_r(Yii::getPathOfAlias('booster.filters'), 1).'</pre><small>'.__FILE__.': '.__LINE__.'</small>'; exit;

		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
			array( 'booster.filters.BoosterFilter - delete' )
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array(
				'allow', // admin permissions
				'actions' => array(
					'create',
					'update',
					'admin',
					'view',
					'delete',
					'updatepassword',
					'assign',
					'listdocuments',
					'add'
				),
				'roles'   => array( 'admin' ),
			),
			array(
				'allow', // all logged users
				'actions' => array( 'create', 'update', 'updatepassword', 'add' ),
				'roles'   => array( 'user' ),
			),
			array(
				'deny',  // deny all users
				'users' => array( '*' ),
			),

		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {

		Yii::app()->clientScript->registerScriptFile( Yii::app()->getAssetManager()->publish( Yii::getPathOfAlias( 'application.vendor' ) . '/knockoutjs/knockout-3.2.0.js' ), CClientScript::POS_HEAD );

		$list = new DocumentListTemplate();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$documents = $this->getItemsToUpdate();

		if ( isset( $_POST['DocumentListTemplate'] ) ) {
			$list->attributes = $_POST['DocumentListTemplate'];

			if ( $list->validate() ) {
				/* @var DocumentTemplate[] $documents */
				if ( isset( $_POST['DocumentTemplate'] ) ) {
					$valid = true;
					foreach ( $documents as $i => $document ) {
						if ( isset( $_POST['DocumentTemplate'][ $i ] ) ) {
							$document->attributes = $_POST['DocumentTemplate'][ $i ];
						}
						$valid = $document->validate( array( 'title' ) ) && $valid;

					}

					if ( $valid ) {

						// Case Type if valid as well as all steps are valid
						// Then we save Case Type and all corresponding steps
						if ( $list->save() ) {
							foreach ( $documents as $i => $document ) {
								$document->document_list_id = $list->id;
								$document->save();
							}

							$this->redirect( array( 'document/admin' ) );
						}

					}
				}
			}
		}

		$this->render( 'create', array(
			'list'      => $list,
			'documents' => $documents,
		) );
	}

	/**
	 * @param string $for_action
	 * @param null DocumentListTemplate $model
	 *
	 * @return array
	 */
	public function getItemsToUpdate( $for_action = 'create', $model = null ) {
		// Create an empty list of records
		$items = array();

		// Iterate over each item from the submitted form
		if ( isset( $_POST['DocumentTemplate'] ) && is_array( $_POST['DocumentTemplate'] ) ) {
			foreach ( $_POST['DocumentTemplate'] as $item ) {
				// If item id is available, read the record from database
				if ( array_key_exists( 'id', $item ) ) {
					$items[] = DocumentTemplate::model()->findByPk( $item['id'] );
				} // Otherwise create a new record
				else {
					$items[] = new DocumentTemplate();
				}
			}
		} else {
			if ( $for_action == 'create' ) {
				$items[] = new DocumentTemplate();
			} else {
				$items = $model->documentTemplates;
			}
		}

		return $items;
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate( $id ) {

		Yii::app()->clientScript->registerScriptFile( Yii::app()->getAssetManager()->publish( Yii::getPathOfAlias( 'application.vendor' ) . '/knockoutjs/knockout-3.2.0.js' ), CClientScript::POS_HEAD );

		$list      = $this->loadModel( $id );
		$documents = $this->getItemsToUpdate( 'update', $list );

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset( $_POST['DocumentListTemplate'] ) ) {

			$list->attributes = $_POST['DocumentListTemplate'];

			if ( $list->validate() ) {
				/* @var DocumentTemplate[] $documents */
				if ( isset( $_POST['DocumentTemplate'] ) ) {
					$valid = true;
					foreach ( $documents as $i => $document ) {
						if ( isset( $_POST['DocumentTemplate'][ $i ] ) ) {
							$document->attributes = $_POST['DocumentTemplate'][ $i ];
						}
						$valid = $document->validate( array( 'title' ) ) && $valid;

					}

					if ( $valid ) {
						// Case Type if valid as well as all steps are valid
						// Then we save Case Type and all corresponding steps
						if ( $list->save() ) {
							DocumentTemplate::model()->deleteAll( 'document_list_id=:list_id', array( ':list_id' => $list->id ) );
							foreach ( $documents as $i => $document ) {
								$document->document_list_id = $list->id;
								$document->save();
							}

							$this->redirect( array( 'document/admin' ) );
						}

					}
				}
			}
		}

		$this->render( 'update', array(
			'list'      => $list,
			'documents' => $documents,
		) );
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete( $id ) {
		$this->loadModel( $id )->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( ! isset( $_GET['ajax'] ) ) {
			$this->redirect( isset( $_POST['returnUrl'] ) ? $_POST['returnUrl'] : array( 'admin' ) );
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin() {
		$model = new DocumentListTemplate( 'search' );
		$model->unsetAttributes();  // clear any default values
		if ( isset( $_GET['DocumentListTemplate'] ) ) {
			$model->attributes = $_GET['DocumentListTemplate'];
		}

		$this->render( 'admin', array(
			'model' => $model,
		) );
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return DocumentListTemplate the loaded model
	 * @throws CHttpException
	 */
	public function loadModel( $id ) {
		$model = DocumentListTemplate::model()->findByPk( $id );
		if ( $model === null ) {
			throw new CHttpException( 404, 'The requested page does not exist.' );
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param DocumentListTemplate $model the model to be validated
	 */
	protected function performAjaxValidation( $model ) {
		if ( isset( $_POST['ajax'] ) && $_POST['ajax'] === 'outstanding-issues-form' ) {
			echo CActiveForm::validate( $model );
			Yii::app()->end();
		}
	}

	public function actionListDocuments( $id ) {
		$this->renderPartial( '_documentlist', array( 'documents' => DocumentListTemplate::model()->findByPk( $id )->documentTemplates ) );
	}

	/**
	 * Method for adding document to existing workflow
	 * @var integer $wid workflow_id
	 * @throws CHttpException
	 */
	public function actionAdd( $wid ) {
		$model = new Document( 'addtostep' );

		if ( empty( $wid ) ) {
			throw new CHttpException( '400', 'Invalid request. Please do not repeat this request again.' );
		}

		$model->workflow_id = (int) $wid;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset( $_POST['Document'] ) ) {
			$model->attributes = $_POST['Document'];
			if ( $model->save() ) {
				if ( Yii::app()->request->isAjaxRequest ) {
					echo CJSON::encode( array(
						'status'  => 'success',
						'message' => 'Document successfully added',
						//'debug' => $model,
					) );
					Yii::app()->end();
				} else {
					$this->redirect( array(
						'workflow/view',
						'id' => $model->workflow_id,
						'c'  => $model->workflow->client_id
					) );
				}
			}
		}

		/*if (Yii::app()->request->isAjaxRequest) {
			$cs=Yii::app()->clientScript;
			$cs->scriptMap=array(
				'jquery.js'=>false,
			);
			$this->renderPartial('add_document',
				array(
					'model'=>$model,
				),
				false,
				true
				);
		} else {
			$this->render('add_document',array(
				'model'=>$model,
			));
		}*/
		if ( Yii::app()->request->isAjaxRequest ) {
			$cs            = Yii::app()->clientScript;
			$cs->scriptMap = array(
				'jquery.js' => false,
			);
			echo CJSON::encode(
				array(
					'heading' => 'Add new document',
					'content' => $this->renderPartial( '_add', array(
						'model' => $model,
					),
						true,
						true
					)
				)
			);
		} else {
			$this->render( 'add_document', array(
				'model' => $model,
			) );
		}
	}
}