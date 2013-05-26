<?php
/**
 * @author Randy Melder <randymelder@gmail.com>
 * @copyright (c) 2013, RCM Software, LLC
 * @license Commercial and Proprietary - Contact author for permission.
 */

class Entity {
    public $name;
    public $namespace;
    public $properties;
    
    public function __construct($name, $namespace = NULL) {
        $this->name         = $name;
        $this->namespace    = $namespace; 
    }
    
    public function getContext() {
        return $this->namespace.'.'.$this->name;
    }
    
    public function getProperties() {
        return $this->properties;
    }
    
    public function getProperty($key) {
        if (isset($this->properties->$key))
            return $this->properties->$key;
        else
            return false;
    }
    
    public function setProperty($key, $val) {
        $this->properties->$key = $val;
        return true;
    }
    
    public function setPropertiesWithKVP($kvp) {
        foreach ($kvp AS $key=>$val) {
            $this->setProperty($key, $val);
        }
        return true;
    }
    
    public function setPropertiesWithJSON(string $json) {
        $kvp = json_decode($json);
        return $this->setPropertiesWithKVP($kvp);
    }
}




