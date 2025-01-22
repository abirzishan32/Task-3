<?php

namespace App\Parser;

use App\Model\Dice;
use InvalidArgumentException;

class ArgumentParser
{
    public function parse(array $args): array 
    {
        if (count($args) < 3) {
            throw new InvalidArgumentException("At least 3 dice configurations are required for the game.");
        }

        $diceConfigs = [];
        
        foreach ($args as $arg) {
            $values = array_map(function($val) {
                if (!is_numeric($val)) {
                    throw new InvalidArgumentException("Non-numeric value found: $val");
                }
                // Check if the number is a float
                if (floor((float)$val) != (float)$val) {
                    throw new InvalidArgumentException("Dice faces must be integers, found: $val");
                }
                return (int)$val;
            }, explode(',', $arg));
            
            $diceConfigs[] = new Dice($values);
        }
        
        return $diceConfigs;
    }
} 