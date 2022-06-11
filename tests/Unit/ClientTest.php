<?php

namespace Tests\Unit;

use Bluelabs\Erwin\PHP\Client;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected $dotenv;

    public function getApiKey()
    {
        $this->dotenv = (($this->dotenv) ?: Dotenv::createImmutable(__DIR__.'/../../')->load());

        return $_ENV['API_KEY'] ?? $_SERVER['API_KEY'] ?? null;
    }

    /**
     * @param  array  $config
     *
     * @return Client
     */
    private function getClient(array $config = [])
    {
        $apiKey = $this->getApiKey();

        return new Client($apiKey, $config);
    }

    public function testGetApiKey()
    {
        $this->assertEquals($this->getApiKey(), $this->getClient()->getApiKey());
    }

    public function testGetSandboxHost()
    {
        $this->assertEquals(Client::DEFAULT_SANDBOX_HOST, $this->getClient()->getSandboxHost());
    }

    public function testGetDefaultHost()
    {
        $this->assertEquals(Client::DEFAULT_HOST, $this->getClient()->getDefaultHost());
    }

    public function testCreateClient()
    {
        $this->assertInstanceOf(Client::class, $this->getClient());
    }

    public function testGetSendDataUrl()
    {
        $this->assertEquals(implode('/', [
            Client::DEFAULT_HOST, Client::API_PATH, Client::API_VERSION, Client::STREAMS_ENDPOINT,
        ]), $this->getClient()->getSendDataUrl());
    }

    public function testClientRequest()
    {
        $response = $this->getClient()->sendRequest(['erwin' => true]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
