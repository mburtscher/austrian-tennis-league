<?php

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Models\Club;
use AustrianTennisLeague\Models\Player;
use AustrianTennisLeague\Models\Team;

require __DIR__ . "/../vendor/autoload.php";

// Configure client with state specific base URL
Client::configure("https://www.vorarlbergtennis.at/");

// Fetch team by ID
$team = Team::getById(394607);
print_r($team);
print_r($team->club());
print_r($team->players());
