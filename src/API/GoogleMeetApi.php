<?php

namespace FBIZI\API;

/**
 * Google Meet API Class
 */
class GoogleMeetApi
{
    private $token;
    private $calendar_id = "primary";
    private $event_title = "Consult";
    private $full_day_event = 0;
    private $event_time = "";
    private $user_timezone = "";
    private $endpoint = "";
    private $base_endpoint = "https://www.googleapis.com/";
    private $fields;
    private $header = [];

    public function __construct(
        private string|int $client_id,
        private string $redirect_url,
        private string $client_secret
    ) {
    }

    public function getAccessToken($code = "")
    {
        if (!empty($code)) {
            $this->endpoint = $this->base_endpoint . 'oauth2/v4/token';
            $this->fields = 'client_id=' .
                $this->client_id . '&redirect_uri=' .
                $this->redirect_url . '&client_secret=' .
                $this->client_secret . '&code=' . $code . '&grant_type=authorization_code';
            $token = $this->call();
            if (isset($token['access_token'])) {
                $this->token = $token;
            }
        }
    }

    public function getUserCalendarTimezone()
    {
        if (!empty($this->token)) {
            $this->endpoint = $this->base_endpoint . 'calendar/v3/users/me/settings/timezone';
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            $data = $this->call('Error : Failed to get calendar timezone');
            if (isset($data['value'])) {
                $this->user_timezone = $data['value'];
            }
        }
    }

    public function getUserCalendarsList()
    {
        if (!empty($this->token)) {
            $parameters = array();
            $parameters['fields'] = 'items(id,summary,timeZone)';
            $parameters['minAccessRole'] = 'owner';
            $this->endpoint = $this->base_endpoint . 'calendar/v3/users/me/calendarList?' . http_build_query($parameters);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->call('Error : Failed to get calendars list');
        }
    }

    public function createUserEvent(
        $calendar_id = '',
        $event_title = '',
        $full_day_event = 0,
        $event_time = []
    ) {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->event_title = !empty($event_title) ? $event_title : $this->event_title;
            $this->full_day_event = !empty($full_day_event) ? $full_day_event : $this->full_day_event;
            $this->event_time = $event_time;

            $this->endpoint = $this->base_endpoint . 'calendar/v3/calendars/' . $this->calendar_id . '/events';
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = array('summary' => "Booked event");
            if ($this->full_day_event == 1) {
                //[ 'event_date' => '2016-12-31' ];
                $this->fields['start'] = array('date' => $this->event_time['event_date']);
                $this->fields['end'] = array('date' => $this->event_time['event_date']);
            } else {
                //[ 'start_time' => '2016-12-31T15:00:00', 'end_time' => '2016-12-31T16:00:00' ];
                $this->fields['start'] = array('dateTime' => $this->event_time['start_time'], 'timeZone' => $this->user_timezone);
                $this->fields['end'] = array('dateTime' => $this->event_time['end_time'], 'timeZone' => $this->user_timezone);
            }

            return $this->call('Error : Failed to create event');
        }
    }

    public function updateUserEvent(
        $calendar_id = '',
        $event_title = '',
        $full_day_event = 0,
        $event_time = [],
        $event_id = 0
    ) {
        if (!empty($this->token)) {
            $this->calendar_id = !empty($calendar_id) ? $calendar_id : $this->calendar_id;
            $this->event_title = !empty($event_title) ? $event_title : $this->event_title;
            $this->full_day_event = !empty($full_day_event) ? $full_day_event : $this->full_day_event;
            $this->event_time = $event_time;

            $this->endpoint = $this->base_endpoint . 'calendar/v3/calendars/' . $this->calendar_id . '/events';
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = array('summary' => "Booked event");
            if ($this->full_day_event == 1) {
                //[ 'event_date' => '2016-12-31' ];
                $this->fields['start'] = array('date' => $this->event_time['event_date']);
                $this->fields['end'] = array('date' => $this->event_time['event_date']);
            } else {
                //[ 'start_time' => '2016-12-31T15:00:00', 'end_time' => '2016-12-31T16:00:00' ];
                $this->fields['start'] = array('dateTime' => $this->event_time['start_time'], 'timeZone' => $this->user_timezone);
                $this->fields['end'] = array('dateTime' => $this->event_time['end_time'], 'timeZone' => $this->user_timezone);
            }

            return $this->call('Error : Failed to update event');
        }
    }

    public function cancelUserEvent($calendar_id = '', $event_id = 0)
    {
        if (!empty($this->token)) {
            $this->endpoint = $this->base_endpoint; //'calendar/v3/users/me/calendarList?' . http_build_query($parameters);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->call('Error : Failed to cancel event');
        }
    }

    public function deleteUserEvent($calendar_id = '', $event_id = 0)
    {
        if (!empty($this->token)) {
            $this->endpoint = $this->base_endpoint; //'calendar/v3/users/me/calendarList?' . http_build_query($parameters);
            $this->header = array('Authorization: Bearer ' . $this->token);
            $this->fields = [];
            return $this->call('Error : Failed to delete event');
        }
    }

    private function call($message = "")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        if(!empty($this->fields)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->fields));
        }
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200)
            throw new \Exception($message);

        return $data;
    }
}
