<?php
//
// This page adapted from Facebook example code.
//


/**
* Copyright 2011 Facebook, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may
* not use this file except in compliance with the License. You may obtain
* a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations
* under the License.
*/

require_once 'lib/facebook/facebook.php';

//http://stackoverflow.com/questions/4432426/which-facebook-permissions-allow-for-posting-to-a-page-wall-not-profile-wall
//$fbPermissions = 'publish_stream,publish_actions,manage_pages,status_update';

// Create our Application instance (replace this with your appId and secret).
$facebook   = new Facebook(array(
  'appId'   => 'xxxxxxxxxxxxxxxxxxxxxxx',
  'secret'  => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
  'cookie' => true ,
  'scope' => 'manage_pages'
));



// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile 	= $facebook->api('/me');
	$user_accounts 	= $facebook->api('/me/accounts');
  } catch (FacebookApiException $e) {
    print_r($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl(array('scope'=>$fbPermissions));
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Plotto.Me Facebook Authorization</title>
</head>

<body>
<?php if ($user): ?>
<h3>You</h3>
<img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

<h3>Your User Object (/me)</h3>
<pre><?php print_r($user_profile);  print_r($user_accounts); ?></pre>
<p>
<a href="<?php echo $logoutUrl; ?>">Logout</a></p>
<?php else: ?>
<div>
<strong><em>You are not Connected.</em></strong>
<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
</div>
<?php endif ?>

<a href="/fb/push_to_fb.php">Push to Facebook</a>

</body>
</html>
