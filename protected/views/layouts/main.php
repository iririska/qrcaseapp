<?php
/* @var $this Controller
 * @var array $breadcrumbs
 * @var string $content
 */

//Yii::app()->bootstrap->bootstrapPath = Yii::getPathOfAlias( 'application.vendor.yiistrap' );
Yii::app()->bootstrap->register();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="favicon.ico">

	<title><?php echo CHtml::encode( $this->pageTitle ); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl();?>/css/crm.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl();?>/css/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl();?>/css/bootstrap.vertical-tabs.css" media="screen" />



</head>

<body>

<?php
/*this->widget( 'bootstrap.widgets.TbNavbar', array(
	'brandLabel' => CHtml::encode( Yii::app()->name ),
	'display'    => null, // default is static to top
	
) );*/
if (!Yii::app()->user->isGuest) {
	$this->widget( '\TbNavbar', array(
		'brandLabel'    => Yii::app()->name,
		'color'         => TbHtml::NAVBAR_COLOR_INVERSE,
		'display'       => null, // default is static to top
		'collapse'      => true,
		'items'         => array(
            array(
				'class' => '\TbNav',
				'encodeLabel' => false,
				'items' =>
					array(
                        array( 'label' => 'Home', 'url' => array( '/' ) ),
                        array( 'label' => 'Administration',
                            'items' => array(
                                array( 'label' => 'Manage Clients', 'url' => array( '/client/admin' ),'visible' => !Yii::app()->user->checkAccess('superadmin')),
                                array( 'label' => 'Manage Users', 'url' => array( '/user/admin' ) ),
                                array( 'label' => 'Grant Access', 'url' => array( '/user/assign' ) ),
                                '<li class="divider"></li>',
                                array( 'label' => 'Manage Case Types', 'url' => array( '/case/admin' ) ),
                                array( 'label' => 'Manage Document List Templates', 'url' => array( '/document/admin' ) ),
                            ),
                            'visible' => Yii::app()->user->checkAccess('admin'), //TODO visible for admin only
                        ),

                        array( 'label'   => 'Clients',
                               'url'     => array( '/client/admin' ),
                               'visible' => !Yii::app()->user->checkAccess('admin')
                        ),

                        array( 'label'   => 'Calendar',
                               'url'     => array( '/calendar/view' ),
                               'visible' => !Yii::app()->user->checkAccess('superadmin') && Yii::app()->user->checkAccess('admin')
                        ),
                        array(
                            'label' => "Issues and Actions <sup><span class='badge label-".( ($this->issues == 0 && $this->attorney_actions == 0)? '':'danger')."'>{$this->issues}/{$this->attorney_actions}</span></sup>",
                            'items' => array(
                                array(
                                'label'       => "Outstanding Issues <sup><span class='badge label-" . ( $this->issues == 0 ? '' : 'danger' ) . "'>{$this->issues}</span></sup>",
                                    'url'         => array( '/issues/admin' ),
                                    'encodeLabel' => false,
                                    'visible'     => Yii::app()->user->checkAccess('admin')
                                ),
                                array(
                                'label'       => "Attorney Required Actions <sup><span class='badge label-" . ( $this->attorney_actions == 0 ? '' : 'danger' ) . "'>{$this->attorney_actions}</span></sup>",
                                    'url'         => array( '/attorneyactions/admin' ),
                                    'encodeLabel' => false,
                                    'visible'     => Yii::app()->user->checkAccess('admin')
                                )
                            ),
                        ),

                        /*array( 'label'   => 'QR codes',
                               'url'     => array( '/qr/generator' ),
                               'visible' => ! Yii::app()->user->isGuest
                        ),*/

                        array( 'label'   => 'Login',
                               'url'     => array( '/site/login' ),
                               'visible' => Yii::app()->user->isGuest
                        ),

                        array(
                            'label'   => 'Logout (' . Yii::app()->user->name . ')',
                            'url'     => array( '/site/logout' ),
                            'visible' => ! Yii::app()->user->isGuest
                        )
                    ),
                ),
            ),
        )
    );
}

?>
<!--/.nav-collapse -->

<div class="container">
     <?php 
        foreach(Yii::app()->user->getFlashes() as $key => $message) {?>
            <div class="alert alert-<?php echo $key; ?> in alert-block fade">
                <a href="#" class="close" data-dismiss="alert" type="button">x</a>
                <?php echo $message; ?>
            </div>
    <?php }?>
	<!-- Main component for a primary marketing message or call to action -->
	<?php echo $content; ?>

</div>
<!-- /container -->


</body>
</html>