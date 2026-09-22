<?php

namespace Comento\SensAlimtalk\Test\Fixtures;

use Comento\SensAlimtalk\SensAlimtalk;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * SensAlimtalk with the Guzzle client swapped for a mock handler,
 * keeping the real request building and signing logic under test.
 */
class FakeSensAlimtalk extends SensAlimtalk
{
    /**
     * @var MockHandler
     */
    private $mock;

    /**
     * @var array
     */
    public $transactions = [];

    public function __construct($accessKey, $secretKey, $serviceId, MockHandler $mock)
    {
        parent::__construct($accessKey, $secretKey, $serviceId);

        $this->mock = $mock;
    }

    protected function setClient()
    {
        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->transactions));

        return new Client([
            'base_uri' => 'https://sens.apigw.ntruss.com',
            'handler' => $stack,
        ]);
    }

    public function lastRequest()
    {
        return end($this->transactions)['request'];
    }
}
