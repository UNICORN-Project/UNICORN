<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * An interface for Twitter's REST API and Search API.
 *
 * PHP version 5.1.0+
 *
 * @category  Services
 * @package   Services_Twitter
 * @author    Joe Stump <joe@joestump.net> 
 * @author    David Jean Louis <izimobil@gmail.com> 
 * @author    Bill Shupp <shupp@php.net>
 * @copyright 1997-2008 Joe Stump <joe@joestump.net> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id: Twitter.php 40 2009-01-07 12:40:35Z izimobil $
 * @link      http://twitter.com
 * @link      http://apiwiki.twitter.com
 * @filesource
 */

/**
 * Include HTTP_Request2 class and exception classes.
 */
require_once 'HTTP/Request2.php';
require_once 'Services_Twitter-0.6.3/Services/Twitter/Exception.php';

/**
 * Base class for interacting with Twitter's API.
 *
 * Here's a basic auth example:
 *
 * <code>
 * require_once 'Services/Twitter.php';
 *
 * $username = 'Your_Username';
 * $password = 'Your_Password';
 *
 * try {
 *     $twitter = new Services_Twitter($username, $password);
 *     $msg = $twitter->statuses->update("I'm coding with PEAR right now!");
 *     print_r($msg);
 * } catch (Services_Twitter_Exception $e) {
 *     echo $e->getMessage(); 
 * }
 *
 * </code>
 *
 * Here's an OAuth example:
 *
 * <code>
 * require_once 'Services/Twitter.php';
 * require_once 'HTTP/OAuth/Consumer.php';
 *
 *
 * try {
 *     $twitter = new Services_Twitter();
 *     $oauth   = new HTTP_OAuth_Consumer('consumer_key',
 *                                        'consumer_secret',
 *                                        'auth_token',
 *                                        'token_secret');
 *     $twitter->setOAuth($oauth);
 *
 *     $msg = $twitter->statuses->update("I'm coding with PEAR right now!");
 *     print_r($msg);
 * } catch (Services_Twitter_Exception $e) {
 *     echo $e->getMessage(); 
 * }
 *
 * </code>
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @author   David Jean Louis <izimobil@gmail.com> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 * @link     http://apiwiki.twitter.com
 */
class Services_Twitter
{
    // constants {{{

    /**#@+
     * Exception codes constants defined by this package.
     *
     * @global integer ERROR_UNKNOWN  An unknown error occurred
     * @global integer ERROR_ENDPOINT Bad endpoint
     * @global integer ERROR_PARAMS   Bad endpoint parameters
     */
    const ERROR_UNKNOWN  = 1;
    const ERROR_ENDPOINT = 2;
    const ERROR_PARAMS   = 3;
    /**#@-*/

    /**#@+
     * Twitter API output parsing options
     *
     * @global string OUTPUT_XML  The response is expected to be XML
     * @global string OUTPUT_JSON The response is expected to be JSON
     */
    const OUTPUT_XML  = 'xml';
    const OUTPUT_JSON = 'json';
    /**#@-*/

    // }}}
    // properties {{{

    /**
     * Public URI of Twitter's API
     *
     * @var string $uri
     */
    public static $uri = 'http://api.twitter.com/1';

    /**
     * Public URI of Twitter's Search API
     *
     * @var string $uri
     */
    public static $searchUri = 'http://search.twitter.com';

    /**
     * Username of Twitter user
     *
     * @var string $user Twitter username
     * @see Services_Twitter::__construct()
     */
    protected $user = '';

    /**
     * Password of Twitter user
     *
     * @var string $pass User's password for Twitter
     * @see Services_Twitter::__construct()
     */
    protected $pass = '';

    /**
     * Optional instance of HTTP_OAuth_Consumer
     * 
     * @var HTTP_OAuth_Consumer $oauth
     * @see HTTP_OAuth_Consumer
     */
    protected $oauth = null;

