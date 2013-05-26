<?php
require_once 'lib/Request.php';
require_once 'lib/Entity.php';
require_once 'lib/SocialRequests.php';
require_once 'lib/RequestFactory.php';

require_once 'lib/OAuth.php';
// require_once 'lib/twitteroauth.php';

define("EXAMPLE_1", FALSE);
define("EXAMPLE_2", FALSE);
define("EXAMPLE_3", FALSE);
define("EXAMPLE_4", FALSE);
define("EXAMPLE_5", TRUE);

$consumerKey        = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$consumerSecret     = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$accessToken        = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$accessTokenSecret  = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$twitterapiurl      = 'https://api.twitter.com/1/statuses/update.json';

// Simple get
if (EXAMPLE_1) {
    echo    "/////////////////// 1 ///////////////////\n";
    $url    = "http://randymelder.com/test/request.php";
    $params = HttpExternalRequest::makeParamStringFromKVP(array("year"=>"2013"));
    echo HttpExternalRequest::doGet($url, $params);
    echo "\n";
}

// Simple post
if (EXAMPLE_2) {
    echo    "/////////////////// 2 ///////////////////\n";
    $url    = "http://randymelder.com/test/request.php";
    $kvp    = array("user_login"=>"fakeuser", 
                    "password"=>"fakepass",
                    "redirect_to"=>"http://yahoo.com/");
    $params = HttpExternalRequest::makeParamStringFromKVP($kvp);
    echo HttpExternalRequest::doGet($url, $params);
    echo "\n";
}

if (EXAMPLE_3) {
    echo    "/////////////////// 3 - Twitter Request ///////////////////\n";
    $status_msg         = "The Adapter pattern seems like it should be more common.";
    echo "Updating Twitter status to: ".$status_msg."\n";
    
    $params             = array('status' =>$status_msg);
    
    $tweetEntity        = new Entity('twitter', 'oauth');
    $aProps             = array('consumer_key'=>$consumerKey, 
                            'consumer_secret'=>$consumerSecret,
                            'oauth_token'=>$accessToken,
                            'oauth_token_secret'=>$accessTokenSecret,
                            'api_url'=>$twitterapiurl,
                            'api_params'=>$params);
    $tweetEntity->setPropertiesWithKVP($aProps);
    
    $oReq               = new TwitterRequest($tweetEntity);
    
    print_r($oReq->doPost($twitterapiurl, $params));
    print_r($oReq->curl_headers);
    
}

if (EXAMPLE_4) {
    echo    "/////////////////// 4 - Fake Req ///////////////////\n";
    $status_msg         = "All the Faker API examples seem half baked. Just me?";
    echo "Updating Faker status to: ".$status_msg."\n";
    
    $params             = array('status' =>$status_msg);
    
    $url                = "http://randymelder.com/feed";
    $fakerEntity        = new Entity('faker', 'socialrequest');
    $aProps             = array('token'=>"abcdefg987654321",
                            'api_url'=>$url,
                            'api_params'=>$params);
    $fakerEntity->setPropertiesWithKVP($aProps);
    
    $tObj               = SocialRequestFactory::doMakeRequest($fakerEntity);
    $tObj->doSocialPost();
    print_r($tObj);
}

if (EXAMPLE_5) {
    echo    "/////////////////// 5 - Adapter Req ///////////////////\n";
    $status_msg         = "It seems like the adapter pattern should be more common.";
    echo "Updating Faker status to: ".$status_msg."\n";
    
    $params             = array('status' =>$status_msg);
    
    //////////////////////////////////////
    // Twitter
    $tweetEntity        = new Entity('twitter', 'oauth');
    $aProps             = array('consumer_key'=>$consumerKey, 
                            'consumer_secret'=>$consumerSecret,
                            'oauth_token'=>$accessToken,
                            'oauth_token_secret'=>$accessTokenSecret,
                            'api_url'=>$twitterapiurl,
                            'api_params'=>$params);
    $tweetEntity->setPropertiesWithKVP($aProps);
    
    /////////////////////////////////////
    // Facebook
    $fbEntity        = new Entity('facebook', 'socialrequest');
    $aProps             = array('api_url'=>$twitterapiurl,
                            'api_paramstring'=>'status='.$status_msg);
    $fbEntity->setPropertiesWithKVP($aProps);
    
    /////////////////////////////////////
    // Faker
    $fakerEntity        = new Entity('faker', 'oauth');
    $aProps             = array('token'=>"abcdefg987654321");
    $fakerEntity->setPropertiesWithKVP($aProps);
    
    SocialRequestAdapter::doBroadcastStatusUpdates(array($tweetEntity, $fbEntity, $fakerEntity));
    
    print_r($tObj);
}
