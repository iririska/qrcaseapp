<?php
/** @var $this WorkflowController
 * @var $dataProvider CActiveDataProvider
 * @var  $issues OutstandingIssues
 */

$this->breadcrumbs = array(
	'Home',
);
?>

<a href="<?php echo Yii::app()->createUrl('workflow/cases')?>" class="btn btn-primary pull-right">Expanded cases view</a>

<h2>Cases Summary</h2>

<div class="row">
	<div class="col-md-12">
		<?php $this->widget( '\TbListView', array(
			'dataProvider' => $dataProvider,
			'itemView'     => '_case_summary',
		) ); ?>
	</div>
</div>


<h2>Issues</h2>

<div class="row">
	<div class="col-md-12">
		<?php

		$this->widget( '\TbGridView', array(
			'id'                    => 'user-grid',
			'dataProvider'          => $issues->search(),
			'filter'                => null,//$model,
			'rowCssClassExpression' => '$data->status == 0 ? " danger" : ""',
			'pagerCssClass'         => 'list-pager',
			'pager'                 => array(
				'class' => '\TbPager',
			),
			'type'                  => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
			'columns'               => array(
				'id',
				'text',
				array(
					'name'   => 'client_id',
					'header' => 'Client',
					'type'   => 'raw',
					'value'  => '(!empty($data->client)) ? CHtml::link("{$data->client->firstname} {$data->client->lastname} {$data->client->email}", array("workflow/view", "id"=>(!empty($data->client->current_workflow))?$data->client->current_workflow->id:"", "c"=>(!empty($data->client->id))?$data->client->id:"")) : ""',
				),
				array(
					'name'   => 'author',
					'header' => 'Creator',
					'type'   => 'raw',
					'value'  => '$data->creator->email',
				),
				array(
					'name'   => 'status',
					'header' => 'Status',
					'type'   => 'raw',
					'value'  => '$data->status==1?"Active":"Disabled"',
				),
				array(
					'name'   => 'created',
					'header' => 'Date Created',
					'type'   => 'raw',
					'value'  => '!empty($data->created)?date(Yii::app()->params["fullDateFormat"], strtotime($data->created)):""',
				),
				array(
					'name'   => 'updated',
					'header' => 'Date Updated',
					'type'   => 'raw',
					'value'  => '!empty($data->updated)?date(Yii::app()->params["fullDateFormat"], strtotime($data->updated)):""',
				),
				/*

				'status',
				'firstname',
				'lastname',
				'phone',
				'phone2',
				'address',
				'case_type',
				*/
				array(
					'header'   => '',
					'class'    => 'bootstrap.widgets.TbButtonColumn',
					'template' => '{update} {delete}',
					//'class'=>'bootstrap.widgets.btnDropdown',
				),
			),
		) );
		/*$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'outstanding-issues-grid',
			'dataProvider'=>$model->search(),
			'filter'=>$model,
			'columns'=>array(
				'id',
				'text',
				'author',
				'status',
				'created',
				'updated',
				array(
					'class'=>'CButtonColumn',
				),
			),
		));*/

		?>
	</div>
</div>



<h2>Actions</h2>

<div class="row">
	<div class="col-md-12">
		<?php
            $this->widget('\TbGridView', array(
                'id' => 'user-grid',
                'dataProvider' => $actions->search(),
                'filter' => null,//$model,
                'rowCssClassExpression' => '$data->status == 0 ? " danger" : ""',
                'pagerCssClass' => 'list-pager',
                'pager' => array(
                    'class' => '\TbPager',
                ),
                'type' => TbHtml::GRID_TYPE_STRIPED . ' ' . TbHtml::GRID_TYPE_BORDERED . ' ' . TbHtml::GRID_TYPE_HOVER . ' ' . TbHtml::GRID_TYPE_CONDENSED,
                'columns' => array(
                    'id',
                    'text',
                    array(
                        'name' => 'client_id',
                        'header' => 'Client',
                        'type' => 'raw',
                        'value' => '(!empty($data->client)) ? CHtml::link("{$data->client->firstname} {$data->client->lastname} {$data->client->email}", array("workflow/view", "id"=>$data->client->current_workflow->id, "c"=>$data->client->id)) : ""',
                    ),
                    array(
                        'name' => 'author',
                        'header' => 'Creator',
                        'type' => 'raw',
                        'value' => '$data->creator->email',
                    ),
                    array(
                        'name' => 'status',
                        'header' => 'Status',
                        'type' => 'raw',
                        'value' => '$data->status==1?"Active":"Disabled"',
                    ),
                    array(
                        'name' => 'created',
                        'header' => 'Date Created',
                        'type' => 'raw',
                        'value' => '!empty($data->created)?date(Yii::app()->params["fullDateFormat"], strtotime($data->created)):""',
                    ),
                    array(
                        'name' => 'updated',
                        'header' => 'Date Updated',
                        'type' => 'raw',
                        'value' => '!empty($data->updated)?date(Yii::app()->params["fullDateFormat"], strtotime($data->updated)):""',
                    ),
                    /*

                    'status',
                    'firstname',
                    'lastname',
                    'phone',
                    'phone2',
                    'address',
                    'case_type',
                    */
                    array(
                        'header' => '',
                        'class' => 'bootstrap.widgets.TbButtonColumn',
                        'template' => '{update} {delete}',
                        //'class'=>'bootstrap.widgets.btnDropdown',
                    ),
                ),
            ));
		/*$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'outstanding-issues-grid',
			'dataProvider'=>$model->search(),
			'filter'=>$model,
			'columns'=>array(
				'id',
				'text',
				'author',
				'status',
				'created',
				'updated',
				array(
					'class'=>'CButtonColumn',
				),
			),
		));*/

		?>
	</div>
</div>

