<?php

namespace FBIZI\Tests;

use PHPUnit\Framework\TestCase;
use FBIZI\Calendar\GoogleCalendar;

final class GoogleCalendarTest extends TestCase
{
    private $calendar;
    private $token;
    private $timeZones;

    protected function setUp(): void
    {
        require_once dirname(__FILE__) ."/../config.php";
        $this->calendar = new GoogleCalendar(FB_G_APPLICATION_ID, FB_G_APPLICATION_REDIRECT_URL, FB_G_APPLICATION_SECRET);
    }

    protected function tearDown(): void
    {
    }

    public function testGetAccessToken()
    {
        $code = "code";
        $this->token = $this->calendar->getAccessToken($code);
        $this->assertIsString($this->token);
    }

    public function testGetUserCalendarTimezone()
    {
        $this->timeZones = $this->calendar->getCalendarTimezone();
        $this->assertIsString($this->timeZones['value']);
    }

    public function testGetUserCalendarsList()
    {
        $cList = $this->calendar->getCalendarsList();
        $this->assertIsArray($cList['items']);
    }

    public function testCreateUserEvent()
    {
        $attendees = [
            ["email" => "test@test.com"]
        ];
        $event_time = [
                    "start" => [
                        "dateTime" => "2022-12-31T12:00:00",
                        "timeZone" => $this->timeZones
                    ],
                    "end" => [
                        "dateTime" => "2022-12-31T13:00:00",
                        "timeZone" => $this->timeZones
                    ],
                ];
        $meet_id = "uniquestring";
        $event = GoogleCalendar::eventData($attendees, $event_time, $meet_id);
        $eventId = $this->calendar->createEvent($event);
        $this->assertIsString($eventId['id']);
    }

    public function testUpdateUserEvent()
    {
        $event_id = "uniquestring";
        $attendees = [
            ["email" => "test@test.com"]
        ];
        $event_time = [
                    "start" => [
                        "dateTime" => "2022-12-31T12:00:00",
                        "timeZone" => $this->timeZones
                    ],
                    "end" => [
                        "dateTime" => "2022-12-31T13:00:00",
                        "timeZone" => $this->timeZones
                    ],
                ];
        $meet_id = "uniquestring";
        $event = GoogleCalendar::eventData($attendees, $event_time, $meet_id);
        $eventId = $this->calendar->updateEvent($event_id, $event);
        $this->assertIsString($eventId['id']);
    }

    public function testCancelUserEvent()
    {
        $event_id = "uniquestring";
        $deleted = $this->calendar->cancelEvent($event_id);
        $this->assertEmpty($deleted);
    }
}
