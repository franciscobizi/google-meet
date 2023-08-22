<?php

namespace FBIZI\Calendar;

use FBIZI\Traits\ErrorsResponse;

/**
 * Google Calendar class
 *
 * @author Francisco Bizi
 */
class GoogleCalendar
{
    use ErrorsResponse;

    protected const BASE_URL = 'https://www.googleapis.com/';
    protected const ACCOUNT_URL = 'https://accounts.google.com/';

    public string $token = '';
    public string $refresh_token = '';
    private string $endpoint;
    private array|string $fields;
    private array $header = [];
    private string $calendar_id = 'primary';
    private string $client_id;
    private string $redirect_url;
    private string $client_secret;

    public function __construct(
        string $client_id,
        string $redirect_url,
        string $client_secret
    ) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
    }

    public static function auth(
        string $redirect_url,
        string $client_id,
        string $access_type = 'online',
        bool $redirect = false
    ): string {
        $url = '';
        if (!empty($redirect_url) && !empty($client_id)) {
            $url = self::ACCOUNT_URL . 'o/oauth2/auth?scope='
                . urlencode(self::BASE_URL . 'auth/calendar')
                . '&redirect_uri=' . $redirect_url
                . '&response_type=code&client_id=' . $client_id . '&access_type=' . $access_type;
            if ($redirect) {
                header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
                exit;
            }
        }
        return $url;
    }

    public function getAccessToken(string $code, bool $refresh = false): array
    {
        if (empty($code)) {
            return $this->show(401, "Missing authorization code or refresh token");
        }

        $this->endpoint = self::BASE_URL . 'oauth2/v4/token';
        $this->fields = 'client_id='
            . $this->client_id . '&client_secret='
            . $this->client_secret;

        if($refresh){
            $this->fields .= '&refresh_token=' . $code . '&grant_type=refresh_token';
        }else{
            $this->fields .= '&redirect_uri='
                . $this->redirect_url . '&code=' . $code . '&grant_type=authorization_code';
        }

        $token = $this->fetch('POST', 'Error : Failed to get access token');

        $this->token = $token['access_token'] ?? $this->token;
        $this->refresh_token = $token['refresh_token'] ?? $this->refresh_token;
        return $token;
    }

    public function getCalendarTimezone(): array
    {
        if (!empty($this->token)) {
            $this->endpoint = self::BASE_URL . 'calendar/v3/users/me/settings/timezone';
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch('GET', 'Error : Failed to get calendar timezone');
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
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
            return $this->fetch('GET', 'Error : Failed to get calendars list');
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public function getCalendarById(string $calendar_id): array
    {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL . "calendar/v3/calendars/{$this->calendar_id}";
            $this->header = array('Authorization: Bearer ' . $this->token, 'Accept: application/json');
            $this->fields = [];
            return $this->fetch('GET', 'Error : Failed to get calendars list');
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public function createEvent(array $event, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params['sendUpdates'] = 'all';
            $params['conferenceDataVersion'] = 1;
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL
                . "calendar/v3/calendars/{$this->calendar_id}/events?"
                . http_build_query($params);

            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = $event;

            if (!empty($this->fields)) {
                return $this->fetch('POST', 'Error : Failed to create event', true);
            }
            return $this->show(404, "Missing event data body");
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public function getEvent(string $event_id, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL . "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}";
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch('GET', 'Error : Failed to get event');
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public function updateEvent(string $event_id, array $event, string $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params['sendUpdates'] = 'all';
            $params['conferenceDataVersion'] = 1;
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL
                . "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}?"
                . http_build_query($params);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = $event;

            if (!empty($this->fields)) {
                return $this->fetch('PUT', 'Error : Failed to update event', true);
            }
            return $this->show(404, "Missing event data body");
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public function cancelEvent(string $event_id, $calendar_id = ''): array
    {
        if (!empty($this->token)) {
            $params = array();
            $params['sendUpdates'] = 'all';
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->endpoint = self::BASE_URL
                . "calendar/v3/calendars/{$this->calendar_id}/events/{$event_id}?"
                . http_build_query($params);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->fetch('DELETE', 'Error : Failed to cancel event');
        }
        return $this->show(401, "Missing token. Get token before procceed to this action.");
    }

    public static function eventData(array $args): array
    {
        $data = [
            'summary' => $args['title'] ?? 'Appointment',
            'description' => $args['description'] ?? 'Appointment with client',
            'start' => $args['start'] ?? [],
            'end' => $args['end'] ?? [],
            'attendees' => $args['attendees'] ?? [],
            'reminders' => [
                'useDefault' => true,
            ],
        ];

        if (isset($args['meet_id']) && !empty($args['meet_id'])) {
            $data['conferenceData'] = [
                'createRequest' => [
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ],
                    'requestId' => $args['meet_id']
                ]
            ];
        }

        return $data;
    }

    private function fetch(string $method, string $message = '', bool $json = false): array
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
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json == true ? json_encode($this->fields) : $this->fields);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
