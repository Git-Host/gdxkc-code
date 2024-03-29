<?php 

date_default_timezone_set('Asia/Chongqing');



/* Load OAuth lib. You can find it at http://oauth.net */ 
/** 
 * @ignore 
 */ 
class OAuthException extends Exception { 
    // pass 
} 

/** 
 * @ignore 
 */ 
class OAuthConsumer { 
    public $key; 
    public $secret; 

    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    function __toString() { 
        return "OAuthConsumer[key=$this->key,secret=$this->secret]"; 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthToken { 
    // access tokens and request tokens 
    public $key; 
    public $secret; 

    /** 
     * key = the token 
     * secret = the token secret 
     */ 
    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    /** 
     * generates the basic string serialization of a token that a server 
     * would respond to request_token and access_token calls with 
     */ 
    function to_string() { 
        return "oauth_token=" . 
            OAuthUtil::urlencode_rfc3986($this->key) . 
            "&oauth_token_secret=" . 
            OAuthUtil::urlencode_rfc3986($this->secret); 
    } 

    function __toString() { 
        return $this->to_string(); 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthSignatureMethod { 
    public function check_signature(&$request, $consumer, $token, $signature) { 
        $built = $this->build_signature($request, $consumer, $token); 
        return $built == $signature; 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod { 
    function get_name() { 
        return "HMAC-SHA1"; 
    } 

    public function build_signature($request, $consumer, $token) { 
        $base_string = $request->get_signature_base_string(); 
	
        $request->base_string = $base_string; 

        $key_parts = array( 
            $consumer->secret, 
            ($token) ? $token->secret : "" 
        ); 
		
		

		$key_parts = OAuthUtil::urlencode_rfc3986($key_parts); 

		

		$key = implode('&', $key_parts); 
        return base64_encode(hash_hmac('sha1', $base_string, $key, true)); 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthSignatureMethod_PLAINTEXT extends OAuthSignatureMethod { 
    public function get_name() { 
        return "PLAINTEXT"; 
    } 

    public function build_signature($request, $consumer, $token) { 
        $sig = array( 
            OAuthUtil::urlencode_rfc3986($consumer->secret) 
        ); 

        if ($token) { 
            array_push($sig, OAuthUtil::urlencode_rfc3986($token->secret)); 
        } else { 
            array_push($sig, ''); 
        } 

        $raw = implode("&", $sig); 
        // for debug purposes 
        $request->base_string = $raw; 

        return OAuthUtil::urlencode_rfc3986($raw); 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethod { 
    public function get_name() { 
        return "RSA-SHA1"; 
    } 

    protected function fetch_public_cert(&$request) { 
        // not implemented yet, ideas are: 
        // (1) do a lookup in a table of trusted certs keyed off of consumer 
        // (2) fetch via http using a url provided by the requester 
        // (3) some sort of specific discovery code based on request 
        // 
        // either way should return a string representation of the certificate 
        throw Exception("fetch_public_cert not implemented"); 
    } 

    protected function fetch_private_cert(&$request) { 
        // not implemented yet, ideas are: 
        // (1) do a lookup in a table of trusted certs keyed off of consumer 
        // 
        // either way should return a string representation of the certificate 
        throw Exception("fetch_private_cert not implemented"); 
    } 

    public function build_signature(&$request, $consumer, $token) { 
        $base_string = $request->get_signature_base_string(); 
        $request->base_string = $base_string; 

        // Fetch the private key cert based on the request 
        $cert = $this->fetch_private_cert($request); 

        // Pull the private key ID from the certificate 
        $privatekeyid = openssl_get_privatekey($cert); 

        // Sign using the key 
        $ok = openssl_sign($base_string, $signature, $privatekeyid); 

        // Release the key resource 
        openssl_free_key($privatekeyid); 

        return base64_encode($signature); 
    } 

    public function check_signature(&$request, $consumer, $token, $signature) { 
        $decoded_sig = base64_decode($signature); 

        $base_string = $request->get_signature_base_string(); 

        // Fetch the public key cert based on the request 
        $cert = $this->fetch_public_cert($request); 

        // Pull the public key ID from the certificate 
        $publickeyid = openssl_get_publickey($cert); 

        // Check the computed signature against the one passed in the query 
        $ok = openssl_verify($base_string, $decoded_sig, $publickeyid); 

        // Release the key resource 
        openssl_free_key($publickeyid); 

        return $ok == 1; 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthRequest { 
    private $parameters; 
    private $http_method; 
    private $http_url; 
    // for debug purposes 
    public $base_string; 
    public static $version = '1.0'; 
    public static $POST_INPUT = 'php://input'; 

    function __construct($http_method, $http_url, $parameters=NULL) { 
        @$parameters or $parameters = array(); 
        $this->parameters = $parameters; 
        $this->http_method = $http_method; 
        $this->http_url = $http_url; 
    } 


    /** 
     * attempt to build up a request from what was passed to the server 
     */ 
    public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) { 
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") 
            ? 'http' 
            : 'https'; 
        @$http_url or $http_url = $scheme . 
            '://' . $_SERVER['HTTP_HOST'] . 
            ':' . 
            $_SERVER['SERVER_PORT'] . 
            $_SERVER['REQUEST_URI']; 
        @$http_method or $http_method = $_SERVER['REQUEST_METHOD']; 

        // We weren't handed any parameters, so let's find the ones relevant to 
        // this request. 
        // If you run XML-RPC or similar you should use this to provide your own 
        // parsed parameter-list 
        if (!$parameters) { 
            // Find request headers 
            $request_headers = OAuthUtil::get_headers(); 

            // Parse the query-string to find GET parameters 
            $parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']); 

            // It's a POST request of the proper content-type, so parse POST 
            // parameters and add those overriding any duplicates from GET 
            if ($http_method == "POST" 
                && @strstr($request_headers["Content-Type"], 
                    "application/x-www-form-urlencoded") 
            ) { 
                $post_data = OAuthUtil::parse_parameters( 
                    file_get_contents(self::$POST_INPUT) 
                ); 
                $parameters = array_merge($parameters, $post_data); 
            } 

            // We have a Authorization-header with OAuth data. Parse the header 
            // and add those overriding any duplicates from GET or POST 
            if (@substr($request_headers['Authorization'], 0, 6) == "OAuth ") { 
                $header_parameters = OAuthUtil::split_header( 
                    $request_headers['Authorization'] 
                ); 
                $parameters = array_merge($parameters, $header_parameters); 
            } 

        } 

        return new OAuthRequest($http_method, $http_url, $parameters); 
    } 

    /** 
     * pretty much a helper function to set up the request 
     */ 
    public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters=NULL) { 
        @$parameters or $parameters = array(); 
        $defaults = array("oauth_version" => OAuthRequest::$version, 
            "oauth_nonce" => OAuthRequest::generate_nonce(), 
            "oauth_timestamp" => OAuthRequest::generate_timestamp(), 
            "oauth_consumer_key" => $consumer->key); 
        if ($token) 
            $defaults['oauth_token'] = $token->key; 

        $parameters = array_merge($defaults, $parameters); 

        return new OAuthRequest($http_method, $http_url, $parameters); 
    } 

    public function set_parameter($name, $value, $allow_duplicates = true) { 
        if ($allow_duplicates && isset($this->parameters[$name])) { 
            // We have already added parameter(s) with this name, so add to the list 
            if (is_scalar($this->parameters[$name])) { 
                // This is the first duplicate, so transform scalar (string) 
                // into an array so we can add the duplicates 
                $this->parameters[$name] = array($this->parameters[$name]); 
            } 

            $this->parameters[$name][] = $value; 
        } else { 
            $this->parameters[$name] = $value; 
        } 
    } 

    public function get_parameter($name) { 
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null; 
    } 

    public function get_parameters() { 
        return $this->parameters; 
    } 

    public function unset_parameter($name) { 
        unset($this->parameters[$name]); 
    } 

    /** 
     * The request parameters, sorted and concatenated into a normalized string. 
     * @return string 
     */ 
    public function get_signable_parameters() { 
        // Grab all parameters 
        $params = $this->parameters; 
        
        // remove pic 
        if (isset($params['pic'])) { 
            unset($params['pic']); 
        }
        
          if (isset($params['image'])) 
         { 
            unset($params['image']); 
        }

        // Remove oauth_signature if present 
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.") 
        if (isset($params['oauth_signature'])) { 
            unset($params['oauth_signature']); 
        } 

        return OAuthUtil::build_http_query($params); 
    } 

    /** 
     * Returns the base string of this request 
     * 
     * The base string defined as the method, the url 
     * and the parameters (normalized), each urlencoded 
     * and the concated with &. 
     */ 
    public function get_signature_base_string() { 
        $parts = array( 
            $this->get_normalized_http_method(), 
            $this->get_normalized_http_url(), 
            $this->get_signable_parameters() 
        ); 
        
        //print_r( $parts );

        $parts = OAuthUtil::urlencode_rfc3986($parts); 
        return implode('&', $parts); 
    } 

    /** 
     * just uppercases the http method 
     */ 
    public function get_normalized_http_method() { 
        return strtoupper($this->http_method); 
    } 

    /** 
     * parses the url and rebuilds it to be 
     * scheme://host/path 
     */ 
    public function get_normalized_http_url() { 
        $parts = parse_url($this->http_url); 

        $port = @$parts['port']; 
        $scheme = $parts['scheme']; 
        $host = $parts['host']; 
        $path = @$parts['path']; 

        $port or $port = ($scheme == 'https') ? '443' : '80'; 

        if (($scheme == 'https' && $port != '443') 
            || ($scheme == 'http' && $port != '80')) { 
                $host = "$host:$port"; 
            } 
        return "$scheme://$host$path"; 
    } 

    /** 
     * builds a url usable for a GET request 
     */ 
    public function to_url() { 
        $post_data = $this->to_postdata(); 
        $out = $this->get_normalized_http_url(); 
        if ($post_data) { 
            $out .= '?'.$post_data; 
        } 
        return $out; 
    } 

    /** 
     * builds the data one would send in a POST request 
     */ 
    public function to_postdata( $multi = false ) {
    //echo "multi=" . $multi . '`';
    if( $multi )
    	return OAuthUtil::build_http_query_multi($this->parameters); 
    else 
        return OAuthUtil::build_http_query($this->parameters); 
    } 

    /** 
     * builds the Authorization: header 
     */ 
    public function to_header() { 
        $out ='Authorization: OAuth realm=""'; 
        $total = array(); 
        foreach ($this->parameters as $k => $v) { 
            if (substr($k, 0, 5) != "oauth") continue; 
            if (is_array($v)) { 
                throw new OAuthException('Arrays not supported in headers'); 
            } 
            $out .= ',' . 
                OAuthUtil::urlencode_rfc3986($k) . 
                '="' . 
                OAuthUtil::urlencode_rfc3986($v) . 
                '"'; 
        } 
        return $out; 
    } 

    public function __toString() { 
        return $this->to_url(); 
    } 


    public function sign_request($signature_method, $consumer, $token) { 
        $this->set_parameter( 
            "oauth_signature_method", 
            $signature_method->get_name(), 
            false 
        ); 
		$signature = $this->build_signature($signature_method, $consumer, $token); 
        //echo "sign=" . $signature;
		$this->set_parameter("oauth_signature", $signature, false); 
    } 

    public function build_signature($signature_method, $consumer, $token) { 
        $signature = $signature_method->build_signature($this, $consumer, $token); 
        return $signature; 
    } 

    /** 
     * util function: current timestamp 
     */ 
    private static function generate_timestamp() { 
        //return 1273566716;
		//echo date("y-m-d H:i:s");
		return time(); 
    } 

    /** 
     * util function: current nonce 
     */ 
    private static function generate_nonce() { 
        //return '462d316f6f40c40a9e0eef1b009f37fa';
		$mt = microtime(); 
        $rand = mt_rand(); 

        return md5($mt . $rand); // md5s look nicer than numbers 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthServer { 
    protected $timestamp_threshold = 300; // in seconds, five minutes 
    protected $version = 1.0;             // hi blaine 
    protected $signature_methods = array(); 

    protected $data_store; 

    function __construct($data_store) { 
        $this->data_store = $data_store; 
    } 

    public function add_signature_method($signature_method) { 
        $this->signature_methods[$signature_method->get_name()] = 
            $signature_method; 
    } 

    // high level functions 

    /** 
     * process a request_token request 
     * returns the request token on success 
     */ 
    public function fetch_request_token(&$request) { 
        $this->get_version($request); 

        $consumer = $this->get_consumer($request); 

        // no token required for the initial token request 
        $token = NULL; 

        $this->check_signature($request, $consumer, $token); 

        $new_token = $this->data_store->new_request_token($consumer); 

        return $new_token; 
    } 

    /** 
     * process an access_token request 
     * returns the access token on success 
     */ 
    public function fetch_access_token(&$request) { 
        $this->get_version($request); 

        $consumer = $this->get_consumer($request); 

        // requires authorized request token 
        $token = $this->get_token($request, $consumer, "request"); 


        $this->check_signature($request, $consumer, $token); 

        $new_token = $this->data_store->new_access_token($token, $consumer); 

        return $new_token; 
    } 

    /** 
     * verify an api call, checks all the parameters 
     */ 
    public function verify_request(&$request) { 
        $this->get_version($request); 
        $consumer = $this->get_consumer($request); 
        $token = $this->get_token($request, $consumer, "access"); 
        $this->check_signature($request, $consumer, $token); 
        return array($consumer, $token); 
    } 

    // Internals from here 
    /** 
     * version 1 
     */ 
    private function get_version(&$request) { 
        $version = $request->get_parameter("oauth_version"); 
        if (!$version) { 
            $version = 1.0; 
        } 
        if ($version && $version != $this->version) { 
            throw new OAuthException("OAuth version '$version' not supported"); 
        } 
        return $version; 
    } 

    /** 
     * figure out the signature with some defaults 
     */ 
    private function get_signature_method(&$request) { 
        $signature_method = 
            @$request->get_parameter("oauth_signature_method"); 
        if (!$signature_method) { 
            $signature_method = "PLAINTEXT"; 
        } 
        
        if (!in_array($signature_method, 
            array_keys($this->signature_methods))) { 
                throw new OAuthException( 
                    "Signature method '$signature_method' not supported " . 
                    "try one of the following: " . 
                    implode(", ", array_keys($this->signature_methods)) 
                ); 
            } 
        return $this->signature_methods[$signature_method]; 
    } 

    /** 
     * try to find the consumer for the provided request's consumer key 
     */ 
    private function get_consumer(&$request) { 
        $consumer_key = @$request->get_parameter("oauth_consumer_key"); 
        if (!$consumer_key) { 
            throw new OAuthException("Invalid consumer key"); 
        } 

        $consumer = $this->data_store->lookup_consumer($consumer_key); 
        if (!$consumer) { 
            throw new OAuthException("Invalid consumer"); 
        } 

        return $consumer; 
    } 

    /** 
     * try to find the token for the provided request's token key 
     */ 
    private function get_token(&$request, $consumer, $token_type="access") { 
        $token_field = @$request->get_parameter('oauth_token'); 
        $token = $this->data_store->lookup_token( 
            $consumer, $token_type, $token_field 
        ); 
        if (!$token) { 
            throw new OAuthException("Invalid $token_type token: $token_field"); 
        } 
        return $token; 
    } 

    /** 
     * all-in-one function to check the signature on a request 
     * should guess the signature method appropriately 
     */ 
    private function check_signature(&$request, $consumer, $token) { 
        // this should probably be in a different method 
        $timestamp = @$request->get_parameter('oauth_timestamp'); 
        $nonce = @$request->get_parameter('oauth_nonce'); 

        $this->check_timestamp($timestamp); 
        $this->check_nonce($consumer, $token, $nonce, $timestamp); 

        $signature_method = $this->get_signature_method($request); 

        $signature = $request->get_parameter('oauth_signature'); 
        $valid_sig = $signature_method->check_signature( 
            $request, 
            $consumer, 
            $token, 
            $signature 
        ); 

        if (!$valid_sig) { 
            throw new OAuthException("Invalid signature"); 
        } 
    } 

    /** 
     * check that the timestamp is new enough 
     */ 
    private function check_timestamp($timestamp) { 
        // verify that timestamp is recentish 
        $now = time(); 
        if ($now - $timestamp > $this->timestamp_threshold) { 
            throw new OAuthException( 
                "Expired timestamp, yours $timestamp, ours $now" 
            ); 
        } 
    } 

    /** 
     * check that the nonce is not repeated 
     */ 
    private function check_nonce($consumer, $token, $nonce, $timestamp) { 
        // verify that the nonce is uniqueish 
        $found = $this->data_store->lookup_nonce( 
            $consumer, 
            $token, 
            $nonce, 
            $timestamp 
        ); 
        if ($found) { 
            throw new OAuthException("Nonce already used: $nonce"); 
        } 
    } 

} 

/** 
 * @ignore 
 */ 
class OAuthDataStore { 
    function lookup_consumer($consumer_key) { 
        // implement me 
    } 

    function lookup_token($consumer, $token_type, $token) { 
        // implement me 
    } 

    function lookup_nonce($consumer, $token, $nonce, $timestamp) { 
        // implement me 
    } 

    function new_request_token($consumer) { 
        // return a new token attached to this consumer 
    } 

    function new_access_token($token, $consumer) { 
        // return a new access token attached to this consumer 
        // for the user associated with this token if the request token 
        // is authorized 
        // should also invalidate the request token 
    } 

} 


/** 
 * @ignore 
 */ 
class OAuthUtil { 

	public static $boundary = '';

    public static function urlencode_rfc3986($input) { 
        if (is_array($input)) { 
            return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input); 
        } else if (is_scalar($input)) { 
            return str_replace( 
                '+', 
                ' ', 
                str_replace('%7E', '~', rawurlencode($input)) 
            ); 
        } else { 
            return ''; 
        } 
    } 


    // This decode function isn't taking into consideration the above 
    // modifications to the encoding process. However, this method doesn't 
    // seem to be used anywhere so leaving it as is. 
    public static function urldecode_rfc3986($string) { 
        return urldecode($string); 
    } 

    // Utility function for turning the Authorization: header into 
    // parameters, has to do some unescaping 
    // Can filter out any non-oauth parameters if needed (default behaviour) 
    public static function split_header($header, $only_allow_oauth_parameters = true) { 
        $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/'; 
        $offset = 0; 
        $params = array(); 
        while (preg_match($pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset) > 0) { 
            $match = $matches[0]; 
            $header_name = $matches[2][0]; 
            $header_content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0]; 
            if (preg_match('/^oauth_/', $header_name) || !$only_allow_oauth_parameters) { 
                $params[$header_name] = OAuthUtil::urldecode_rfc3986($header_content); 
            } 
            $offset = $match[1] + strlen($match[0]); 
        } 

        if (isset($params['realm'])) { 
            unset($params['realm']); 
        } 

        return $params; 
    } 

    // helper to try to sort out headers for people who aren't running apache 
    public static function get_headers() { 
        if (function_exists('apache_request_headers')) { 
            // we need this to get the actual Authorization: header 
            // because apache tends to tell us it doesn't exist 
            return apache_request_headers(); 
        } 
        // otherwise we don't have apache and are just going to have to hope 
        // that $_SERVER actually contains what we need 
        $out = array(); 
        foreach ($_SERVER as $key => $value) { 
            if (substr($key, 0, 5) == "HTTP_") { 
                // this is chaos, basically it is just there to capitalize the first 
                // letter of every word that is not an initial HTTP and strip HTTP 
                // code from przemek 
                $key = str_replace( 
                    " ", 
                    "-", 
                    ucwords(strtolower(str_replace("_", " ", substr($key, 5)))) 
                ); 
                $out[$key] = $value; 
            } 
        } 
        return $out; 
    } 

    // This function takes a input like a=b&a=c&d=e and returns the parsed 
    // parameters like this 
    // array('a' => array('b','c'), 'd' => 'e') 
    public static function parse_parameters( $input ) { 
        if (!isset($input) || !$input) return array(); 

        $pairs = explode('&', $input); 

        $parsed_parameters = array(); 
        foreach ($pairs as $pair) { 
            $split = explode('=', $pair, 2); 
            $parameter = OAuthUtil::urldecode_rfc3986($split[0]); 
            $value = isset($split[1]) ? OAuthUtil::urldecode_rfc3986($split[1]) : ''; 

            if (isset($parsed_parameters[$parameter])) { 
                // We have already recieved parameter(s) with this name, so add to the list 
                // of parameters with this name 

                if (is_scalar($parsed_parameters[$parameter])) { 
                    // This is the first duplicate, so transform scalar (string) into an array 
                    // so we can add the duplicates 
                    $parsed_parameters[$parameter] = array($parsed_parameters[$parameter]); 
                } 

                $parsed_parameters[$parameter][] = $value; 
            } else { 
                $parsed_parameters[$parameter] = $value; 
            } 
        } 
        return $parsed_parameters; 
    } 
    
    public static function build_http_query_multi($params) { 
        if (!$params) return ''; 
		
		//print_r( $params );
		//return null;
        
        // Urlencode both keys and values 
        $keys = array_keys($params);
        $values = array_values($params);
        //$keys = OAuthUtil::urlencode_rfc3986(array_keys($params)); 
        //$values = OAuthUtil::urlencode_rfc3986(array_values($params)); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 

        $pairs = array(); 
        
        self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

        foreach ($params as $parameter => $value) { 
            
        //if( $parameter == 'pic' && $value{0} == '@' )
        if( in_array($parameter,array("pic","image")) )
        {
        	$content = $value;
        	$filename = 'pic.jpg';
        	$mime ='image/jpeg'; 
        	
        	$multipartbody .= $MPboundary . "\r\n";
			$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
			$multipartbody .= 'Content-Type: '. $mime . "\r\n\r\n";
			$multipartbody .= $content. "\r\n";
        }
        else
        {
        	$multipartbody .= $MPboundary . "\r\n";
			$multipartbody .= 'content-disposition: form-data; name="'.$parameter."\"\r\n\r\n";
			$multipartbody .= $value."\r\n";
			
        }    
            
            
           
             
        } 
        
        $multipartbody .=  $endMPboundary."\r\n";
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61) 
        // Each name-value pair is separated by an '&' character (ASCII code 38) 
        // echo $multipartbody;
        return $multipartbody; 
    } 

    public static function build_http_query($params) { 
        if (!$params) return ''; 

        // Urlencode both keys and values 
        $keys = OAuthUtil::urlencode_rfc3986(array_keys($params)); 
        $values = OAuthUtil::urlencode_rfc3986(array_values($params)); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 

        $pairs = array(); 
        foreach ($params as $parameter => $value) { 
            if (is_array($value)) { 
                // If two or more parameters share the same name, they are sorted by their value 
                // Ref: Spec: 9.1.1 (1) 
                natsort($value); 
                foreach ($value as $duplicate_value) { 
                    $pairs[] = $parameter . '=' . $duplicate_value; 
                } 
            } else { 
                $pairs[] = $parameter . '=' . $value; 
            } 
        } 
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61) 
        // Each name-value pair is separated by an '&' character (ASCII code 38) 
        return implode('&', $pairs); 
    } 
    
    public static function get_image_mime( $file )
    {
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;
    		 	
    		case 'png';
    			$mime = 'image/png';
    			break;
    			
    		case 'gif';
    		default:
    			$mime = 'image/gif';
    			break;    		
    	}
    	return $mime;
    }
} 


class WeiboClient 
{ 

    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new WeiboOAuth( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    } 


	function get_ip() {
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) 
{ 
$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
} 
elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) 
{ 
$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
} 
elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) 
{ 
$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
} 
elseif (getenv("HTTP_X_FORWARDED_FOR")) 
{ 
$ip = getenv("HTTP_X_FORWARDED_FOR");
} 
elseif (getenv("HTTP_CLIENT_IP")) 
{ 
$ip = getenv("HTTP_CLIENT_IP");
} 
elseif (getenv("REMOTE_ADDR")) 
{ 
$ip = getenv("REMOTE_ADDR");
} 
else 
{ 
$ip = "Unknown"; 
} 
   return $ip;
}


    //广播大厅时间线
    function public_timeline($pos=0,$reqnum=20,$format='json') 
    { 
    	$param['format']=$format;
    	$param['pos']=$pos;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/public_timeline',$param); 
    } 

	//首页时间线
    function home_timeline($pageflag=0,$pagetime=0,$reqnum=20,$format='json') 
    { 
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/home_timeline',$param); 
    } 


    //其他用户发表时间线
	    function user_timeline($name,$pageflag=0,$pagetime=0,$reqnum=20,$format='json') 
    { 
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
		$param['name']=$name;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/user_timeline',$param); 
    } 

   // @提到我的微博时间线
   function  mentions_timeline($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/mentions_timeline',$param);    
      }

  //话题时间线
   function  ht_timeline($httext,$pageflag=1,$pageinfo='',$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
		$param['httext']=$httext;
    	$param['pageflag']=$pageflag;
    	$param['pageinfo']=$pageinfo;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/ht_timeline',$param);    
      }
  
  //我发表时间线
   function broadcast_timeline($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/broadcast_timeline',$param);    
      }

//特别收听的人发表时间线
   function special_timeline($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/statuses/special_timeline',$param);    
      }



//--------------------------------------------------------------------//
//***************************微博相关*********************************//
//--------------------------------------------------------------------//

//1.t/show 获取一条微博数据
    function t_show($id,$format='json') 
    { 
        $param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/t/show' ,$param); 
    } 

//2.t/add 发表一条微博
    function t_add($content='',$jing='',$wei='',$format='json') 
    { 
        $param['content'] =$content; 
        $param['format']=$format;
		$param['jing']=$jing;
		$param['wei']=$wei;
		$param['clientip']=$this->get_ip();
        return $this->oauth->post( 'http://open.t.qq.com/api/t/add' ,$param); 
    } 

//3.t/del 删除一条微博
    function t_del($id,$format='json') 
    { 
        $param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/t/del' ,$param); 
    } 

//4.t/re_add 转播一条微博
    function t_re_add($reid,$content='',$jing='',$wei='',$format='json') 
    { 
        $param['content'] =$content; 
		$param['reid'] =$reid; 
        $param['format']=$format;
		$param['jing']=$jing;
		$param['wei']=$wei;
		$param['clientip']=$this->get_ip();
        return $this->oauth->post( 'http://open.t.qq.com/api/t/re_add' ,$param); 
    } 

//5.t/reply 回复一条微博
    function t_reply($reid,$content='',$jing='',$wei='',$format='json') 
    { 
        $param['content'] =$content; 
		$param['reid'] =$reid; 
        $param['format']=$format;
		$param['jing']=$jing;
		$param['wei']=$wei;
		$param['clientip']=$this->get_ip();
        return $this->oauth->post( 'http://open.t.qq.com/api/t/reply' ,$param); 
    } 

//6.t/add_pic 发表一条带图片的微博
    function t_add_pic($content='',$pic_data='',$jing='',$wei='',$format='json') 
    { 
        $param['format']=$format;
        $param['pic']=$pic_data;
        $param['content']=$content;
		$param['jing']=$jing;
		$param['wei']=$wei;
        return $this->oauth->post( 'http://open.t.qq.com/api/t/add_pic',$param,true); //采用multi form-data方式提交
    } 

//7.t/re_count 转播数  参数 $ids 的格式为: "第1条微博id,第2条微博id,第3条微博id,........."   最多30个
    function t_re_count($ids,$format='json') 
    { 
        $param['ids'] =$ids; 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/t/re_count' ,$param); 
    } 

//8.t/re_list 获取单条微博的转发或点评列表  Flag:标识0 转播列表，1点评列表 2 点评与转播列表
   function  t_re_list($flag,$rootid,$pageflag=0,$pagetime=0,$reqnum=20,$twitterid=0,$format='json')
	   {
    	$param['format']=$format;
		$param['flag']=$flag;
		$param['rootid']=$rootid;
		$param['twitterid']=$twitterid;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get('http://open.t.qq.com/api/t/re_list',$param);    
      }

//9.t/comment 点评一条微博
    function t_comment($reid,$content='',$jing='',$wei='',$format='json') 
    { 
        $param['content'] =$content; 
		$param['reid'] =$reid; 
        $param['format']=$format;
		$param['jing']=$jing;
		$param['wei']=$wei;
		$param['clientip']=$this->get_ip();
        return $this->oauth->post( 'http://open.t.qq.com/api/t/comment' ,$param); 
    } 

//--------------------------------------------------------------------//
//********************       帐户相关       **************************//
//--------------------------------------------------------------------//

//1.User/info获取自己的详细资料
    function user_info($format='json') 
    { 
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/user/info' ,$param); 
    } 
    
//2.user/update 更新用户信息
    function user_update($nick='',$introduction='',$sex=0,$year=1980,$month=1,$day=1,$countrycode=1,$provincecode=1,$citycode=1,$format='json') 
    { 
       $param['nick'] =$nick; 
		$param['introduction'] =$introduction; 
       $param['sex']=$sex;
		$param['year']=$year;
		$param['month']=$month;
		$param['day']=$day;
		$param['countrycode']=$countrycode;
		$param['provincecode']=$provincecode;
		$param['citycode']=$citycode;
		 $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/user/update' ,$param); 
    } 
//3.user/update_head 更新用户头像信息
    function user_update_head($pic,$format='json') 
    { 
       $param['pic'] =$pic; 
       $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/user/update_head' ,$param,true); 
    } 

//4.user/other_info 获取其他人资料
    function user_other_info($name,$format='json') 
    {   
		$param['name'] =$name;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/user/other_info' ,$param); 
    } 



//--------------------------------------------------------------------//
//********************      关系链相关      **************************//
//--------------------------------------------------------------------//


//1.friends/fanslist 我的听众列表
    function f_fanslist($startindex=0,$reqnum=30,$format='json') 
    {   
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/fanslist' ,$param); 
    } 

//2.friends/idollist 我收听的人列表
    function f_idollist($startindex=0,$reqnum=30,$format='json') 
    {   
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/idollist' ,$param); 
    } 

//3.friends/blacklist 黑名单列表
    function f_blacklist($startindex=0,$reqnum=30,$format='json') 
    {   
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/blacklist' ,$param); 
    } 

//4.friends/speciallist 特别收听列表
    function f_speciallist($startindex=0,$reqnum=30,$format='json') 
    {   
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/speciallist' ,$param); 
    } 


//5.friends/add  收听某个人
    function f_add($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/add' ,$param); 
    } 

//6.friends/del  取消收听某个人
    function f_del($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/del' ,$param); 
    } 

//7.friends/addspecial 特别收听某个
    function f_add_s($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/addspecial' ,$param); 
    } 

//8.friends/delspecial 取消特别收听某个
    function f_del_s($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/delspecial' ,$param); 
    } 

//9.friends/addblacklist 添加某个用户到黑名单
    function f_add_black($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/addblacklist' ,$param); 
    } 
//10.friends/delblacklist 从黑名单释放某用户
    function f_del_black($name='',$format='json') 
    { 
        $param['name'] =$name; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/friends/delblacklist' ,$param); 
    } 

//11.friends/check 检测是否是我的粉丝或偶像  flag 0:粉丝  1：偶像
    function f_check($names='',$flag=0,$format='json') 
    { 
        $param['names'] =$names; 
        $param['flag'] =$flag; 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/friends/check' ,$param); 
    } 

//12.friends/user_fanslist 其他用户的听众列表
    function f_user_fanslist($name,$startindex=0,$reqnum=30,$format='json') 
    {   $param['name'] =$name;
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/user_fanslist' ,$param); 
    } 

//13.friends/user_idollist 其他用户的听众列表
    function f_user_idollist($name,$startindex=0,$reqnum=30,$format='json') 
    {   $param['name'] =$name;
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/user_idollist' ,$param); 
    } 

//4.friends/user_speciallist  其他用户的特别收听列表
    function f_user_speciallist($name,$startindex=0,$reqnum=30,$format='json') 
    {   $param['name'] =$name;
		$param['startindex'] =$startindex;
		$param['reqnum'] =$reqnum;
        $param['format']=$format;
        return $this->oauth->get('http://open.t.qq.com/api/friends/user_speciallist' ,$param); 
    } 


//--------------------------------------------------------------------//
//********************       私信相关       **************************//
//--------------------------------------------------------------------//

//1.private/add 发一条私信
    function pm_add($name,$content='',$jing='',$wei='',$format='json') 
    { 
        $param['content'] =$content; 
		$param['name'] =$name; 
        $param['format']=$format;
		$param['jing']=$jing;
		$param['wei']=$wei;
		$param['clientip']=$this->get_ip();
        return $this->oauth->post( 'http://open.t.qq.com/api/private/add' ,$param); 
    } 
//2.private/del 删除一条私信
    function pm_del($id,$format='json') 
    { 
		$param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/private/del' ,$param); 
    } 

//3.private/recv 获取私信收件箱列表
    function pm_recv($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get( 'http://open.t.qq.com/api/private/recv' ,$param); 
    } 

//4.private/send 获取私信收件箱列表
    function pm_send($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get( 'http://open.t.qq.com/api/private/send' ,$param); 
    } 


//--------------------------------------------------------------------//
//********************       搜索相关       **************************//
//--------------------------------------------------------------------//

//1.Search/user 搜索用户
    function search_user($keyword,$page=1,$pagesize=10,$format='json')
	   {
    	$param['format']=$format;
    	$param['page']=$page;
    	$param['pagesize']=$pagesize;
    	$param['keyword']=$keyword;
        return $this->oauth->get( 'http://open.t.qq.com/api/search/user' ,$param); 
    } 

//2.Search/t 搜索微博
    function search_t($keyword,$page=1,$pagesize=10,$format='json')
	   {
    	$param['format']=$format;
    	$param['page']=$page;
    	$param['pagesize']=$pagesize;
    	$param['keyword']=$keyword;
        return $this->oauth->get( 'http://open.t.qq.com/api/search/t' ,$param); 
    } 

//3.Search/userbytag 搜索微博
    function search_by_tag($keyword,$page=1,$pagesize=10,$format='json')
	   {
    	$param['format']=$format;
    	$param['page']=$page;
    	$param['pagesize']=$pagesize;
    	$param['keyword']=$keyword;
        return $this->oauth->get( 'http://open.t.qq.com/api/search/userbytag' ,$param); 
    } 




//--------------------------------------------------------------------//
//********************       标签相关       **************************//
//--------------------------------------------------------------------//

//1.tag/add 添加标签
    function tag_add($tag,$format='json') 
    { 
		$param['tag'] =$tag; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/tag/add' ,$param); 
    } 
//2.tag/del  删除标签
    function tag_del($tagid,$format='json') 
    { 
		$param['tagid'] =$tagid; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/tag/del' ,$param); 
    } 


//--------------------------------------------------------------------//
//********************       热度趋势       **************************//
//--------------------------------------------------------------------//

//1.trends/ht 话题热榜
    function hts($type=3,$pos=0,$reqnum=20,$format='json') 
    { 
		$param['type'] =$type; 
		$param['pos'] =$pos; 
		$param['reqnum'] =$reqnum; 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/trends/ht' ,$param); 
    } 

//--------------------------------------------------------------------//
//********************       数据更新       **************************//
//--------------------------------------------------------------------//
//1.info/update 查看数据更新条数
    function info_update($op=0,$type=5,$format='json') 
    { 
		$param['op'] =$op; 
		if($op==1){
		$param['type'] =$type;
		}
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/info/update' ,$param); 
    } 

//--------------------------------------------------------------------//
//********************       数据收藏       **************************//
//--------------------------------------------------------------------//
//1.fav/addt 收藏一条微博
    function fav_add_t($id,$format='json') 
    { 
		$param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/fav/addt' ,$param); 
    } 
//2.fav/delt 删除一条收藏
    function fav_del_t($id,$format='json') 
    { 
		$param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/fav/delt' ,$param); 
    } 
//3.fav/list_t 获取收藏的微博列表
    function fav_list_t($pageflag=0,$pagetime=0,$reqnum=20,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get( 'http://open.t.qq.com/api/fav/list_t' ,$param); 
    } 
//4.fav/addht 收藏话题
    function fav_add_ht($id,$format='json') 
    { 
		$param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/fav/addht' ,$param); 
    } 
//5.fav/delt 删除一条话题收藏 
    function fav_del_ht($id,$format='json') 
    { 
		$param['id'] =$id; 
        $param['format']=$format;
        return $this->oauth->post( 'http://open.t.qq.com/api/fav/delht' ,$param); 
    } 
//6.fav/list_ht 获取收藏的话题列表
    function fav_list_ht($pageflag=0,$pagetime=0,$reqnum=15,$format='json')
	   {
    	$param['format']=$format;
    	$param['pageflag']=$pageflag;
    	$param['pagetime']=$pagetime;
    	$param['reqnum']=$reqnum;
        return $this->oauth->get( 'http://open.t.qq.com/api/fav/list_ht' ,$param); 
    } 

//--------------------------------------------------------------------//
//********************       话题相关       **************************//
//--------------------------------------------------------------------//
//1.ht/ids 根据话题名称查话题ID
    function ht_ids($httexts,$format='json')
	   {
    	$param['httexts']=$httexts;
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/ht/ids' ,$param); 
    } 
//2.ht/info 根据话题名称查话题ID
    function ht_info($ids,$format='json')
	   {
    	$param['ids']=$ids;
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/ht/info' ,$param); 
    } 
//--------------------------------------------------------------------//
//********************       other          **************************//
//--------------------------------------------------------------------//
//other/kownperson   我可能认识的人
function kownperson($ip=false,$format='json')
	{
    	$param['ip']=$this->get_ip();
        $param['format']=$format;
		return $this->oauth->get( 'http://open.t.qq.com/api/other/kownperson' ,$param); 
    }

	//发表微博

	//我的信息
    function getinfo($format='json') 
    { 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/user/info' ,$param); 
    } 
    
  	//发表带图片微博







   //获取自己信息(sina api 同步)
	 function verify_credentials($format='json') 
   { 
        $param['format']=$format;
        return $this->oauth->get( 'http://open.t.qq.com/api/user/info',$param); 
    } 




   
} 


class WeiboOAuth { 
    /** 
     * Contains the last HTTP status code returned.  
     * 
     * @ignore 
     */ 
    public $http_code; 
    /** 
     * Contains the last API call. 
     * 
     * @ignore 
     */ 
    public $url; 
    /** 
     * Set up the API root URL. 
     * 
     * @ignore 
     */ 
    public $host = "http://open.t.qq.com/"; 
    /** 
     * Set timeout default. 
     * 
     * @ignore 
     */ 
    public $timeout = 30; 
    /**  
     * Set connect timeout. 
     * 
     * @ignore 
     */ 
    public $connecttimeout = 30;  
    /** 
     * Verify SSL Cert. 
     * 
     * @ignore 
     */ 
    public $ssl_verifypeer = FALSE; 
    /** 
     * Respons format. 
     * 
     * @ignore 
     */ 
    public $format = 'json'; 
    /** 
     * Decode returned json data. 
     * 
     * @ignore 
     */ 
    public $decode_json = TRUE; 
    /** 
     * Contains the last HTTP headers returned. 
     * 
     * @ignore 
     */ 
    public $http_info; 
    /** 
     * Set the useragnet. 
     * 
     * @ignore 
     */ 
    public $useragent = 'Sae T OAuth v0.2.0-beta2'; 
    /* Immediately retry the API call if the response was not successful. */ 
    //public $retry = TRUE; 
    



    /** 
     * Set API URLS 
     */ 
    /** 
     * @ignore 
     */ 
    function accessTokenURL()  { return 'https://open.t.qq.com/cgi-bin/access_token'; } 
    /** 
     * @ignore 
     */ 
    function authorizeURL()    { return 'https://open.t.qq.com/cgi-bin/authorize'; } 
    /** 
     * @ignore 
     */ 
    
   /*
    function requestTokenURL() { return 'http://api.t.sina.com.cn/oauth/request_token'; }
    */ 
   function requestTokenURL() { return 'https://open.t.qq.com/cgi-bin/request_token'; } 
    

    /** 
     * Debug helpers 
     */ 
    /** 
     * @ignore 
     */ 
    function lastStatusCode() { return $this->http_status; } 
    /** 
     * @ignore 
     */ 
    function lastAPICall() { return $this->last_api_call; } 

    /** 
     * construct WeiboOAuth object 
     */ 
    function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) { 
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1(); 
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret); 
        if (!empty($oauth_token) && !empty($oauth_token_secret)) { 
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret); 
        } else { 
            $this->token = NULL; 
        } 
    } 


