#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use App\Game\DiceGame;
use App\Parser\ArgumentParser;
use App\Exception\GameException;

try {
    // Skip first argument (script name)
    $args = array_slice($argv, 1);
    
    if (count($args) < 3) {
        throw new GameException(
            "At least 3 dice configurations required.\n" .
            "Example: php game.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3"
        );
    }

    $parser = new ArgumentParser();
    $diceConfigs = $parser->parse($args);
    
    $game = new DiceGame($diceConfigs);
    $game->play();
    
} catch (GameException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Unexpected error occurred: " . $e->getMessage() . "\n";
    exit(1);
} 