    /**
     * Options for HTTP requests and misc.
     *
     * Available options are:
     * - format: the desired output format: json (default) or xml,
     * - raw_format: if set to true, the configured format is returned "as is",
     * - source: you can set this if you have registered a twitter source 
     *   {@see http://twitter.com/help/request_source}, your source will be 
     *   passed with each POST request,
     * - use_ssl: whether to send all requests over ssl or not. If set to true, the 
     *   script will replace the http:// URL scheme with https://,
     * - validate: if set to true the class will validate api xml file against 
     *   the RelaxNG file (you should not need this unless you are hacking 
     *   Services_Twitter...).
     *
     * These options can be set either by passing them directly to the 
     * constructor as an array (3rd parameter), or by using the setOption() or 
     * setOptions() methods.
     *
     * @var array $options
     * @see Services_Twitter::__construct()
     * @see Services_Twitter::setOption()
     * @see Services_Twitter::setOptions()
     */
    protected $options = array(
        'format'     => self::OUTPUT_JSON,
        'raw_format' => false,
        'source'     => 'pearservicestwitter',
        'use_ssl'    => false,
        'validate'   => false,
    );

    /**
     * The HTTP_Request2 instance, you can customize the request if you want to 
     * (proxy, auth etc...) with the get/setRequest() methods.
     *
     * @var HTTP_Request2 $request
     * @see Services_Twitter::getRequest()
     * @see Services_Twitter::setRequest()
     */
    protected $request = null;

    /**
     * The Twitter API mapping array, used internally to retrieve infos about 
     * the API categories, endpoints, parameters, etc...
     *
     * The mapping is constructed with the api.xml file present in the 
     * Services_Twitter data directory.
     *
     * @var array $api Twitter api array
     * @see Services_Twitter::__construct()
     * @see Services_Twitter::prepareRequest()
     */
    protected $api = array();

    /**
     * Used internally by the __get() and __call() methods to identify the
     * current call.
     *
     * @var string $currentCategory 
     * @see Services_Twitter::__get()
     * @see Services_Twitter::__call()
     */
    protected $currentCategory = null;

    // }}}
    // __construct() {{{

    /**
     * Constructor.
     *
     * @param string $user    Twitter username
     * @param string $pass    Twitter password
     * @param array  $options An array of options
     *
     * @return void
     */
    public function __construct($user = null, $pass = null, $options = array())
    {
        // set properties and options
        $this->user = $user;
        $this->pass = $pass;
        $this->setOptions($options);

        // load the XML API definition
        $this->loadAPI();
    }

    // }}}
    // __get() {{{

    /**
     * Get interceptor, if the requested property is "options", it just return 
     * the options array, otherwise, if the property matches a valid API 
     * category it return an instance of this class.
     *
     * @param string $property The property of the call
     *
     * @return mixed 
     */
    public function __get($property)
    {
        if ($this->currentCategory === null) {
            if (isset($this->api[$property])) {
                $this->currentCategory = $property;
                return $this;
            }
        } else {
            $this->currentCategory = null;
        }
        throw new Services_Twitter_Exception(
            'Unsupported endpoint ' . $property,
            self::ERROR_ENDPOINT
        );
    }

    // }}}
    // __call() {{{

