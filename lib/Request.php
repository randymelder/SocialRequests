<?php
/**
 * @author Randy Melder <randymelder@gmail.com>
 * @copyright (c) 2013, RCM Software, LLC
 * @license Commercial and Proprietary - Contact author for permission.
 */

class Request {
    
    public $timecreated;
    public $myEntity;
    
    public function __construct(Entity $e = NULL) {
        $this->timecreated = time();
        if (NULL != $e) {
            $this->myEntity = new Entity($e->name, $e->namespace);
            $this->myEntity->setPropertiesWithKVP($e->properties);
        }
    }
}

class ExternalRequest extends Request {
    
    private $host_;
    private $protocol_;
    private $port_;
    private $result_;
    private $last_error_;
    private $last_error_no_;
    
    public function __construct(Entity $e = NULL) {
        parent::__construct($e);
    }
    
    public static function hostIsUp($host, $port, $timeout = 1) { 
        $fP = fSockOpen($host, $port, $this->last_error_errno, 
                        $this->last_error_, $timeout); 
        if (!$fP) { return false; }
        return true;
    }
}

define('REQUEST_TIMEOUT_SECONDS', 30);
define('DEFAULT_HTTP_PORT', 80);

class HttpExternalRequest extends ExternalRequest {
    
    private $path_;              // sub.domain.ext/controller/function/param/etc
    private $param_string_;      // arg1=nnnn&arg2=nnnn
    private $req_method_;        // GET, POST
    private $is_ssl_;
    private $headers;
    
    public function __construct(Entity $e = NULL) {
        parent::__construct($e);
        $this->req_method_  = 'GET';
        $this->result_      = '';
        $this->last_error_  = '';
        $this->port_        = DEFAULT_HTTP_PORT;
        $this->is_ssl_      = FALSE;
        $this->protocol_    = 'http';
        $this->path_        = '';
        $this->host_        = '';
    }
    
    public function init($host, $port = 80, $method = 'GET', $ssl = FALSE) {
        $this->host_         = $host;
        $this->port_         = $port;
        $this->req_method_   = $method;
        if ($ssl) 
            $this->protocol_ = 'https';
        
    }
    
    public function initWithEntity (Entity $e) {
        foreach ($e->properties AS $key=>$val)
            $this->$key = $val;
    }
    
    public function setProtocol($protocol) {
        $this->protocol_ = $protocol;
        if ("https" == strtolower($protocol)) 
            $this->is_ssl_ = TRUE;
    }
    
    
    
    /**
     * getURLWithPath() - Get a formatted URL string from init() values 
     * with the desired path appended.
     * @param string $path
     * @return string
     */
    public function getURLWithPath($path) {
        return $this->protocol_.'://'
                .$this->host_.':'
                .$this->port_.$this->path_.'?'
                .$paramstring;
        
    }
    
    /**
     * markParamStringFromKVP() - Get a URL parameter string from a key-value
     * pair array. 
     * @param mixed $kvp
     * @return string
     * @example $params = HttpExternalRequest::makeParamStringFromKVP(array('a'=>'1','b'=>'2'));
     * 
     */
    public static function makeParamStringFromKVP($kvp) {
        $params = '';
        foreach ($kvp AS $key=>$val) {
            $params .= $key.'='.$val.'&';
        }
        return rtrim($params, "&");
    }
    
    /**
     *
     * doGet() - Get the contents of $url by passing a $paramstring.
     * 
     * @param string $url
     * @param string $paramstring
     * @return string
     * 
     * @example $res = HttpExternalRequest::doGet('http://here.com/','a=1&b=2');
     * 
     */
    public static function doGet($url, $paramstring = NULL) {
        return file_get_contents($url.'?'.$paramstring);
    }
    
    /**
     * doPost() - Get the contents of $url by passing a $paramstring.
     * @param string $url
     * @param string $paramstring
     * @param array $arrOpts An array of CURL_OPT settings.
     * @return string
     */
    public static function doPost($url, $paramstring = NULL, $arrOpts = NULL) {
        $resource = curl_init();
        
        curl_setopt($resource, CURLOPT_USERAGENT, __CLASS__.' 1.0');
        curl_setopt($resource, CURLOPT_CONNECTTIMEOUT, REQUEST_TIMEOUT_SECONDS);
        curl_setopt($resource, CURLOPT_TIMEOUT, REQUEST_TIMEOUT_SECONDS);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, TRUE);
        if (!is_array($arrOpts)) {
            // ???
        } else {
            foreach ($arrOpts AS $key=>$val) {
                curl_setopt($resource, $key, $val);
            }
        }
        
        curl_setopt($resource, CURLOPT_URL, $url);
        curl_setopt($resource, CURLOPT_POST, TRUE);
        curl_setopt($resource, CURLOPT_POSTFIELDS, $paramstring);
        
        $result = curl_exec($resource);
        curl_close($resource);
        return $result;
    }
}
