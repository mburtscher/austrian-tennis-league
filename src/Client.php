<?php

namespace AustrianTennisLeague;

class Client
{
    private static $baseUrl = "https://www.oetv.at/";

    public static function configure(string $baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    public static function fetch(string $url): string
    {
        static $client;
        if ($client === null) {
            $client = new \GuzzleHttp\Client([
                "base_uri" => self::$baseUrl,
            ]);
        }

        return $client->get($url)->getBody()->getContents();
    }
}
