<?php

namespace Comento\SensAlimtalk\Test\Fixtures;

class TestNotifiable
{
    public $phone;

    public function __construct($phone)
    {
        $this->phone = $phone;
    }

    public function routeNotificationFor($driver, $notification = null)
    {
        return $this->phone;
    }
}
