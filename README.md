# Google Calendar With Meet Conference
Lightweight Google Calendar API library for managing events on Google Calendar. You can create event with video conference (google meet), update and cancel. The library focus only on Calendar Events resource.

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

// Generate authenticate url link so you can call it
$url = GoogleCalendar::auth(string $client_redirect_url, string $client_id, string $access_type optional, bool $redirect optional);

// check if get the response code after calling the authentication url
if(isset($_GET['code']) && !empty($_GET['code'])){
    $code = $_GET['code'];
    $calendar->getAccessToken($code);

    // if you want to save the token and refresh_token somewhere for use later e.g DB just call this
    $token_to_save = $calendar->token
    $refresh_token_to_save = $calendar->refresh_token

    // if want to assign saved token and refresh_token just do this
    $calendar->token = $token_to_save; 
    $calendar->refresh_token = $refresh_token_to_save;

    // for event creation
    $timezone = $calendar->getCalendarTimezone(); // to get user calendar timezone

    $args = [
        "title" => "Event title", // optional
        "description" => "Event description", // optional
        "start" => [
                "dateTime" => "2024-12-31T12:00:00",
                "timeZone" => $timezone['value']
        ],
        "end" => [
            "dateTime" => "2024-12-31T13:00:00",
            "timeZone" => $timezone['value'],
        ],
        "attendees" => [
            ["email" => "test1@test.com"]
        ],
        "meet_id" => "jdjhjdhdjdj", // optional
    ];

    $event = GoogleCalendar::eventData(array $args);

    $data = $calendar->createEvent(array $event, string $calendar_id optional);
    // on successful will get the event resource
    // retrive event id or meet link $data['id'] | $data['hangoutLink']
    
    // for event update
    $timezone = $calendar->getCalendarTimezone(); // to get user calendar timezone

    $args['attendees'] = [
        ["email" => "test1@test.com"],
        ["email" => "test2@test.com"] // add 1 more attendee
    ];

    $event_id = $data['id'];

    $event = GoogleCalendar::eventData(array $args);

    $data = $calendar->updateEvent(string $event_id, array $event, string $calendar_id optional);
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