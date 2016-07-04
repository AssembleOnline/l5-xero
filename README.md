# Eloquent Model Searcher for Laravel 5

## Installation

Add this line to your `providers` array:
``` php
Assemble\XeroIntegration\XeroIntegrationServiceProvider::class,
```

Add this line to your `aliases` array:
``` php
'Xero' => Assemble\XeroIntegration\Facades\XeroIntegrationer::class,
```

You will need to run `php artisan vendor:publish` to publish the config file to your instalation,
Once run, you can find it in `config/eloquenet_search.php`.
This config file is used to controll which models are used to search/return entities of.


## Configuration

The config file can be found in the laravel config folder at `config/xero.php`,
here you can define the classes related to your models as below.
``` php

```


### Additional Feature:

``` php

```


