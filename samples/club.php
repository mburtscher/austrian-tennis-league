<?php

use AustrianTennisLeague\Client;
use AustrianTennisLeague\Models\Club;
use AustrianTennisLeague\Models\Player;
use AustrianTennisLeague\Models\Team;

require __DIR__ . "/../vendor/autoload.php";

// Configure client with state specific base URL
Client::configure("https://www.vorarlbergtennis.at/");

// Fetch club by ID
$club = Club::getById(30015);
print_r($club);
print_r($club->players());
print_r($club->teams());
