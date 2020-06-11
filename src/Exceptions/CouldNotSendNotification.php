<?php


namespace Comento\SensAlimtalk\Exceptions;


use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnHttpError(ClientException $response) {
        return new static("SensAlimtalk responded with an http error: {$response->getResponse()->getStatusCode()}: {$response->getMessage()}");
    }

    public static function serviceRespondedWithAnError($response) {
        return new static("SensAlimtalk was not sent: {$response['statusCode']}: {$response['messages'][0]['requestStatusCode']}");
    }

    public static function serviceCommunicationError($response) {
        return new static("Communication with SensAlimtalk failed: {$response->getCode()}: {$response->getMessage()}");
    }
}