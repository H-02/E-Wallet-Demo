<?php

namespace App\EWallet\Helper;
use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Facades\Facade;


class CommonHelper extends Facade
{
    public static function datetime($datetime_string, $format = 'd-M-Y h:i:s A', $user_timezone = 'Asia/Kolkata'): string
    {
        if (empty($datetime_string)) {
            return '-';
        }

        $new_datetime_string = new DateTime($datetime_string, new DateTimeZone($user_timezone));

        return $new_datetime_string->format($format);
    }

    public static function current_date(): string
    {
        return now()->toDateString();
    }

    public static function number($number, $default = null)
    {
        if (!empty($number)) {
            return round($number, 0);
        }

        return $default;
    }

    public static function searchBy($query)
    {
        $query = explode(',', $query);

        if (count($query) < 2) {
            throw new Exception("Invalid Query");
        }

        return ['search' => $query[0], 'search_on' => $query[1]];
    }
}