    /** 
     * Get a request_token from Weibo 
     * 
     * @return array a key/value array containing oauth_token and oauth_token_secret 
     */ 
    function getRequestToken($oauth_callback = NULL) { 
        $parameters = array(); 
        if (!empty($oauth_callback)) { 
            $parameters['oauth_callback'] = $oauth_callback; 
        }  

        $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters); 
        $token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * Get the authorize URL 
     * 
     * @return string 
     */ 
    function getAuthorizeURL($token, $sign_in_with_Weibo = TRUE , $url) { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($sign_in_with_Weibo)) { 
            return $this->authorizeURL() . "?oauth_token={$token}"; 
        } else { 
            return $this->authenticateURL() . "?oauth_token={$token}"; 
        } 
    } 

    /** 
     * Exchange the request token and secret for an access token and 
     * secret, to sign API calls. 
     * 
     * @return array array("oauth_token" => the access token, 
     *                "oauth_token_secret" => the access secret) 
     */ 
    function getAccessToken($oauth_verifier = FALSE, $oauth_token = false) { 
        $parameters = array(); 
        if (!empty($oauth_verifier)) { 
            $parameters['oauth_verifier'] = $oauth_verifier; 
        } 


        $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters); 
        $token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * GET wrappwer for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function get($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'GET', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * POST wreapper for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function post($url, $parameters = array() , $multi = false) { 
        
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi ); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * DELTE wrapper for oAuthReqeust. 
     * 
     * @return mixed 
     */ 
    function delete($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'DELETE', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * Format and sign an OAuth / API request 
     * 
     * @return string 
     */ 
    function oAuthRequest($url, $method, $parameters , $multi = false) { 

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) { 
            $url = "{$this->host}{$url}.{$this->format}"; 
        } 

        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters); 
        $request->sign_request($this->sha1_method, $this->consumer, $this->token); 
        switch ($method) { 
        case 'GET': 
            //echo $request->to_url(); 
            return $this->http($request->to_url(), 'GET'); 
        default: 
            return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi ); 
        } 
    } 

    /** 
     * Make an HTTP request 
     * 
     * @return string API results 
     */ 
    function http($url, $method, $postfields = NULL , $multi = false) { 
        $this->http_info = array(); 
        $ci = curl_init(); 
        /* Curl settings */ 
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent); 
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 

        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer); 

        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); 

        curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($postfields)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields); 
                //echo "=====post data======\r\n";
                //echo $postfields;
            } 
            break; 
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($postfields)) { 
                $url = "{$url}?{$postfields}"; 
            } 
        } 

        $header_array = array(); 
        
