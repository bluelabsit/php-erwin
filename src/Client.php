<?php

namespace Bluelabs\Erwin\PHP;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

/**
 * Client class
 */
class Client
{

    /**
     * Version
     */
    const VERSION = '1.3.1';

    /**
     * Default host url
     */
    const DEFAULT_HOST = 'https://api.erwin.bluelabs.it';

    /**
     * Default sandbox url
     */
    const DEFAULT_SANDBOX_HOST = 'https://sandbox.api.erwin.bluelabs.it';

    /**
     * ERWIN API version
     */
    const API_VERSION = 'v1';

    /**
     * ERWIN API Path prefix
     */
    const API_PATH = 'api';

    /**
     * ERWIN Streams endpoint
     */
    const STREAMS_ENDPOINT = 'streams';

    /**
     * ERWIN User agent
     */
    const DEFAULT_USER_AGENT = 'Erwin PHP Client v'.self::VERSION;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param  string  $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return array|mixed
     */
    public function getConfig($name = null)
    {
        if ($name === 'api_key') {
            return $this->apiKey;
        }

        return $this->config[$name] ?? (isset($name) ? null : $this->config);
    }

    /**
     * @param  array  $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param  string  $apiKey
     * @param  array  $config
     */
    public function __construct($apiKey, $config = [])
    {
        $this->apiKey = $apiKey;
        $this->config = $config;
    }

    /**
     * @param  string  $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        $key = Str::snake($name);

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return null;
    }

    /**
     * @param  string  $name
     * @param  mixed|null  $value
     */
    public function __set($name, $value)
    {
        $key = Str::snake($name);

        if ($key === 'api_key') {
            $this->apiKey = $value;

            return;
        }

        $this->config[$key] = $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient ?: $this->httpClient = new \GuzzleHttp\Client();
    }

    /**
     * Send Data to Erwin and return the JSON response body
     *
     * @param  array|string  $data  Data to send (array or json string)
     * @param  array  $options
     *
     * @return mixed
     *
     * @throws GuzzleException
     */
    public function sendData($data, $options = [])
    {
        $response = $this->sendRequest($data, $options);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Send the data and returns the response
     *
     * @param  array|string  $data
     * @param  array  $options
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function sendRequest(array $data, array $options = [])
    {
        $options = array_merge($this->config, $options);

        if ( ! is_string($data)) {
            $data = json_encode([
                'data' => $data,
            ]);
        }

        $url = $this->getSendDataUrl($options);

        return $this->getHttpClient()->post($url, array_merge([
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$this->getApiKey(),
                'User-Agent'    => $this->getUserAgent(),
            ],
            'body'    => $data,
        ], $options));
    }

    /**
     * @return array|string
     */
    public function getUserAgent()
    {
        return $this->getConfig('user_agent') ?: static::DEFAULT_USER_AGENT;
    }

    /**
     * @return array|string
     */
    public function getSandboxHost()
    {
        return $this->getConfig('sandbox_host') ?: static::DEFAULT_SANDBOX_HOST;
    }

    /**
     * @return string
     */
    public function getDefaultHost()
    {
        return $this->getConfig('default_host') ?: static::DEFAULT_HOST;
    }

    /**
     * @return string
     */
    protected function getHost()
    {
        return $this->sandbox ? $this->getSandboxHost() : $this->getDefaultHost();
    }

    /**
     * @param  array  $options
     *
     * @return string
     */
    public function getSendDataUrl(&$options = [])
    {
        $query = [];
        if ( ! empty($options['with_data'])) {
            $query['with_data'] = 1;
            unset($options['with_data']);
        }
        if ( ! empty($options['with_request'])) {
            $query['with_request'] = 1;
            unset($options['with_request']);
        }

        $endpoint = implode('/', [
            $this->getHost(),
            $this->getApiPath(),
            $this->getApiVersion(),
            $this->getStreamsEndpoint(),
        ]);

        return rtrim($endpoint.'?'.http_build_query($query), '?&');
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->getConfig('api_version') ?: static::API_VERSION;
    }

    /**
     * @return string
     */
    public function getApiPath()
    {
        return $this->getConfig('api_path') ?: static::API_PATH;
    }

    /**
     * @return string
     */
    protected function getStreamsEndpoint()
    {
        return $this->getConfig('streams_endpoint') ?: static::STREAMS_ENDPOINT;
    }
}
