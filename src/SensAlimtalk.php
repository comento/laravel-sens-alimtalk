<?php


namespace Comento\SensAlimtalk;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class SensAlimtalk
{
    private $accessKey;
    private $secretKey;
    private $baseURL;
    private $targetURL;
    private $method = 'POST';
    private $timestamp = '';

    /**
     * SensAlimtalk constructor.
     *
     * @param $accessKey
     * @param $secretKey
     * @param $serviceId
     */
    public function __construct($accessKey, $secretKey, $serviceId)
    {
        $this->baseURL = 'https://sens.apigw.ntruss.com';
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->targetURL = '/alimtalk/v2/services/' . $serviceId . '/messages';
    }

    /**
     * @return string
     */
    private function signature() : string
    {
        $buffer = [];

        // Important - do not change these all lines down here ever!
        $buffer[] = strtoupper($this->method) . ' ' . $this->targetURL;
        $buffer[] = $this->timestamp;
        $buffer[] = $this->accessKey;
        $secretKey = utf8_encode($this->secretKey);
        $message = utf8_encode(implode("\n", $buffer));
        $hash = hex2bin(hash_hmac('sha256', $message, $secretKey));

        return base64_encode($hash);
    }

    /**
     * @return Client
     */
    protected function setClient()
    {
        return new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->baseURL,
            // You can set any number of default request options.
            'timeout' => 30.0,
        ]);
    }

    /**
     * @param $body
     * @return ResponseInterface
     */
    public function send($body)
    {
        $client = $this->setClient();

        $this->timestamp = (string)(int)round(microtime(true) * 1000);

        return $client->request(
            $this->method,
            $this->targetURL,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-ncp-apigw-timestamp' => $this->timestamp,
                    'x-ncp-iam-access-key' => $this->accessKey,
                    'x-ncp-apigw-signature-v2' => $this->signature(),
                ],
                'body' => json_encode($body)
            ]
        );
    }
}
