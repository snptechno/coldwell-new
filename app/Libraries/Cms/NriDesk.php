<?php

namespace App\Libraries\Cms;

/**
 * Allow-lists for the CMS "NRI desk enquiry form" block (Figma NRI Services – v2, 134:600).
 * The form and Site\Enquiry::nriEnquiry both read these, so a submitted value is only ever
 * stored when it is one the form offered.
 */
final class NriDesk
{
    /** "Interested in" choices. */
    public const INTERESTS = [
        'Buying a property',
        'Selling a property',
        'Leasing & property management',
        'Legal, tax & documentation',
        'Not sure yet',
    ];

    /** Value used when the visitor's city is not one of our franchise cities. */
    public const OTHER_CITY = 'Other';

    /**
     * Preferred-city choices: the cities with open franchise offices (live), sorted, plus "Other".
     *
     * @return list<string>
     */
    public static function cities(): array
    {
        $cities = array_map('strval', array_keys(Offices::cities(Offices::all())));
        sort($cities);
        $cities[] = self::OTHER_CITY;

        return $cities;
    }

    /** Max NRI desk submissions per IP address per window. */
    public const THROTTLE_MAX = 5;

    /** Throttler bucket for one visitor IP (hashed: no raw IPs in cache file names). */
    public static function throttleKey(string $ip): string
    {
        return 'public-nri-' . hash('sha256', $ip);
    }

    /** The submitted interest when it is on the list, else ''. */
    public static function interest(string $value): string
    {
        return in_array($value, self::INTERESTS, true) ? $value : '';
    }

    /** The submitted city when it is on the list, else ''. */
    public static function city(string $value): string
    {
        return in_array($value, self::cities(), true) ? $value : '';
    }
}
