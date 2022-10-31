# Google Calendar With Meet Conference
Light Google Calendar API library for managing events on Google Calendar. You can create event with video conference (google meet), update and cancel. The library focus only on Calendar Events resource.

## Installation
Package is available on [Packagist](https://packagist.org/packages/fbizi/google-meet), you can install it using Composer.

```composer require fbizi/google-meet```

## Dependencies
- PHP 7.4+

## Basic usage
Before use this library make sure to register a Google Application and enable Calendar API on Google Cloude Console, so you can make APIs calls. Follow this [guide](https://developers.google.com/workspace/guides/get-started) if you don't have yet.

```ruby
use FBIZI\Calendar\GoogleCalendar;

$calendar = new GoogleCalendar(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET);

// Build authenticate link so you can call it
$url = GoogleCalendar::auth(CLIENT_REDIRECT_URL, CLIENT_ID);

// OR
$url = 'https://accounts.google.com/o/oauth2/auth?scope=' 
                  . urlencode('https://www.googleapis.com/auth/calendar') 
                  . '&redirect_uri=' . CLIENT_REDIRECT_URL 
                  . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online';

if(isset($_GET['code']) && !empty($_GET['code'])){
    $code = sanitize_your_code($_GET['code']); // use your own function to sanitize the code due to security
    $calendar->getAccessToken($code);

    // for event creation
    $timezone = $calendar->getCalendarTimezone(); // to get user calendar timezone
    $attendees = [
        ["email" => "test@test.com"]
    ];

    $event_time = [
                "start" => [
                    "dateTime" => "2022-12-31T12:00:00",
                    "timeZone" => $timezone
                ],
                "end" => [
                    "dateTime" => "2022-12-31T13:00:00",
                    "timeZone" => $timezone
                ],
            ];

    $meet_id = "uniquestring";

    $event = GoogleCalendar::eventData($attendees, $event_time, $meet_id); // have three more optionals arguments, please look at this method

    $data = $calendar->createEvent($event); // calendar_id is optionals argument
    // on successful will get the event resource
    // retrive event id or meet link $data['id'] | $data['hangoutLink']
    
    // for event update
    $timezone = $calendar->getCalendarTimezone(); // to get user calendar timezone

    $attendees = [
        ["email" => "test@test.com"],
        ["email" => "test1@test.com"] // add 1 more attendee
    ];
    
    $event_id = $data['id'];

    $event = GoogleCalendar::eventData($attendees, $event_time, $meet_id); // have three more optionals arguments, please look at this method

    $data = $calendar->updateEvent($event_id, $event); // calendar_id is optionals argument
    // on successful will get the event resource
    
    // for event cancellation
    $calendar->cancelEvent($data['id']);
    
    // explore other methods such as get event or calendar by id 
}

```

## Donation
Methods :

- [Buy me a coffee](https://www.buymeacoffee.com/franciscobizi)

If this project help you reduce time to develop, you can give me a cup of coffee :)