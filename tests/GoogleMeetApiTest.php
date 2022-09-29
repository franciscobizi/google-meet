<?php

namespace FBIZI\Tests;

use PHPUnit\Framework\TestCase;
use FBIZI\API\GoogleMeetApi;

final class GoogleMeetApiTest extends TestCase
{
    private $gmapi;
    private $token;
    private $timeZones;
    private $calendar_id = "primary";
    private $event_title = "Consult";
    private $full_day_event = 0;
    private $event_time = "";
    private $user_timezone = "";


    protected function setUp(): void
    {
        $this->gmapi = new GoogleMeetApi(FB_G_APPLICATION_ID, FB_G_APPLICATION_REDIRECT_URL, FB_G_APPLICATION_SECRET);
    }

    protected function tearDown(): void
    {
    }

    public function testGetAccessToken()
    {
        $code = "code";
        $this->token = $this->gmapi->getAccessToken($code);
        $this->assertIsString($this->token['access_token']);
    }

    public function testGetUserCalendarTimezone()
    {
        $this->timeZones = $this->gmapi->getUserCalendarTimezone($this->token['access_token']);
        $this->assertIsString($this->timeZones['value']);
    }

    public function testGetUserCalendarsList()
    {
        $cList = $this->gmapi->GetCalendarsList($this->token['access_token']);
        $this->assertIsArray($cList['items']);
    }

    public function testCreateUserEvent()
    {
        $eventId = $this->gmapi->createUserEvent(
            $this->calendar_id,
            $this->event_title,
            $this->full_day_event,
            $this->event_time,
            $this->user_timezone,
            $this->token['access_token']
        );
        $this->assertIsInt($eventId['id']);
    }

    public function testUpdateUserEvent()
    {
        $eventId = $this->gmapi->updateUserEvent(
            $this->calendar_id,
            $this->event_title,
            $this->full_day_event,
            $this->event_time,
            $this->user_timezone,
            $this->token['access_token']
        );
        $this->assertIsInt($eventId['id']);
    }

    public function testCancelUserEvent()
    {
        $eventId = $this->gmapi->cancelUserEvent($this->calendar_id, $this->token['access_token']);
        $this->assertIsInt($eventId['id']);
    }

    public function testDeleteUserEvent()
    {
        $eventId = $this->gmapi->deleteUserEvent($this->calendar_id, $this->token['access_token']);
        $this->assertIsInt($eventId['id']);
    }
}
