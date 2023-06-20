<?php
use CodeIgniter\I18n\Time;

function load_key() {
    $fileText = file_get_contents('../.key.json');
    return json_decode($fileText, true)['key'];
}

function create_token(string ...$texts)
{
    $concat_text = '';
    foreach ($texts as $text) {
        $concat_text .= $text;
    }
    helper('UtilityFunctions');
    $key = load_key();
    $token_text = hash_hmac('sha256', $concat_text, $key);
    return $token_text;
}

function is_admin()
{
    return session()->get('user_access') == config('\User\Config\UserConfig')
        ->access_lvl_admin;
}

function get_ci_user_id(): ?int
{
    return session()->get('user_id');
}

function get_tim_user_id(): ?int
{
    $model = model('AccessTimModel');
    return $model->get_tim_user_id(get_ci_user_id());
}

function toSeconds(string $time): int
{
    $negative = false;
    if ($time[0] === '-') {
        $negative = true;
    }
    if (($time[0] === '-') or ($time[0] === '+')) {
        $time = substr($time, 1, -1);
    }
    // $date = Time::parse($time);
    $date = parseDuration($time);
    //$seconds = $date->hour * 3600 + $date->minute * 60 + $date->second;
    $seconds = $date['hour'] * 3600 + $date['minute'] * 60
            + $date['second'];
    if ($negative) {
        $seconds = -$seconds;
    }
    return $seconds;
}

function parseDuration(string $duration): array
{
    $beginHour = 0;
    $endHour = strpos($duration, ':') - 1;
    $beginMinute = $endHour + 2;
    $endMinute = strpos($duration, ':', $beginMinute) - 1;
    $beginSecond = $endMinute + 2;
    $endSecond = strlen($duration) - 1;
    $return['hour'] = substr($duration, $beginHour,
            $endHour - $beginHour + 1);
    $return['minute'] = substr($duration, $beginMinute,
            $endMinute - $beginMinute + 1);
    $return['second'] = substr($duration, $beginSecond,
            $endSecond - $beginSecond + 1);
    return array_map('intval', $return);
}

function get_hours_by_seconds(int $seconds): string
{
    $negative = $seconds < 0;
    $seconds = abs($seconds);
    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    $text = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    if ($negative) {
        return "-$text";
    }
    return $text;
}

function get_last_monday(Time $day): Time
{
    return $day->subDays($day->dayOfWeek - 1);
}

function get_time_period(string $firstDay, int $numberOfDay, int $timUserId,
    string $methodName, $instance): ?string
{
    $days = range(0, $numberOfDay - 1);
    $firstDay = Time::parse($firstDay);
    $daysDate = array_map(array($firstDay, 'addDays'), $days);
    $daysDateText = array_map(fn($day) => $day->toDateString(), $daysDate);
    $daysText = array_map(fn($day) => call_user_func_array(
        array($instance, $methodName), array($timUserId, $day)),
        $daysDateText);
    $daysTextFiltered = array_filter($daysText,
        fn($text) => !is_null($text));
    if (is_null($daysTextFiltered)) {
        return null;
    }
    $daysSeconds = array_map(array($instance, 'toSeconds'),
            $daysTextFiltered); 
    $seconds = array_reduce($daysSeconds,
            fn($carry, $day) => $carry + $day);
    $text = $instance->get_hours_by_seconds($seconds);
    return $text;
}
