<?php

namespace Comento\SensAlimtalk\Test;

use Comento\SensAlimtalk\SensAlimtalkMessage;
use Exception;

class SensAlimtalkMessageTest extends TestCase
{
    public function test_to_array_builds_a_payload_for_every_recipient()
    {
        $payload = (new SensAlimtalkMessage())
            ->templateCode('welcome')
            ->to(['01000000000', '01011111111'])
            ->content('안녕하세요')
            ->toArray();

        $this->assertSame('@comento', $payload['plusFriendId']);
        $this->assertSame('welcome', $payload['templateCode']);
        $this->assertNull($payload['reserveTime']);
        $this->assertCount(2, $payload['messages']);
        $this->assertSame('01000000000', $payload['messages'][0]['to']);
        $this->assertSame('01011111111', $payload['messages'][1]['to']);
        $this->assertSame('안녕하세요', $payload['messages'][0]['content']);
        $this->assertSame('+82', $payload['messages'][0]['countryCode']);
        $this->assertTrue($payload['messages'][0]['useSmsFailover']);
    }

    public function test_to_array_wraps_a_single_recipient_into_an_array()
    {
        $payload = (new SensAlimtalkMessage())->to('01000000000')->content('hi')->toArray();

        $this->assertCount(1, $payload['messages']);
        $this->assertSame('01000000000', $payload['messages'][0]['to']);
    }

    public function test_to_array_replaces_variables_in_the_content()
    {
        $payload = (new SensAlimtalkMessage())
            ->to('01000000000')
            ->content('#{name}님, #{course} 신청이 완료되었습니다.')
            ->variables(['name' => '문범', 'course' => '백엔드'])
            ->toArray();

        $this->assertSame('문범님, 백엔드 신청이 완료되었습니다.', $payload['messages'][0]['content']);
    }

    public function test_to_array_appends_utm_source_to_button_links()
    {
        $payload = (new SensAlimtalkMessage())
            ->to('01000000000')
            ->content('hi')
            ->button(['linkMobile' => 'https://comento.kr', 'linkPc' => 'https://comento.kr?ref=a'])
            ->utmSource('utm_source=alimtalk')
            ->toArray();

        $button = $payload['messages'][0]['buttons'][0];

        $this->assertSame('https://comento.kr?utm_source=alimtalk', $button['linkMobile']);
        $this->assertSame('https://comento.kr?ref=a&utm_source=alimtalk', $button['linkPc']);
    }

    public function test_failover_content_falls_back_to_the_content_and_the_first_mobile_link()
    {
        $payload = (new SensAlimtalkMessage())
            ->to('01000000000')
            ->content('hi')
            ->button(['linkMobile' => 'https://comento.kr'])
            ->toArray();

        $this->assertSame("hi\n\nhttps://comento.kr", $payload['messages'][0]['failoverConfig']['content']);
    }

    public function test_use_sms_failover_defaults_to_the_config_value()
    {
        TestConfig::set([
            'sens-alimtalk.plus_friend_id' => '@comento',
            'sens-alimtalk.use_sms_failover' => false,
        ]);

        $payload = (new SensAlimtalkMessage())->to('01000000000')->content('hi')->toArray();

        $this->assertFalse($payload['messages'][0]['useSmsFailover']);
    }

    public function test_reserve_after_minute_rejects_reservations_within_ten_minutes()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Reservation cannot be requested within 10 minutes.');

        (new SensAlimtalkMessage())->reserveAfterMinute(10);
    }

    public function test_reserve_after_day_rejects_reservations_beyond_180_days()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Reservations can be made in up to 180 days.');

        (new SensAlimtalkMessage())->reserveAfterDay(181);
    }

    public function test_reserve_after_minute_sets_a_formatted_reserve_time()
    {
        $payload = (new SensAlimtalkMessage())
            ->to('01000000000')
            ->content('hi')
            ->reserveAfterMinute(30)
            ->toArray();

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $payload['reserveTime']);
    }

    /**
     * Guards against the dynamic property deprecation introduced in PHP 8.2.
     */
    public function test_link_setters_write_to_declared_properties()
    {
        $message = new SensAlimtalkMessage();

        $this->assertTrue(property_exists($message, 'linkMobile'));
        $this->assertTrue(property_exists($message, 'linkPc'));

        $message->linkMobile('https://m.comento.kr')->linkPc('https://comento.kr');

        $this->assertSame('https://m.comento.kr', $message->linkMobile);
        $this->assertSame('https://comento.kr', $message->linkPc);
    }
}