    /**
     * Overloaded call for API passthrough.
     * 
     * @param string $endpoint API endpoint being called
     * @param array  $args     $args[0] is an array of GET/POST arguments
     * 
     * @return object Instance of SimpleXMLElement
     */
    public function __call($endpoint, array $args = array())
    {
        if ($this->currentCategory !== null) {
            if (!isset($this->api[$this->currentCategory][$endpoint])) {
                throw new Services_Twitter_Exception(
                    'Unsupported endpoint ' 
                    . $this->currentCategory . '/' . $endpoint,
                    self::ERROR_ENDPOINT
                );
            }
            // case of a classic "category->endpoint()" call
            $ep = $this->api[$this->currentCategory][$endpoint]; 
        } else if (isset($this->api[$endpoint][$endpoint])) {
            // case of a "root" endpoint call, the method is the name of the 
            // category (ex: $twitter->direct_messages())
            $ep = $this->api[$endpoint][$endpoint];
        } else {
            throw new Services_Twitter_Exception(
                'Unsupported endpoint ' . $endpoint,
                self::ERROR_ENDPOINT
            );
        }

        // check that endpoint is available in the configured format
        $formats = explode(',', (string)$ep->formats);
        if (!in_array($this->getOption('format'), $formats)) {
            throw new Services_Twitter_Exception(
                'Endpoint ' . $endpoint . ' does not support '
                . $this->getOption('format') . ' format',
                self::ERROR_ENDPOINT
            );
        }

        // we must reset the current category to null for future calls.
        $cat                   = $this->currentCategory;
        $this->currentCategory = null;

        // prepare the request
        list($uri, $method, $params, $files)
            = $this->prepareRequest($ep, $args, $cat);

        // we can now send our request
        if ($this->oauth instanceof HTTP_OAuth_Consumer) {
            $resp = $this->sendOAuthRequest($uri, $method, $params, $files);
        } else {
            $resp = $this->sendRequest($uri, $method, $params, $files);
        }
        $body = $resp->getBody();

        // check for errors
        if (substr($resp->getStatus(), 0, 1) != '2') {
            $error = $this->decodeBody($body);
            if (isset($error->error)) {
                $message = (string) $error->error;
            } else {
                $message = $resp->getReasonPhrase();
            }
            throw new Services_Twitter_Exception(
                $message,
                $resp->getStatus(),
                $uri,
                $resp
            );
        }

        if ($this->getOption('raw_format')) {
            return $body;
        }
        return $this->decodeBody($body);
    }

    // }}}
    // sendOAuthRequest() {{{

    /**
     * Sends a request using OAuth instead of basic auth
     * 
     * @param string $uri    The full URI of the endpoint
     * @param string $method GET or POST
     * @param array  $params Array of additional parameter
     * @param array  $files  Array of files to upload
     * 
     * @throws Services_Twitter_Exception on failure
     * @return HTTP_Request2_Response
     * @see prepareRequest()
     */
    protected function sendOAuthRequest($uri, $method, $params, $files)
    {
        include_once 'HTTP/OAuth/Consumer/Request.php';
        try {
            $request = clone $this->getRequest();

            if ($method == 'POST') {
                foreach ($files as $key => $val) {
                    $request->addUpload($key, $val);
                }
            }

            // Use the same instance of HTTP_Request2
            $consumerRequest = new HTTP_OAuth_Consumer_Request;
            $consumerRequest->accept($request);
            $this->oauth->accept($consumerRequest);

            $response = $this->oauth->sendRequest($uri, $params, $method);
        } catch (HTTP_Request2_Exception $exc) {
            throw new Services_Twitter_Exception(
                $exc->getMessage(),
                $exc, // the original exception cause
                $uri
            );
        }
        return $response;
    }

    // }}}
    // setOption() {{{

    /**
     * Set an option in {@link Services_Twitter::$options}
     *
     * If a function exists named _set$option (e.g. _setUserAgent()) then that
     * method will be used instead. Otherwise, the value is set directly into
     * the options array.
     *
     * @param string $option Name of option
     * @param mixed  $value  Value of option
     *
     * @return void
     * @see Services_Twitter::$options
     */
    public function setOption($option, $value)
    {
        $func = '_set' . ucfirst($option);
        if (method_exists($this, $func)) {
            $this->$func($value);
        } else {
            $this->options[$option] = $value;
        }
    }

    /**
     * Sets an instance of HTTP_OAuth_Consumer
     * 
     * @param HTTP_OAuth_Consumer $oauth Object containing OAuth credentials
     * 
     * @see $oauth
     * @see $sendOAuthRequest
     * @return void
     */
    public function setOAuth(HTTP_OAuth_Consumer $oauth)
    {
        $this->oauth = $oauth;
    }

    // }}}
    // getOption() {{{

