<?php

namespace Comento\SensAlimtalk\Test;

use Comento\SensAlimtalk\Exceptions\CouldNotSendNotification;
use Comento\SensAlimtalk\SensAlimtalkChannel;
use Comento\SensAlimtalk\Test\Fixtures\FakeSensAlimtalk;
use Comento\SensAlimtalk\Test\Fixtures\TestNotifiable;
use Comento\SensAlimtalk\Test\Fixtures\TestNotification;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class SensAlimtalkChannelTest extends TestCase
{
    private function channel(MockHandler $mock): array
    {
        $sens = new FakeSensAlimtalk('access-key', 'secret-key', 'service-id', $mock);

        return [new SensAlimtalkChannel($sens), $sens];
    }

    public function test_send_returns_the_decoded_response_when_the_service_accepts_the_message()
    {
        $body = '{"statusCode":"202","statusName":"success"}';
        [$channel] = $this->channel(new MockHandler([new Response(202, [], $body)]));

        $response = $channel->send(new TestNotifiable('01000000000'), new TestNotification());

        $this->assertSame(['statusCode' => '202', 'statusName' => 'success'], $response);
    }

    public function test_send_routes_the_notification_to_the_notifiable_phone_number()
    {
        [$channel, $sens] = $this->channel(new MockHandler([new Response(202, [], '{"statusCode":"202"}')]));

        $channel->send(new TestNotifiable('01000000000'), new TestNotification());

        $payload = json_decode((string) $sens->lastRequest()->getBody(), true);

        $this->assertSame('welcome', $payload['templateCode']);
        $this->assertSame('01000000000', $payload['messages'][0]['to']);
    }

    public function test_send_keeps_an_explicit_recipient_set_on_the_message()
    {
        [$channel, $sens] = $this->channel(new MockHandler([new Response(202, [], '{"statusCode":"202"}')]));

        $notification = new TestNotification(function ($message) {
            return $message->to('01099999999');
        });

        $channel->send(new TestNotifiable('01000000000'), $notification);

        $payload = json_decode((string) $sens->lastRequest()->getBody(), true);

        $this->assertSame('01099999999', $payload['messages'][0]['to']);
    }

    public function test_send_throws_when_the_service_responds_with_a_non_accepted_status()
    {
        $body = '{"statusCode":"404","messages":[{"requestStatusCode":"FAIL"}]}';
        [$channel] = $this->channel(new MockHandler([new Response(200, [], $body)]));

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('SensAlimtalk was not sent: 404: FAIL');

        $channel->send(new TestNotifiable('01000000000'), new TestNotification());
    }

    public function test_send_throws_when_the_service_responds_with_an_http_error()
    {
        [$channel] = $this->channel(new MockHandler([new Response(400, [], '{"error":"bad request"}')]));

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('SensAlimtalk responded with an http error: 400');

        $channel->send(new TestNotifiable('01000000000'), new TestNotification());
    }

    public function test_send_throws_when_communication_fails()
    {
        [$channel] = $this->channel(new MockHandler([new \RuntimeException('connection refused')]));

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Communication with SensAlimtalk failed: 0: connection refused');

        $channel->send(new TestNotifiable('01000000000'), new TestNotification());
    }
}
