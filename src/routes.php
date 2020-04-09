<?php
// if config set
$action = config('xero.webhook.action');

$oAuthRoute = config('xero.oauth.redirect_uri');
$oAuthAction = config('xero.oauth.redirect_uri_action');

if(!isset($action)) {
    // default if not
    $action = '\Assemble\l5xero\Controllers\XeroWebhookController@handle';
}

if(!isset($oAuthRoute)) {
    // default if not
    $oAuthRoute = 'api/xero/webhook';
}

if(!isset($oAuthAction)) {
    // default if not
    $oAuthRoute = 'api/xero/webhook';
}

// register webhook route
Route::post('api/xero/webhook', [
    'middleware'    => \Assemble\l5xero\Middleware\XeroWebhookMiddleware::class,
    'uses'        => $action
]);

// register webhook route
Route::get($oAuthRoute, [
    'uses'        => $oAuthAction
]);