    /**
     * Get an option from {@link Services_Twitter::$options}
     *
     * @param string $option Name of option
     *
     * @return mixed
     * @see Services_Twitter::$options
     */
    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
        return null;
    }

    // }}}
    // setOptions() {{{

    /**
     * Set a number of options at once in {@link Services_Twitter::$options}
     *
     * @param array $options Associative array of options name/value
     *
     * @return void
     * @see Services_Twitter::$options
     * @see Services_Twitter::setOption()
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    // }}}
    // getOptions() {{{

    /**
     * Return the Services_Twitter options array.
     *
     * @return array
     * @see Services_Twitter::$options
     */
    public function getOptions()
    {
        return $this->options;
    }

    // }}}
    // getRequest() {{{
    
    /**
     * Returns the HTTP_Request2 instance.
     * 
     * @return HTTP_Request2 The request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = new HTTP_Request2();
        }
        if ($this->getOption('use_ssl')) {
            // XXX ssl won't work with ssl_verify_peer set to true, which is 
            // the default in HTTP_Request2
            $this->request->setConfig('ssl_verify_peer', false);
        }
        return $this->request;
    }
    
    // }}}
    // setRequest() {{{
    
    /**
     * Sets the HTTP_Request2 instance.
     * 
     * @param HTTP_Request2 $request The request to set
     *
     * @return void
     */
    public function setRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
    }
    
    // }}}
    // decodeBody() {{{

    /**
     * Decode the response body according to the configured format.
     *
     * @param string $body The response body to decode
     *
     * @return mixed
     */
    protected function decodeBody($body)
    {

        if ($this->getOption('format') == Services_Twitter::OUTPUT_XML) {
            // See http://pear.php.net/bugs/bug.php?id=17345
            $previousSetting = libxml_use_internal_errors(true);
            $result          = simplexml_load_string($body);
            libxml_use_internal_errors($previousSetting);
            $isbool = ((string)$result == 'true' || (string)$result == 'false');
        } else { 
            // default to Services_Twitter::OUTPUT_JSON
            $result = json_decode($body);
            $isbool = ($result == 'true' || $result == 'false');
        }
        // special case where the API returns true/false strings
        if ($isbool) {
            return (string)$result == 'true';
        }
        return $result;
    }

    // }}}
    // loadAPI() {{{

    /**
     * Loads the XML API definition.
     *
     * @return void
     */
    protected function loadAPI()
    {
        // initialize xml mapping
        $p = is_dir('@data_dir@') 
            ? array('@data_dir@', 'Services_Twitter', 'data')
            : array(dirname(__FILE__), '..', 'data');
        $d = implode(DIRECTORY_SEPARATOR, $p) . DIRECTORY_SEPARATOR;
        if ($this->getOption('validate') && class_exists('DomDocument')) {
            // this should be done only when testing
            $doc = new DomDocument();
            $doc->load($d . 'api.xml');
            $doc->relaxNGValidate($d . 'api.rng');
        }
        $xmlApi = simplexml_load_file($d . 'api.xml');
        foreach ($xmlApi->category as $category) {
            $catName             = (string)$category['name'];
            $this->api[$catName] = array();
            foreach ($category->endpoint as $endpoint) {
                $this->api[$catName][(string)$endpoint['name']] = $endpoint;
            }
        }
    }

    // }}}
    // prepareRequest() {{{

    /**
     * Prepare the request before it is sent.
     *
     * @param SimpleXMLElement $endpoint API endpoint xml node
     * @param array            $args     API endpoint arguments
     * @param string           $cat      The current category
     *
     * @throws Services_Twitter_Exception
     * @return array The array of arguments to pass to in the request
     */
    protected function prepareRequest($endpoint, array $args = array(), $cat = null)
    {
        $params = array();
        $files  = array();

        // check if we have is a search or trends call, in this case the base 
        // uri is different
        if (   $cat == 'search' 
            || ( $cat == 'trends'
            && !in_array((string)$endpoint['name'], array('available', 'location')))
        ) {
            $uri = self::$searchUri;
        } else {
            $uri = self::$uri;
        }

        // ssl requested
        if ($this->getOption('use_ssl')) {
            $uri = str_replace('http://', 'https://', $uri);
        }

        // build the uri path
        $path = '/';
        if ($cat !== null && $cat !== 'search') {
            $path .= $cat . '/';
        }
        $path  .= (string)$endpoint['name'];
        $method = (string)$endpoint['method'];

        // check if we have a POST method and a registered source to pass
        $source = $this->getOption('source');
        if ($method == 'POST' && $source !== null) {
            $params['source'] = $source;
        }
        
        // check arguments requirements
        $minargs = isset($endpoint['min_args'])
            ? (int)$endpoint['min_args']
            : count($endpoint->xpath('param[@required="true" or @required="1"]'));
        if (!$minargs && (isset($args[0]) && !is_array($args[0]))) {
            throw new Services_Twitter_Exception(
                $path . ' expects an array as unique parameter',
                self::ERROR_PARAMS,
                $path
            );
        }
        if (   $minargs && (!isset($args[0]) 
            || is_array($args[0]) 
            && $minargs > count($args[0]))
        ) {
            throw new Services_Twitter_Exception(
                'Not enough arguments for ' . $path,
                self::ERROR_PARAMS,
                $path
            );
        }
        $needargs = $minargs;

        $routing = array();
        if (isset($endpoint['routing'])) {
            $routing = explode('/', (string)$endpoint['routing']);
        }
        // now process arguments according to their definition in the xml 
        // mapping
        foreach ($endpoint->param as $param) {
            $pName      = (string)$param['name'];
            $pType      = (string)$param['type'];
            $pMaxLength = (int)$param['max_length'];
            $pMaxLength = $pMaxLength > 0 ? $pMaxLength : null;
            $pReq       = (string)$param['required'] == 'true' || $needargs;
            if ($pReq && !is_array($args[0])) {
                $arg = array_shift($args);
                $needargs--;
            } else if (isset($args[0][$pName])) {
                $arg = $args[0][$pName];
                $needargs--;
            } else {
                continue;
            }
            try {
                $this->validateArg($pName, $arg, $pType, $pMaxLength);
            } catch (Exception $exc) {
                throw new Services_Twitter_Exception(
                    $path . ': ' . $exc->getMessage(),
                    self::ERROR_PARAMS,
                    $path
                );
            }
            if (in_array(':' . $pName, $routing)) {
                $routing[array_search(':' . $pName, $routing)] = rawurlencode($arg);
            } else {
                if ($pName == 'id') {
                     if (count($routing) > 1) {
                         $params[$pName] = $arg;
                         if ($method == 'DELETE') {
                             $method = "POST";
                             $params['_method'] = 'DELETE';
                         }
                     } else {
                         $path .= '/' . $arg;
                     }
                } else {
                    if ($pType == 'string' && !$this->isUtf8($arg)) {
                        // iso-8859-1 string that we must convert to unicode
                        $arg = utf8_encode($arg);
                    }
                    if ($pType == 'image') {
                        // we have a file upload
                        $files[$pName] = $arg;
                    } else {
                        $params[$pName] = $arg;
                    }
                }
            }
        }
        $path = count($routing) ? implode('/', $routing) : $path;
        $uri .= $path . '.' . $this->getOption('format');
        return array($uri, $method, $params, $files);
    }

    // }}}
    // sendRequest() {{{

    /**
     * Send a request to the Twitter API.
     *
     * @param string $uri    The full URI to the API endpoint
     * @param array  $method The HTTP request method (GET or POST)
     * @param array  $args   The API endpoint arguments if any
     * @param array  $files  The API endpoint file uploads if any
     *
     * @throws Services_Twitter_Exception
     * @return object Instance of SimpleXMLElement 
     */
    protected function sendRequest($uri, $method = 'GET', array $args = array(),
        array $files = array()
    ) {
        try {
            $request = clone $this->getRequest();
            $request->setMethod($method);
            if ($method == 'POST') {
                foreach ($args as $key => $val) {
                    $request->addPostParameter($key, $val);
                }
                foreach ($files as $key => $val) {
                    $request->addUpload($key, $val);
                }
            } else {
                $prefix = '?';
                foreach ($args as $key => $val) {
                    $uri   .= $prefix . $key . '=' . urlencode($val);
                    $prefix = '&';
                }
            }
            $request->setUrl($uri);
            if ($this->user !== null && $this->pass !== null) {
                $request->setAuth($this->user, $this->pass);
            }
            $response = $request->send();
        } catch (HTTP_Request2_Exception $exc) {
            throw new Services_Twitter_Exception(
                $exc->getMessage(),
                $exc, // the original exception cause
                $uri
            );
        }
        return $response;
    }

    // }}}
    // validateArg() {{{

    /**
     * Check the validity of an argument (required, max length, type...).
     *
     * @param array $name      The argument name
     * @param array &$val      The argument value, passed by reference
     * @param array $type      The argument type
     * @param array $maxLength The argument maximum length (optional)
     *
     * @throws Services_Twitter_Exception
     * @return void
     */
    protected function validateArg($name, &$val, $type, $maxLength = null)
    {
        // check length if necessary
        if ($maxLength !== null && mb_strlen($val, 'UTF-8') > $maxLength) {
            throw new Exception(
                $name . ' must not exceed ' . $maxLength . ' chars',
                self::ERROR_PARAMS
            );
        }

        // type checks
        $msg = null;
        switch ($type) {
        case 'boolean':
            if (!is_bool($val)) {
                $msg = $name . ' must be a boolean';
            }
            // we modify the value by reference
            $val = $val ? 'true' : 'false';
            break;
        case 'integer':
            if (!is_numeric($val)) {
                $msg = $name . ' must be an integer';
            }
            break;
        case 'string':
            if (!is_string($val)) {
                $msg = $name . ' must be a string';
            }
            break;
        case 'date':
            if (is_numeric($val)) {
                // we have a timestamp
                $val = date('Y-m-d', $val);
            } else {
                $rx = '/^\d{4}\-\d{2}\-\d{2}$/';
                if (!preg_match($rx, $val)) {
                    $msg = $name . ' should be formatted YYYY-MM-DD';
                }
            }
            break;
        case 'id_or_screenname':
            if (!preg_match('/^[a-zA-Z0-9_\.]{1,16}$/', $val)) {
                $msg = $name . ' must be a valid id or screen name';
            }
            break;
        case 'device':
            $devices = array('none', 'sms', 'im');
            if (!in_array($val, $devices)) {
                $msg = $name . ' must be one of the following: ' 
                     . implode(', ', $devices);
            }
            break;
        case 'iso-639-1':
            if (strlen($val) != 2) {
                $msg = $name . ' must be a valid iso-639-1 language code';
            }
            break;
        case 'geocode':
            if (!preg_match('/^([-\d\.]+,){2}([-\d\.]+(km|mi)$)/', $val)) {
                $msg = $name . ' must be "latitide,longitude,radius(km or mi)"';
            }
            break;
        case 'lat':
        case 'long':
            if (!is_numeric($val)) {
                $msg = $name . ' must be a float';
            } else {
                $val = floatval($val);
                if ($type == 'lat' && ($val < -90 || $val > 90)) {
                    $msg = 'valid range for ' . $name . ' is -90.0 to +90.0';
                }
                if ($type == 'long' && ($val < -180 || $val > 180)) {
                    $msg = 'valid range for ' . $name . ' is -180.0 to +180.0';
                }
            }
            break;
        case 'color':
            if (!preg_match('/^([0-9a-f]{1,2}){3}$/i', $val)) {
                $msg = $name . ' must be an hexadecimal color code (eg. fff)';
            }
            break;
        case 'image':
            if (!file_exists($val) || !is_readable($val)) {
                $msg = $name . ' must be a valid image path';
            }
            // XXX we don't check the image type for now...
            break;
        case 'listid_or_slug':
            if (!preg_match('/^[0-9a-z]+(?:-?[0-9a-z]+)*$/', $val)) {
                $msg = $name . ' must be a valid list id or slug';
            }
            break;
        case 'mode':
            $modes = array('public', 'private');
            if (!in_array($val, $modes)) {
                $msg = $name . ' must be one of the following: '
                     . implode(', ', $modes);
            }
            break;
        }
        if ($msg !== null) {
            throw new Services_Twitter_Exception($msg, self::ERROR_PARAMS);
        }
    }

    // }}}
    // isUtf8() {{{

    /**
     * Check if the given string is a UTF-8 string or an iso-8859-1 one.
     *
     * @param string $str The string to check
     *
     * @return boolean Wether the string is unicode or not
     */
    protected function isUtf8($str)
    {
        return (bool)preg_match(
            '%^(?:
                  [\x09\x0A\x0D\x20-\x7E]            # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%xs',
            $str
        );
    }

    // }}}
}
