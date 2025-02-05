<?php

namespace FourOver;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class BaseApiClient {

    /**
     * @var array Envrionment map (https://api-users.4over.com/?page_id=22)
     */
    private const ENVIRONMENTS = [
        'LIVE' => 'https://api.4over.com',
        'SANDBOX' => 'https://sandbox-api.4over.com'
    ];

    /**
     * @var string Currently set environment (must be one of the const ENVRIONMENTS keys).
     */
    private string $currentEnvironment;

    /**
     * @var array Valid HTTP methods that 4over API supports
     */
    private const VALID_HTTP_METHODS = ['GET', 'DELETE', 'POST', 'PUT', 'PATCH'];

    /**
     * @var string Public key
     */
    private string $publicKey;

    /**
     * @var string Private key
     */
    private string $privateKey;
    
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    protected \Psr\Http\Client\ClientInterface $httpClient;

    /**
     * @param string $publicKey
     * @param string $privateKey
     * @param string|null $environmentType either 'LIVE' or 'SANDBOX'
     * @param \Psr\Http\Client\ClientInterface|null $httpClient Any HTTP client which is compatible with PSR
     */
    public function __construct(string $publicKey, string $privateKey, string $environmentType = null, \Psr\Http\Client\ClientInterface $httpClient = null)
    {
        $this->publicKey = $publicKey;
        
        $this->privateKey = $privateKey;

        if($httpClient == null) {
            $this->httpClient = new Client();
        } else {
            $this->httpClient = $httpClient;
        }

        $this->setEnvironment($environmentType);
    }

    /**
     * Sets an HTTP client. You might want to pass an http client that is configured for caching.
     * For an example see: https://github.com/Kevinrob/guzzle-cache-middleware
     *
     * @param \Psr\Http\Client\ClientInterface $httpClient
     * @return void
     */
    public function setHttpClient(\Psr\Http\Client\ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Sets environment type https://api-users.4over.com/?page_id=22
     *
     * @param string $type either 'LIVE' or 'SANDBOX'
     * @return void
     */
    private function setEnvironment(string $type) : void
    {
        $type = strtoupper($type);
        $baseUrl = \array_key_exists($type, self::ENVIRONMENTS) ? self::ENVIRONMENTS[$type] : null;

        if($baseUrl === null)
            throw new \InvalidArgumentException("Invalid envrionment type '$type'. Accepted types: 'LIVE', 'SANDBOX'.");

        $this->currentEnvironment = $type;
    }

    /**
     * @return string
     */
    private function getEnvironment() : string
    {
        return $this->currentEnvironment;
    }

    /**
     * Returns true if the API set to 'SANDBOX' mode or otherwise false.
     * 
     * @return bool
     */
    public function isSandboxMode() : bool
    {
        return $this->getEnvironment() === 'SANDBOX' ? true : false;
    }

    /**
     * @return string
     */
    private function getBaseUrl() : string
    {
        return self::ENVIRONMENTS[$this->getEnvironment()];
    }

    /**
     * @param string $method GET, POST, DELETE, PUT, PATCH.
     * @param string $path ex. "/printproducts/productsfeed?product_uuid={uuid}"
     * @param array $params
     * 
     * @throws \Exception
     * 
     * @return array JSON converted to array
     */
    public function request(string $method, string $path, array $params = []) : array
    {
        $request = $this->prepareRequest($method, $this->prepareUri($path));

        try {
            $response = $this->getHttpClient()->send($request, $params);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }

        if($response->getStatusCode() !== 200)
            throw new \Exception('Something went wrong...'); 

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $path ex. "/printproducts/productsfeed?product_uuid={uuid}"
     * 
     * @return string full URI ex. "https://api.4over.com/printproducts/productsfeed?product_uuid={uuid}"
     */
    private function prepareUri(string $path) : string
    {
        return $this->getBaseUrl() . $path;
    }

    /**
     * 4over API supports only certain HTTP methods. If given method is not supported then it's better
     * to throw an exception rather than continuing sending an API call that will fail anyways
     * 
     * @param string $method GET, POST, DELETE, PUT, PATCH etc.
     * @throws \InvalidArgumentException
     * 
     * @return void
     */
    private static function throwExceptionIfMethodNotAllowed(string $method) : void
    {
        if(!in_array($method, self::VALID_HTTP_METHODS))
            throw new \InvalidArgumentException("Method '$method' is not supported.");
    }

    /**
     * According to the 4over API docs (https://api-users.4over.com/?page_id=44)
     * GET and DELETE requests need to have the API credentials in the URL query
     * and POST, PUT, PATCH requests need to have the credentials as an Authorization HTTP header.
     * 
     * @param \Psr\Http\Client\ClientInterface Any http client compatible with Psr standards
     * @param string $method GET, POST, DELETE, PUT, PATCH.
     * @param string $uri ex. "https://api.4over.com/printproducts/productsfeed?product_uuid={uuid}" (entire uri not just '/printproducts/(...)')
     * 
     * @throws \InvalidArgumentException
     * 
     * @return \GuzzleHttp\Psr7\Request Prepared request with API credentials in proper place
     */
    private function prepareRequest(string $method, string $uri) : Request
    {
        self::throwExceptionIfMethodNotAllowed($method);

        $publicKey = $this->getPublicKey();
        $signature = $this->getSignature($method);

        $headers = [];

        // URL parameters
        $uri .= "?apikey=$publicKey&signature=$signature";
    
        // HTTP header
        $headers['Authorization'] = "API $publicKey:$signature";
        
        return new Request($method, $uri, $headers);
    }

    /**
     * @return \Psr\Http\Client\ClientInterface
     */
    private function getHttpClient() : \Psr\Http\Client\ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @param string $httpMethod GET, POST etc.
     * 
     * @return string Prepared, hashed, and ready to use signature
     */
    private function getSignature(string $httpMethod) : string 
    {
        self::throwExceptionIfMethodNotAllowed($httpMethod);

        return hash_hmac("sha256", $httpMethod, hash('sha256', $this->privateKey));
    }

    /**
     * @return string Public key
     */
    private function getPublicKey() : string
    {
        return $this->publicKey;
    }
}