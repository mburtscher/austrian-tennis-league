<?php

namespace AustrianTennisLeague\Tests\Models;

use AustrianTennisLeague\Models\Team;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    public function testFromHtml()
    {
        $html = file_get_contents(__DIR__ . "/data/team-overview.html");

        $team = Team::fromHtml(394607, $html);

        $this->assertEquals("Head ATP", $team->balls);
        $this->assertEquals("Alexander Zuggal", $team->teamLeader);
        $this->assertEquals("Philipp Hadler", $team->teamLeaderDeputy);
        $this->assertEquals(394607, $team->id);
        $this->assertEquals("Herren 1", $team->title);
        $this->assertEquals(30015, $team->clubId);
    }
}