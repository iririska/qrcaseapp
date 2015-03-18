<?php

return array(
	// True if objects should be returned by the service classes.
	// False if associative arrays should be returned (default behavior).
	'use_objects' => false,

	// The application_name is included in the User-Agent HTTP header.
	'application_name' => 'ODESK CRM',

    //service app
    'service_acc_client_id' => '180333236740-g7cq40idglikjnumfg65310ujuesk653.apps.googleusercontent.com',
    'service_acc_email' => '180333236740-g7cq40idglikjnumfg65310ujuesk653@developer.gserviceaccount.com',
    'service_acc_key' => file_get_contents( dirname(__FILE__) . "/google_service_acc_key_77680b6167eed83e2d9652f7214a8c881f65e1b0_privatekey.p12" ),
    
    
    //Client ID for web application
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => '',
);