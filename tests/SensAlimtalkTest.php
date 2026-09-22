<?php

namespace Comento\SensAlimtalk\Test;

use Comento\SensAlimtalk\Test\Fixtures\FakeSensAlimtalk;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class SensAlimtalkTest extends TestCase
{
    public function test_send_posts_a_signed_json_request_to_the_service_endpoint()
    {
        $mock = new MockHandler([new Response(202, [], '{"statusCode":"202"}')]);
        $sens = new FakeSensAlimtalk('access-key', 'secret-key', 'service-id', $mock);

        $sens->send(['templateCode' => 'welcome']);

        $request = $sens->lastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/alimtalk/v2/services/service-id/messages', $request->getUri()->getPath());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame('access-key', $request->getHeaderLine('x-ncp-iam-access-key'));
        $this->assertSame('{"templateCode":"welcome"}', (string) $request->getBody());

        $timestamp = $request->getHeaderLine('x-ncp-apigw-timestamp');
        $this->assertMatchesRegularExpression('/^\d{13}$/', $timestamp);

        $expected = base64_encode(hex2bin(hash_hmac(
            'sha256',
            "POST /alimtalk/v2/services/service-id/messages\n{$timestamp}\naccess-key",
            'secret-key'
        )));

        $this->assertSame($expected, $request->getHeaderLine('x-ncp-apigw-signature-v2'));
    }
}
