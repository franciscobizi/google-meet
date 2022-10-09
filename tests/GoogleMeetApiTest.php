<?php

namespace FBIZI\Tests;

use PHPUnit\Framework\TestCase;
use FBIZI\API\GoogleMeetApi;

final class GoogleMeetApiTest extends TestCase
{
    private $gmapi;
    private $token;
    private $timeZones;

    protected function setUp(): void
    {
        require_once dirname(__FILE__) ."/../config.php";
        $this->gmapi = new GoogleMeetApi(FB_G_APPLICATION_ID, FB_G_APPLICATION_REDIRECT_URL, FB_G_APPLICATION_SECRET);
    }

    protected function tearDown(): void
    {
    }

    public function testGetAccessToken()
    {
        $code = "code";
        $this->token = $this->gmapi->getAccessToken($code);
        $this->assertIsString($this->token);
    }

    public function testGetUserCalendarTimezone()
    {
        $this->timeZones = $this->gmapi->getCalendarTimezone();
        $this->assertIsString($this->timeZones['value']);
    }

    public function testGetUserCalendarsList()
    {
        $cList = $this->gmapi->getCalendarsList();
        $this->assertIsArray($cList['items']);
    }

    public function testCreateUserEvent()
    {
        $event = [ 'start_time' => '2022-12-31T15:00:00', 'end_time' => '2022-12-31T16:00:00' ];
        $eventId = $this->gmapi->createEvent($event);
        $this->assertIsInt($eventId['id']);
    }

    public function testUpdateUserEvent()
    {
        $event_id = 2;
        $event = [ 'start_time' => '2022-12-31T15:00:00', 'end_time' => '2022-12-31T16:00:00' ];
        $eventId = $this->gmapi->updateEvent($event_id, $event);
        $this->assertIsInt($eventId['id']);
    }

    public function testCancelUserEvent()
    {
        $event_id = 2;
        $eventId = $this->gmapi->cancelEvent($event_id);
        $this->assertIsInt($eventId['id']);
    }
}
