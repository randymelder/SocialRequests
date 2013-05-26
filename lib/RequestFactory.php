<?php

/**
 * @author Randy Melder <randymelder@gmail.com>
 * @copyright (c) 2013, RCM Software, LLC
 * @license Commercial and Proprietary - Contact author for permission.
 */

require_once 'Entity.php';
require_once 'SocialRequests.php';

class SocialRequestFactory {
    public static function doMakeRequest(Entity $e) {
        $c = ucfirst($e->name)."Request";
        if (class_exists($c)) {
            return new $c($e);
        } else {
            throw new Exception("Library ".$c."::".$c." cannot be found.");
        }
    }
}

class SocialRequestAdapter {
    
    public static function doBroadcastStatusUpdates (array $arr) {
        foreach ($arr as $entity) {
            $obj = SocialRequestFactory::doMakeRequest($entity);
            $obj->doSocialPost();
        }
    }
}
