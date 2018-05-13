<?php

namespace AustrianTennisLeague\Tests\Models;

use AustrianTennisLeague\Models\Club;
use PHPUnit\Framework\TestCase;

final class ClubTest extends TestCase
{
    public function testFromHtml()
    {
        $html = file_get_contents(__DIR__ . "/data/club-overview.html");

        $club = Club::fromHtml($html);

        $this->assertEquals(30015, $club->id);
        $this->assertEquals("TC Götzis", $club->name);
        $this->assertEquals(1970, $club->foundingYear);
    }
}
