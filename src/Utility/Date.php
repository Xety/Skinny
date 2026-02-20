<?php

namespace Skinny\Utility;

class Date
{
    /**
     *  Transform the date into french format.
     *
     * @param string $date The date to transform.
     * @param string $format The format used to displayt he date.
     *
     * @return string The date formated in french.
     */
    public static function dateToFrench($date, $format) {
        $english_days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];
        $french_days = [
            'lundi',
            'mardi',
            'mercredi',
            'jeudi',
            'vendredi',
            'samedi',
            'dimanche'
        ];
        $english_months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $french_months = [
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        ];
        return str_replace(
            $english_months,
            $french_months,
            str_replace($english_days, $french_days, date($format, strtotime($date)))
        );
    }
}
