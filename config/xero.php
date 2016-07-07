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
        'rsa_public_key'   	=> 'publickey.cer'
    ]


];