<?php
/**
 * View to display note in
 * @var Document $data
 * @var string $listview_id // id of the listview to update upon deletion
 */
?>
<a href="<?php echo $data->document_link//echo Yii::app()->createUrl( 'document/download', array( 'id' => $data->id ) ) ?>" class="list-group-item" target="_blank">
    <h4 class="list-group-item-heading <?php echo "document-{$data->document_link_type}" ?>"><?php echo CHtml::encode($data->document_name ); ?></h4>
</a>

<?php
/*echo CHtml::ajaxLink( '<span class="glyphicon glyphicon-trash"></span>',
    $this->createUrl( 'workflow/documentdelete', array( 'id' => $data->id, 'ajax' => 'delete' ) ),
    array(
        'type'     => 'POST',
        'complete' => 'js:function(jqXHR, textStatus)
            {
                $.fn.yiiListView.update("'.$listview_id.'");
            }',
        //'beforeSend' => 'js:function(){return confirm("Are you sure you want to delete this document?");}',
        //'success'    => "js:function(html){ alert('removed'); }"

    ),
    array(
        'class'=>'delete',
    )

);*/

echo CHtml::link( '<span class="glyphicon glyphicon-trash"></span>',
    $this->createUrl( 'workflow/documentdelete', array( 'id' => $data->id, 'ajax' => 'delete' ) ),
    array(
        'class'=>'delete',
    )
);
?>
