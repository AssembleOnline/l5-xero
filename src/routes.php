<?php
// if config set
$action = config('xero.webhook.action');
if(!isset($action)) {
    // default if not
    $action = '\Assemble\l5xero\Controllers\XeroWebhookController@handle';
}

// register webhook route
Route::post('api/xero/webhook', [
    'middleware'    => \Assemble\l5xero\Middleware\XeroWebhookMiddleware::class,
    'uses'        => $action
]);