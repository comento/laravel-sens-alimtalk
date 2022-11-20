<?php


namespace Comento\SensAlimtalk;

use Exception;
use Comento\SensAlimtalk\Exceptions\CouldNotSendNotification;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Notifications\Notification;

class SensAlimtalkChannel
{
    /**
     * @var SensAlimtalk
     */
    protected $sensAlimtalk;

    /**
     * SensAlimtalkChannel constructor.
     * @param SensAlimtalk $sensAlimtalk
     */
    public function __construct(SensAlimtalk $sensAlimtalk)
    {
        $this->sensAlimtalk = $sensAlimtalk;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        /**
         * @var SensAlimtalkMessage $message
         */
        $message = $notification->toSensAlimtalk($notifiable);

        if (!$message->to) {
            $message->to($notifiable->routeNotificationFor('sens_alimtalk', $notification));
        }

        try {
            $response = $this->sensAlimtalk->send($message->toArray());
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnHttpError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::serviceCommunicationError($exception);
        }

        $response = json_decode($response->getBody(), true);

        if ($response['statusCode'] != 202) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response;
    }
}
