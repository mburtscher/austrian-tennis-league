<?php

namespace AustrianTennisLeague\Models;

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Exceptions\NotFoundException;
use Symfony\Component\DomCrawler\Crawler;

final class Club
{
    public static function fromHtml(string $overview): Club
    {
        $club = new static();
        $crawler = new Crawler($overview);

        $h1 = $crawler->filter("h1");

        $club->name = trim($h1->getNode(0)->childNodes->item(2)->textContent);

        $res = [ ];
        if (preg_match("/Gründungsjahr:\\s+(\d+)/", $overview, $res)) {
            $club->foundingYear = (int)$res[1];
        }

        $res = [ ];
        if (preg_match("/Vereinsnummer:\\s+(\d+)/", $overview, $res)) {
            $club->id = (int)$res[1];
        }

        return $club;
    }

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $foundingYear;

    /**
     * @return Player[]
     */
    public function players(): array
    {
        return Player::getByClubId($this->id);
    }

    public function teams(): array
    {
        return Team::findByClubId($this->id);
    }

    public static function getById(int $id): Club
    {
        $club = static::findById($id);

        if ($club === null) {
            throw new NotFoundException("Club with ID {$id} not found.");
        }

        return $club;
    }

    public static function findById(int $id): ?Club
    {
        $overview = Client::fetch("liga/vereine/verein/v/{$id}.html");

        return static::fromHtml($overview);
    }
}
