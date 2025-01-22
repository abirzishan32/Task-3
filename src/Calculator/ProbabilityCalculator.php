<?php

namespace App\Calculator;

use App\Model\Dice;

class ProbabilityCalculator
{
    public function calculateWinProbability(Dice $dice1, Dice $dice2): float
    {
        $wins = 0;
        $total = 36; 
        
        foreach ($dice1->getValues() as $val1) {
            foreach ($dice2->getValues() as $val2) {
                if ($val1 > $val2) {
                    $wins++;
                }
            }
        }
        
        return $wins / $total;
    }

    public function generateProbabilityMatrix(array $diceConfigs): array
    {
        $matrix = [];
        $n = count($diceConfigs);
        
        for ($i = 0; $i < $n; $i++) {
            $matrix[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $matrix[$i][$j] = 0.5;
                } else {
                    $matrix[$i][$j] = $this->calculateWinProbability($diceConfigs[$i], $diceConfigs[$j]);
                }
            }
        }
        
        return $matrix;
    }
} 