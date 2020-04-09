<?php
namespace Assemble\l5xero\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class XeroOAuthController extends Controller
{
    /**
     * Handle incoming webhooks
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    { 
        $provider = new \Calcinai\OAuth2\Client\Provider\Xero([
            'clientId'          => config('xero.oauth.client_id'),
            'clientSecret'      => config('xero.oauth.client_secret'),
            'redirectUri'       => config('xero.oauth.redirect_uri'),
        ]);
        
        if ($request->has('code')) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => 'openid email profile accounting.transactions'
            ]);

            $request->session()->put('oauth2state',$provider->getState());
            return redirect()->away($url);

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (!$request->has('state') || ($request->get('state') !== $request->session()->get('oauth2state'))) {

            $request->session()->forget('oauth2state');
            exit('Invalid state');

        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);


            //If you added the openid/profile scopes you can access the authorizing user's identity.
            $identity = $provider->getResourceOwner($token);
            print_r($identity);

            //Get the tenants that this user is authorized to access
            $tenants = $provider->getTenants($token);
            print_r($tenants);
        }
    }


}