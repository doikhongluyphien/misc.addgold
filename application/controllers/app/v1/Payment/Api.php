<?php

namespace Payment;

use Payment\Http\Client\ClientInterface;
use Payment\Http\RequestInterface;
use Payment\Http\ResponseInterface;
use Payment\Logger\LoggerInterface;
use Payment\Logger\NullLogger;

class Api {

    /**
     * @var string
     */
    const VERSION = '';

    /**
     * @var Api
     */
    protected static $instance;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $defaultGraphVersion;

    /**
     * @param Client $http_client
     * @param Session $session A Facebook API session
     */
    public function __construct(
    ClientInterface $http_client) {

        $this->httpClient = $http_client;
        //$this->session = $session;
    }

    /**
     * @param string $app_id
     * @param string $app_secret
     * @param string $access_token
     * @return static
     */
    public static function init($app_id, $app_secret, $access_token) {
        $session = new Session($app_id, $app_secret, $access_token);
        $api = new static($this->httpClient, $session);
        static::setInstance($api);

        return $api;
    }

    /**
     * @return Api|null
     */
    public static function instance() {
        return static::$instance;
    }

    /**
     * @param Api $instance
     */
    public static function setInstance(Api $instance) {
        static::$instance = $instance;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function base64UrlEncode($string) {
        $str = strtr(base64_encode($string), '+/', '-_');
        $str = str_replace('=', '', $str);
        return $str;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $params
     * @return RequestInterface
     */
    public function prepareRequest(
    $path, $method = RequestInterface::METHOD_GET, array $params = array(), $post_array = true) {

        $request = $this->getHttpClient()->createRequest();        
        $request->setMethod($method);
        $request->setPostArray($post_array);
        $request->setGraphVersion($this->getDefaultGraphVersion());
        $request->setPath($path);

        if ($method === RequestInterface::METHOD_GET) {
            $params_ref = $request->getQueryParams();
        } else {
            $params_ref = $request->getBodyParams();
        }

        if (!empty($params)) {
            $params_ref->enhance($params);
        }
        return $request;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function executeRequest(RequestInterface $request) {
        $this->getLogger()->logRequest('debug', $request);        
        $response = $request->execute();
        $this->getLogger()->logResponse('debug', $response);
        $this->getLogger()->logFullRequest('request', $request, $response);
        return $response;
    }

    /**
     * @return string
     */
    public function getDefaultGraphVersion() {
        if ($this->defaultGraphVersion === null) {
            $match = array();
            if (preg_match("/^\d+\.\d+/", static::VERSION, $match)) {
                $this->defaultGraphVersion = $match[0];
            }
        }

        return $this->defaultGraphVersion;
    }

    /**
     * @param string $version
     */
    public function setDefaultGraphVersion($version) {
        $this->defaultGraphVersion = $version;
    }

    /**
     * Make graph api calls
     *
     * @param string $path Ads API endpoint
     * @param string $method Ads API request type
     * @param array $params Assoc of request parameters
     * @return ResponseInterface Graph API responses
     */
    public function call(
    $path, $method = RequestInterface::METHOD_GET, array $params = array(), $post_array = true) {

        $request = $this->prepareRequest($path, $method, $params, $post_array);

        return $this->executeRequest($request);
    }

    /**
     * @return Session
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @return Client
     */
    public function getHttpClient() {
        return $this->httpClient;
    }

}
