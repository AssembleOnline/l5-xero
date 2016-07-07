<?php
/** 
* Config file to store the oauth and settings for xero functionality.
 **/
return [

    'oauth' => [

        'callback'          => 'oob',
        'consumer_key'      => 'YOURCONSUMERKEY',
        'consumer_secret'   => 'YOURSECRETKEY',
        'rsa_private_key'  	=> 'file:///home/absolutepath/vendor/src/drawmyattention/xerolaravel/Certificates/privatekey.pem',
        'rsa_public_key'   	=> 'file:///home/absolutepath/vendor/src/drawmyattention/xerolaravel/Certificates/publickey.cer'
    ]


];