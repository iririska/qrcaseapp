<?php
/**
 * @var $this QRController
 * @var string $image
 * @var Client $client
 */

?>
<div class="print-qr-page">

    <div class="row">
        <div class="col-md-1 text-right no-print pull-right"><a class="print-btn-lg" id="js-qr-print" onclick="print();"><span class="glyphicon glyphicon-print"></span></a></div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center print-qr-image">
            <?php echo CHtml::image($image, 'QR code'); ?>
        </div>

        <div class="col-md-12 text-center">
            <input type="text" class="text-center form-control" placeholder="Enter file number here">
        </div>

    </div>

    <hr>

    <div class="row">
        <div class="col-md-12 text-center"><h3>Client: <?php echo $client->email?> &nbsp;&nbsp;&nbsp; T: <?php echo $client->phone?></h3></div>
    </div>

</div>