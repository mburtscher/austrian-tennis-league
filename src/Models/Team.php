<?php

namespace AustrianTennisLeague\Models;

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Exceptions\NotFoundException;
use Symfony\Component\DomCrawler\Crawler;

final class Team
{
    public static function fromHtml(int $id, string $overview): Team
    {
        $team = new static();
        $team->id = $id;
        $crawler = new Crawler($overview);

        $ballsLabel = $crawler->filterXPath("//b[text()='Ballmarke']");
        $team->balls = trim($ballsLabel->parents()->getNode(0)->childNodes->item(1)->textContent);

        $teamLeaderLabel = $crawler->filterXPath("//b[text()='Mannschaftsführer']");
        $teamLeaderText = $teamLeaderLabel->nextAll()->eq(1)->text();
        $team->teamLeader = trim(substr($teamLeaderText, 0, strpos($teamLeaderText, "|")));

        $teamLeaderDeputyLabel = $crawler->filterXPath("//b[text()='Stellvertreter']");
        if ($teamLeaderDeputyLabel->count() == 1) {
            $teamLeaderDeputyText = $teamLeaderDeputyLabel->nextAll()->eq(1)->text();
            $team->teamLeaderDeputy = trim(substr($teamLeaderDeputyText, 0, strpos($teamLeaderDeputyText, "|")));
        }

        $titleLabel = $crawler->filterXPath("//h2");
        $titleText = $titleLabel->getNode(0)->childNodes->item(2)->textContent;
        $team->title = trim(substr($titleText, strpos($titleText, ",") + 1));

        $res = [];
        if (preg_match("/Verein (\d+)/", $overview, $res)) {
            $team->clubId = (int)$res[1];
        }

        return $team;
    }

    public $id;
    public $title;
    public $clubId;
    public $balls;
    public $teamLeader;
    public $teamLeaderDeputy;

    public function club(): Club
    {
        return Club::findById($this->clubId);
    }

    public function players(): array
    {
        return Player::findByTeamId($this->id);
    }

    public static function getById(int $id): Team
    {
        $team = static::findById($id);

        if ($team === null) {
            throw new NotFoundException("Team with ID {$id} not found.");
        }

        return $team;
    }

    public static function findById(int $id): ?Team
    {
        $overview = Client::fetch("/liga/vereine/verein/mannschaften/mannschaft/m/{$id}.html");

        return static::fromHtml($id, $overview);
    }

    public static function findByClubId(int $clubId): array
    {
        $teams = [ ];

        $list = Client::fetch("/liga/vereine/verein/mannschaften/v/{$clubId}.html");

        $res = [ ];
        preg_match_all("/vereine\/verein\/mannschaften\/mannschaft\/m\/(\d+)\.html/", $list, $res);

        foreach (array_unique($res[1]) as $id) {
            $teams[] = static::getById($id);
        }

        return $teams;
    }
}
