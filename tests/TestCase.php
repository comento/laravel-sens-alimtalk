<?php

namespace Comento\SensAlimtalk\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TestConfig::set([
            'sens-alimtalk.plus_friend_id' => '@comento',
            'sens-alimtalk.use_sms_failover' => true,
        ]);
    }

    protected function tearDown(): void
    {
        TestConfig::reset();

        parent::tearDown();
    }
}
