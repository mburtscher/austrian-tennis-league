<?php

namespace AustrianTennisLeague\Tests\Models;

use AustrianTennisLeague\Models\Player;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase
{
    public function testFromHtml()
    {
        $html = file_get_contents(__DIR__ . "/data/player-overview.html");

        $player = Player::fromHtml($html);

        $this->assertEquals("Matthias Burtscher", $player->name);
        $this->assertEquals(30015, $player->clubId);
        $this->assertEquals(Player::GENDER_MALE, $player->gender);
        $this->assertEquals(7.513, $player->itn);
        $this->assertEquals(50532, $player->licenseNumber);
        $this->assertEquals(1987, $player->yearOfBirth);
    }
}
