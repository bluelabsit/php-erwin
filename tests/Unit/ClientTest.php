<?php

namespace Tests\Unit;

use Bluelabs\PHPErwin\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    public function testGetApiKey()
    {
        $client = new Client($_ENV['API_KEY']);
        $this->assertEquals($_ENV['API_KEY'], $client->getApiKey());
    }

    public function testGetSandboxHost()
    {
        $client = new Client($_ENV['API_KEY']);
        $this->assertEquals(Client::DEFAULT_SANDBOX_HOST, $client->getSandboxHost());
    }

    public function testGetDefaultHost()
    {
        $client = new Client($_ENV['API_KEY']);
        $this->assertEquals(Client::DEFAULT_HOST, $client->getDefaultHost());
    }

    public function testCreateClient()
    {
        $client = new Client($_ENV['API_KEY']);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testGetSendDataUrl()
    {
        $client = new Client($_ENV['API_KEY']);
        $this->assertEquals(implode('/', [
            Client::DEFAULT_HOST, Client::API_PATH, Client::API_VERSION, Client::STREAMS_ENDPOINT,
        ]), $client->getSendDataUrl());
    }

    public function testClientRequest()
    {
        $client   = new Client($_ENV['API_KEY']);
        $response = $client->sendRequest([
            'erwin' => true,
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
