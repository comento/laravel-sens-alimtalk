<?php

namespace Comento\SensAlimtalk\Test\Fixtures;

use Comento\SensAlimtalk\SensAlimtalkMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    /**
     * @var callable|null
     */
    private $customize;

    public function __construct(?callable $customize = null)
    {
        $this->customize = $customize;
    }

    public function toSensAlimtalk($notifiable): SensAlimtalkMessage
    {
        $message = (new SensAlimtalkMessage())->templateCode('welcome')->content('안녕하세요');

        if ($this->customize) {
            $message = call_user_func($this->customize, $message);
        }

        return $message;
    }
}
