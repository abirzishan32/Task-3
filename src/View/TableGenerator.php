<?php

namespace App\View;

class TableGenerator
{
    public function generateProbabilityTable(array $matrix, array $diceConfigs): string
    {
        $output = "\nProbability of winning for the user:\n\n";
        

        $maxWidth = max(array_map(function($dice) {
            return strlen((string)$dice);
        }, $diceConfigs));
        $colWidth = max($maxWidth + 2, 12); 
        

        $output .= str_pad("User dice v", $colWidth);
        foreach ($diceConfigs as $dice) {
            $output .= str_pad((string)$dice, $colWidth);
        }
        $output .= "\n";
        

        $totalWidth = $colWidth * (count($diceConfigs) + 1);
        $output .= str_repeat("-", $totalWidth) . "\n";
        

        for ($i = 0; $i < count($diceConfigs); $i++) {
            $output .= str_pad((string)$diceConfigs[$i], $colWidth);
            for ($j = 0; $j < count($diceConfigs); $j++) {
                if ($i === $j) {
                    $output .= str_pad("-", $colWidth);
                } else {
                    $output .= str_pad(sprintf("%.4f", $matrix[$i][$j]), $colWidth);
                }
            }
            $output .= "\n";
        }
        
        $output .= "- Each cell shows the probability of the row dice winning against the column dice\n";
        $output .= "- Values > 0.5 indicate favorable odds for the row dice\n";
        $output .= "- (-) indicates same-dice matchups\n\n";
        
        return $output;
    }
} 