/*
        $header_array["FetchUrl"] = $url; 
        $header_array['TimeStamp'] = date('Y-m-d H:i:s'); 
        $header_array['AccessKey'] = SAE_ACCESSKEY; 


        $content="FetchUrl"; 

        $content.=$header_array["FetchUrl"]; 

        $content.="TimeStamp"; 

        $content.=$header_array['TimeStamp']; 

        $content.="AccessKey"; 

        $content.=$header_array['AccessKey']; 

        $header_array['Signature'] = base64_encode(hash_hmac('sha256',$content, SAE_SECRETKEY ,true)); 
*/
        //curl_setopt($ci, CURLOPT_URL, SAE_FETCHURL_SERVICE_ADDRESS ); 

        //print_r( $header_array ); 
        $header_array2=array(); 
        if( $multi ) 
        	$header_array2 = array("Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary , "Expect: ");
        foreach($header_array as $k => $v) 
            array_push($header_array2,$k.': '.$v); 

        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2 ); 
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 


        curl_setopt($ci, CURLOPT_URL, $url); 

        $response = curl_exec($ci); 
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci)); 
        $this->url = $url; 

        //echo '=====info====='."\r\n";
        //print_r( curl_getinfo($ci) ); 
        
        //echo '=====$response====='."\r\n";
       //print_r( $response ); 

        curl_close ($ci); 
        return $response; 
    } 

    /** 
     * Get the header info to store. 
     * 
     * @return int 
     */ 
    function getHeader($ch, $header) { 
        $i = strpos($header, ':'); 
        if (!empty($i)) { 
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i))); 
            $value = trim(substr($header, $i + 2)); 
            $this->http_header[$key] = $value; 
        } 
        return strlen($header); 
    } 
} 

