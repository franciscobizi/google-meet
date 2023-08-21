<?php

namespace FBIZI\Tests;

use FBIZI\Calendar\GoogleCalendar;
use PHPUnit\Framework\TestCase;

final class GoogleCalendarTest extends TestCase
{
    private $calendar;
    private $token;
    private $timeZones;

    protected function setUp(): void
    {
        require_once dirname(__FILE__) . '/../config.php';
        $this->calendar = new GoogleCalendar(FB_G_APPLICATION_ID, FB_G_APPLICATION_REDIRECT_URL, FB_G_APPLICATION_SECRET);
    }

    protected function tearDown(): void {}

    public function testAuth()
    {
        $url = GoogleCalendar::auth('testtere', 'dfkdfkdfd');
        $permission_url = 'https://accounts.google.com/o/oauth2/auth?scope='
            . urlencode('https://www.googleapis.com/auth/calendar')
            . '&redirect_uri=testtere'
            . '&response_type=code&client_id=dfkdfkdfd&access_type=online';
        $this->assertEquals($url, $permission_url);
    }

    public function testGetAccessToken()
    {
        $code = 'code';
        $this->token = $this->calendar->getAccessToken($code);
        $this->assertIsString($this->token['token'] ?? null);
    }

    public function testGetUserCalendarTimezone()
    {
        $this->timeZones = $this->calendar->getCalendarTimezone();
        $this->assertIsString($this->timeZones['value'] ?? null);
    }

    public function testGetUserCalendarsList()
    {
        $cList = $this->calendar->getCalendarsList();
        $this->assertIsArray($cList['items'] ?? null);
    }

    public function testCreateUserEvent()
    {
        $args = [
            'title' => 'Event title',  // optional
            'description' => 'Event description',  // optional
            'start' => [
                'dateTime' => '2024-12-31T12:00:00',
                'timeZone' => $this->timeZones
            ],
            'end' => [
                'dateTime' => '2024-12-31T13:00:00',
                'timeZone' => $this->timeZones,
            ],
            'attendees' => [
                ['email' => 'test@test.com']
            ],
            'meet_id' => 'jdjhjdhdjdj',  // optional
        ];

        $event = GoogleCalendar::eventData($args);
        $eventId = $this->calendar->createEvent($event);
        $this->assertIsString($eventId['id'] ?? null);
    }

    public function testUpdateUserEvent()
    {
        $event_id = 'uniquestring';

        $args = [
            'title' => 'Event title',  // optional
            'description' => 'Event description',  // optional
            'start' => [
                'dateTime' => '2024-12-31T12:00:00',
                'timeZone' => $this->timeZones
            ],
            'end' => [
                'dateTime' => '2024-12-31T13:00:00',
                'timeZone' => $this->timeZones,
            ],
            'attendees' => [
                ['email' => 'test@test.com']
            ],
            'meet_id' => 'jdjhjdhdjdj',  // optional
        ];

        $event = GoogleCalendar::eventData($args);

        $eventId = $this->calendar->updateEvent($event_id, $event);
        $this->assertIsString($eventId['id'] ?? null);
    }

    public function testCancelUserEvent()
    {
        $event_id = 'uniquestring';
        $deleted = $this->calendar->cancelEvent($event_id);
        $this->assertEmpty($deleted);
    }
}
