# Xero Wrapper for Laravel 5

Laravel 5 wrapper for calcinai/xero-php
All credit where its due to calcinai/xero-php contributors for the awesome OO implementation.

## Installation

Add this line to your `providers` array:
``` php
Assemble\l5xero\XeroServiceProvider::class,
```

Add this line to your `aliases` array:
``` php
'Xero' => Assemble\l5xero\Xero::class,
```

You will need to run `php artisan vendor:publish` to publish the config file to your instalation,
Once run, you can find it in `config/xero.php`.


## Configuration

The config file can be found in the laravel config folder at `config/xero.php`
``` php
'oauth' => [

        'callback'          => 'oob',
        'consumer_key'      => 'YOURCONSUMERKEY',
        'consumer_secret'   => 'YOURSECRETKEY',
        'rsa_private_key'  	=> 'privatekey.pem',
        'rsa_public_key'   	=> 'publickey.cer'
    ]
```
