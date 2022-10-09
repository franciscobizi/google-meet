# Google Meet
Light Google Calendar API library for event creation with video conference (google meet). The library focus only on Calendar Events resource.

## Installation
Package is available on [Packagist](https://packagist.org/packages/fbizi/google-meet), you can install it using Composer.

```composer require fbizi/google-meet```

## Dependencies
- PHP 7.4+

## Basic usage
Before use this library make sure to register a Google Application and enable Calendar API on Google Cloude Console, so you can make APIs calls. Follow this [guide](https://developers.google.com/workspace/guides/get-started) if you don't have yet.

```ruby
use FBIZI\API\GoogleMeetApi;

$gmeet = new GoogleMeetApi(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET);
$permission_url = 'https://accounts.google.com/o/oauth2/auth?scope=' 
                  . urlencode('https://www.googleapis.com/auth/calendar') 
                  . '&redirect_uri=' . CLIENT_REDIRECT_URL 
                  . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online'; // call this to authenticate

if(isset($_GET['code']) && !empty($_GET['code'])){
    $code = sanitize_your_code($_GET['code']); // use your own function to sanitize the code due to security
    $gmeet->getAccessToken($code);
    // for event creation
    $timezone = $gmeet->getCalendarTimezone(); // to get user calendar timezone
    $attendees = [
        ["email" => "test@test.com"]
    ];
    $date_time = "2022-12-31T12:00:00";
    $meet_link = "uniquestring";
    $event = GoogleMeetApi::eventData($attendees, $date_time, $meet_link); // have three more optionals arguments, please look at this method
    $data = $gmeet->createEvent($event); // calendar_id is optionals argument
    // on successful will get the event resource
    
    // for event update
    $timezone = $gmeet->getCalendarTimezone(); // to get user calendar timezone
    $attendees = [
        ["email" => "test@test.com"]
    ];
    $date_time = "2022-12-31T12:00:00";
    $meet_link = "uniquestring";
    $event_id = $data['id'];
    $event = GoogleMeetApi::eventData($attendees, $date_time, $meet_link); // have three more optionals arguments, please look at this method
    $data = $gmeet->updateEvent($event_id, $event); // calendar_id is optionals argument
    // on successful will get the event resource
    
    // for event cancellation
    $gmeet->cancelEvent($data['id']);
    
    // explore other methods such as get event or calendar by id 
}

```
