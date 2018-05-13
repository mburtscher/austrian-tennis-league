<?php

namespace AustrianTennisLeague\Models;

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Exceptions\NotFoundException;
use Symfony\Component\DomCrawler\Crawler;

class Player
{
    public static function fromHtml(string $overview): Player
    {
        $player = new static();
        $crawler = new Crawler($overview);

        $h1 = $crawler->filter("h1");

        $player->name = trim($h1->getNode(0)->childNodes->item(0)->textContent);

        $licenseNumberLabel = $crawler->filterXPath("//th[text()='Lizenznummer']");
        $licenseNumberField = $licenseNumberLabel->nextAll()->eq(0);
        $player->licenseNumber = (int)$licenseNumberField->text();

        $clubLabel = $crawler->filterXPath("//th[text()='Verein']");
        $clubField = $clubLabel->nextAll()->eq(0)->children()->eq(1);
        $player->clubId = (int)trim($clubField->text(), "() ");

        $genderLabel = $crawler->filterXPath("//th[text()='Geschlecht']");
        $genderField = $genderLabel->nextAll()->eq(0);
        $player->gender = $genderField->text() == "Männlich" ? Player::GENDER_MALE : Player::GENDER_FEMALE;

        $itnLabel = $crawler->filterXPath("//th[text()=' ITN Austria Spielstärke ']");
        $itnField = $itnLabel->nextAll()->eq(0);
        $player->itn = (float)str_replace(",", ".", $itnField->text());

        $yearOfBirthLabel = $crawler->filterXPath("//th[text()='Alter']");
        $yearOfBirthField = $yearOfBirthLabel->nextAll()->eq(0)->children()->eq(0);
        $player->yearOfBirth = (int)trim($yearOfBirthField->text(), "() ");

        return $player;
    }

    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";

    /**
     * @var int
     */
    public $licenseNumber;

    /**
     * @var string
     */
    public $gender;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $yearOfBirth;

    /**
     * @var int
     */
    public $clubId;

    /**
     * @var float
     */
    public $itn;

    public function club(): Club
    {
        return Club::findById($this->clubId);
    }

    public static function getById(int $id): Player
    {
        $club = static::findById($id);

        if ($club === null) {
            throw new NotFoundException("Player with ID {$id} not found.");
        }

        return $club;
    }

    public static function findById(int $id): ?Player
    {
        $overview = Client::fetch("/spieler/detail/mm/fed/VTV/pi/NU{$id}.html");

        return static::fromHtml($overview);
    }

    public static function getByClubId(int $clubId): array
    {
        $list = Client::fetch("/liga/vereine/verein/meldung/v/{$clubId}.html");
        return self::extractPlayersFromHtml($list);
    }

    public static function findByTeamId(int $teamId): array
    {
        $list = Client::fetch("/liga/vereine/verein/mannschaften/mannschaft/m/{$teamId}.html");
        return self::extractPlayersFromHtml($list);
    }

    private static function extractPlayersFromHtml(string $html): array
    {
        $res = [ ];
        preg_match_all("/spieler\/detail\/mm\/fed\/\w+\/pi\/NU(\d+)\.html/", $html, $res);

        foreach (array_unique($res[1]) as $id) {
            $players[] = static::getById($id);
        }

        return $players;
    }
}
