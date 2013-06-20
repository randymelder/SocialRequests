<?php
		require_once 'lib/facebook/facebook.php';
		$message = date("F j, Y, g:i a")." Lucky Numbers: 1 2 3 4 5 6";
		 $fb     = new Facebook(array(
		  'appId'   => 'xxxxxxxxxxxxxx',
		  'secret'  => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
		  //'cookie' => true ,
		  'scope' => 'manage_pages',
		));
            try {
              $apiurl = '/fbpageidhere/feed/';
			  $res = $fb->api($apiurl,'post',array('message' => $message));
			  return $res;
            } catch (FacebookApiException $e) {
              error_log($e);
			  return FALSE;
            }
?>
