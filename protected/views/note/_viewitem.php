<?php
/**
 * View to display note in
 * @var ClientNote|Note $data
 * @var string $type 'clientnote'|'note'
 */
?>
<a href="<?php echo Yii::app()->createUrl( 'note/view', array( 'id' => $data->id, 'type'=>$type ) ) ?>" class="list-group-item item-<?php echo CHtml::encode($type)?>">
    <h4 class="list-group-item-heading"><?php echo date( Yii::app()->params['fullDateFormat'], strtotime( $data->created ) ) ?></h4>

    <p class="list-group-item-text"><?php echo Yii::app()->format->truncateText( $data->content, array( '' ) ) ?></p>
</a>