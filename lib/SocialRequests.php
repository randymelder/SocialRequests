<?php

/**
 * @author Randy Melder <randymelder@gmail.com>
 * @copyright (c) 2013, RCM Software, LLC
 * @license Commercial and Proprietary - Contact author for permission.
 */

require_once 'Request.php';
require_once 'Entity.php';
require_once 'OAuth.php';
// require_once 'twitteroath.php';

define('TWITTER_BASE_URI', 'api.twitter.com/1/');
define('PROTOCOL_HTTP', 'http');
define('PROTOCOL_HTTPS', 'https');
define('REQUEST_METHOD_POST', "POST");

class OAuthSignedRequest extends HttpExternalRequest {
    
    private $sha1_method;
    private $oauth_consumer;
    private $oauth_token;
    public  $curl_headers;
    
    public function __construct(Entity $e = NULL) {
        parent::__construct($e);
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->setProtocol(PROTOCOL_HTTPS);
    }
    
    public function setOAuthFromEntity(Entity $e) {
        $this->oauth_consumer = new OAuthConsumer($e->properties->consumer_key, $e->properties->consumer_secret);
        if (!empty($e->properties->oauth_token) && !empty($e->properties->oauth_token_secret)) {
            $this->oauth_token = new OAuthConsumer($e->properties->oauth_token, $e->properties->oauth_token_secret);
        } else {
            $this->oauth_token = NULL;
        }
    }
    
    public function doOAuthPost($serviceurl, $paramstring, $method = REQUEST_METHOD_POST) {
        $request = OAuthRequest::from_consumer_and_token($this->oauth_consumer, 
                                                         $this->oauth_token, 
                                                         $method, 
                                                         $serviceurl, 
                                                         $paramstring);
        $request->sign_request($this->sha1_method, $this->oauth_consumer, $this->oauth_token);
        $arrOpts = array(CURLOPT_HTTPHEADER => array('Expect:'),
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HEADERFUNCTION => array($this, 'getHeader'),
            CURLOPT_HEADER => FALSE);
        
        return HttpExternalRequest::doPost($request->get_normalized_http_url(), 
                                           $request->to_postdata(),
                                           $arrOpts);
    }
    
    public function getHeader($chandle, $header) {
        $this->curl_headers = $header;
        return $header;
    }
    
}

class TwitterRequest extends OAuthSignedRequest {
    
    public function __construct(Entity $e = NULL) {
        parent::__construct($e);
    }
    
    public function doSocialPost() {
        $this->setOAuthFromEntity($this->myEntity);
        $this->doOAuthPost($this->myEntity->properties->api_url, $this->myEntity->properties->api_params, REQUEST_METHOD_POST);
    }
}

require_once 'facebook/facebook.php';
/**
 * Facebook is a beast unto itself. You are dependent on the lib:
 * https://github.com/facebook/facebook-php-sdk
 */
class FacebookRequest extends HttpExternalRequest {
	
	const FB_PERMISSIONS = 'publish_stream';
    
    // http://stackoverflow.com/questions/9575782/facebook-api-news-feed-with-wall-posts
    /**
     * Required parameters for entity include:
     * - api_key
     * - api_secret
     * @param Entity $e
     */
    public function __construct(Entity $e = NULL) {
        parent::__construct($e);
    }
    
    /**
     * Wrapper for Facebook wall post
     * - 
     * @return void
	 * @example 
	   $fbEntity        = new Entity('facebook', 'socialrequest');
       $aProps          = array('fb_api_id'=>$fbAppId,
                                'fb_api_secret'=>$fbSecretKey,
								'fb_status_msg'=>$status_msg,
								'fb_username'=>'someusername');
	   $fbEntity->setPropertiesWithKVP($aProps);
	   $fbReq           = new FacebookRequest($fbEntity);
	   $result          = $fbReq->doSocialPost();				
     */
    public function doSocialPost() {
		
        $fb     = new Facebook(array('appId'  => $this->myEntity->properties->fb_api_id, 
									 'secret' => $this->myEntity->properties->fb_api_secret,
									 'scope'  => FacebookRequest::FB_PERMISSIONS));
            try {
			  $apiurl = '/'.$this->myEntity->properties->fb_username.'/feed/';
			  $res = $fb->api($apiurl,'post',array('message' => $this->myEntity->properties->fb_status_msg));
			  return $res;
            } catch (FacebookApiException $e) {
              error_log($e);
			  return FALSE;
            }
        
    }
}


