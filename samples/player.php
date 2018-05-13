<?php

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Models\Club;
use AustrianTennisLeague\Models\Player;
use AustrianTennisLeague\Models\Team;

require __DIR__ . "/../vendor/autoload.php";

// Configure client with state specific base URL
Client::configure("https://www.vorarlbergtennis.at/");

// Fetch player by ID
$player = Player::getById(12364);
print_r($player);
print_r($player->club());
