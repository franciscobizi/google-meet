<?php

namespace FBIZI\Calendar;

/**
 * Google Calendar class
 *
 * @author Francisco Bizi
 */
class GoogleCalendar
{
    protected const BASE_URL = "https://www.googleapis.com/";
    public string $token;
    private string $endpoint;
    private array | string $fields;
    private array $header = [];
    private string $calendar_id = "primary";

    public function __construct(
        private string $client_id,
        private string $redirect_url,
        private string $client_secret
    ) {
    }

    public function getAccessToken($code = ""): void
    {
        if (!empty($code)) {
            $this->endpoint = self::BASE_URL . 'oauth2/v4/token';
            $this->fields = 'client_id=' .
                $this->client_id . '&redirect_uri=' .
                $this->redirect_url . '&client_secret=' .
                $this->client_secret . '&code=' . $code . '&grant_type=authorization_code';
            $token = $this->fetch("POST", 'Error : Failed to get access token');
            if (isset($token['access_token'])) {
                $this->token = $token['access_token'];
            }
        }
    }

    public function getCalendarTimezone(): array
    {
        if (!empty($this->token)) {
            $this->endpoint = self::BASE_URL . 'calendar/v3/users/me/settings/timezone';
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch("GET", 'Error : Failed to get calendar timezone');
        }
    }

    public function getCalendarsList(): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params['fields'] = 'items(id,summary,timeZone)';
            $params['minAccessRole'] = 'owner';
            $this->endpoint = self::BASE_URL . 'calendar/v3/users/me/calendarList?' . http_build_query($params);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch("GET", 'Error : Failed to get calendars list');
        }
    }

    public function getCalendarById(string $calendar_id): array
    {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL . "calendar/v3/calendars/{$this->calendar_id}";
            $this->header = array('Authorization: Bearer ' . $this->token, 'Accept: application/json');
            $this->fields = [];
            return $this->fetch("GET", 'Error : Failed to get calendars list');
        }
        return ['message' => "Get Google Calendar token before add an event"];
    }

    public function createEvent(array $event, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params["sendUpdates"] = "all";
            $params["conferenceDataVersion"] = 1;
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL .
                "calendar/v3/calendars/{$this->calendar_id}/events?" .
                http_build_query($params);

            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = $event;

            if (!empty($this->fields)) {
                return $this->fetch("POST", 'Error : Failed to create event', true);
            }
            return ['message' => "Missing event data body"];
        }
        return ['message' => "Get Google Calendar token before add an event"];
    }

    public function getEvent(string $event_id, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL . "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}";

            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch("GET", 'Error : Failed to get event');
        }
        return ['message' => "Get Google Calendar token before add an event"];
    }

    public function updateEvent(string $event_id, array $event, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params["sendUpdates"] = "all";
            $params["conferenceDataVersion"] = 1;
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL .
                "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}?" .
                http_build_query($params);

            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = $event;

            if (!empty($this->fields)) {
                return $this->fetch("PUT", 'Error : Failed to update event', true);
            }
            return ['message' => "Missing event data body"];
        }
        return ['message' => "Get Google Calendar token before add an event"];
    }

    public function cancelEvent(string $event_id, $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params["sendUpdates"] = "all";
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL .
                "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}?" .
                http_build_query($params);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch("DELETE", 'Error : Failed to cancel event');
        }
        return ['message' => "Get Google Calendar token before add an event"];
    }

    public static function eventData(
        array $attendees,
        array $date_time,
        string $meet_id,
        string $event_title = "",
        string $description = ""
    ): array {

        return [
            'summary' => !empty($event_title) ? $event_title : "Consultation Appointment",
            'description' => !empty($description) ? $description : "Consultation appointment with patient",
            'start' => $date_time['start'],
            'end' => $date_time['end'],
            'attendees' => $attendees,
            'reminders' => [
                'useDefault' => true,
            ],
            "conferenceData" => [
                "createRequest" => [
                    "conferenceSolutionKey" => [
                        "type" => "hangoutsMeet"
                    ],
                    "requestId" => $meet_id
                ]
            ],

        ];
    }

    private function fetch(string $method, string $message = "", bool $json = false): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($this->header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json == true ? json_encode($this->fields) : $this->fields);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json == true ? json_encode($this->fields) : $this->fields);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }

        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        switch ($http_code) {
            case 200:
                return $data;
                break;
            case 204:
                return [];
                break;
            default:
                return ['http_code' => $http_code, 'error' => $data, 'message' => $message];
                break;
        }
    }
}
