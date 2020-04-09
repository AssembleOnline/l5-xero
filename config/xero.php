<?php
/** 
* Config file to store the oauth and settings for xero functionality.
 **/
return [

    'oauth' => [

        'callback'          => 'oob',
        'consumer_key'      => 'YOURCONSUMERKEY',
        'consumer_secret'   => 'YOURSECRETKEY',
        'rsa_private_key'  	=> 'privatekey.pem',
        'rsa_public_key'   	=> 'publickey.cer',

        'client_id'         => 'CLIENTID',
        'client_secret'     => 'CLIENTSECRET',
    ],

    'prefix' => 'lfivexero_',

    'rate_limits' => [
        [60, 60],
        [5000, 86400],
    ],
    'rate_limit_cache_key' => 'lFiveXero_rateLimitHistory',

    'application_type'	=> 'private',


    'webhook' => [
        'signing_key' => 'YOURWEBHOOKKEY',
        'action'    =>  '\Assemble\l5xero\Controllers\XeroWebhookController@handle',
    ]
];