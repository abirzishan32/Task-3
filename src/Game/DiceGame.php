<?php

namespace App\Game;

use App\Model\Dice;
use App\Random\FairRandomGenerator;
use App\Calculator\ProbabilityCalculator;
use App\View\TableGenerator;
use App\Exception\GameException;

class DiceGame
{
    private array $diceConfigs;
    private FairRandomGenerator $randomGenerator;
    private ProbabilityCalculator $probabilityCalculator;
    private TableGenerator $tableGenerator;
    private ?Dice $computerDice = null;
    private ?Dice $playerDice = null;

    public function __construct(array $diceConfigs)
    {
        $this->diceConfigs = $diceConfigs;
        $this->randomGenerator = new FairRandomGenerator();
        $this->probabilityCalculator = new ProbabilityCalculator();
        $this->tableGenerator = new TableGenerator();
    }

    public function play(): void
    {
        echo "Let's determine who makes the first move.\n";
        $computerFirst = $this->determineFirstPlayer();
        
        if ($computerFirst) {
            $this->computerSelectsDice();
            $this->playerSelectsDice();
        } else {
            $this->playerSelectsDice();
            $this->computerSelectsDice();
        }
        
        $computerThrow = $this->performThrow("Computer");
        $playerThrow = $this->performThrow("Player");
        
        $this->determineWinner($computerThrow, $playerThrow);
    }

    private function determineFirstPlayer(): bool
    {
        $key = $this->randomGenerator->generateSecretKey();
        $computerChoice = $this->randomGenerator->generateUniformRandom(0, 1);
        $hmac = $this->randomGenerator->calculateHMAC((string)$computerChoice, $key);
        
        echo "I selected a random value in the range 0..1 (HMAC=$hmac).\n";
        echo "Try to guess my selection:\n";
        echo "0 - 0\n1 - 1\nX - exit\n? - help\n";
        
        $playerGuess = $this->getPlayerInput(0, 1);
        echo "My selection: $computerChoice (KEY=" . bin2hex($key) . ")\n";
        
        return $playerGuess !== $computerChoice;
    }

    private function performThrow(string $player): int
    {
        $key = $this->randomGenerator->generateSecretKey();
        $computerNumber = $this->randomGenerator->generateUniformRandom(0, 5);
        $hmac = $this->randomGenerator->calculateHMAC((string)$computerNumber, $key);
        
        echo "\nIt's time for $player's throw.\n";
        echo "I selected a random value in the range 0..5 (HMAC=$hmac).\n";
        echo "Add your number modulo 6:\n";
        
        for ($i = 0; $i <= 5; $i++) {
            echo "$i - $i\n";
        }
        echo "X - exit\n? - help\n";
        
        $playerNumber = $this->getPlayerInput(0, 5);
        echo "My number is $computerNumber (KEY=" . bin2hex($key) . ")\n";
        
        $result = $this->randomGenerator->calculateModularSum($computerNumber, $playerNumber, 6);
        echo "The result is $computerNumber + $playerNumber = $result (mod 6)\n";
        
        $dice = $player === "Computer" ? $this->computerDice : $this->playerDice;
        $value = $dice->getValue($result);
        echo "$player's throw is $value\n";
        
        return $value;
    }

    private function getPlayerInput(int $min, int $max): int
    {
        while (true) {
            $input = trim(fgets(STDIN));
            
            if ($input === '?') {
                $this->showHelp();
                continue;
            }
            
            if ($input === 'X') {
                exit(0);
            }
            
            if (!is_numeric($input) || $input < $min || $input > $max) {
                echo "Please enter a number between $min and $max\n";
                continue;
            }
            
            return (int)$input;
        }
    }

    private function showHelp(): void
    {
        $matrix = $this->probabilityCalculator->generateProbabilityMatrix($this->diceConfigs);
        echo $this->tableGenerator->generateProbabilityTable($matrix, $this->diceConfigs);
    }

    private function computerSelectsDice(): void
    {
        $matrix = $this->probabilityCalculator->generateProbabilityMatrix($this->diceConfigs);
        $bestScore = -1;
        $bestDice = 0;
        
        for ($i = 0; $i < count($this->diceConfigs); $i++) {
            if ($this->playerDice && $this->playerDice === $this->diceConfigs[$i]) {
                continue;
            }
            
            $score = array_sum($matrix[$i]);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestDice = $i;
            }
        }
        
        $this->computerDice = $this->diceConfigs[$bestDice];
        echo "Computer chooses the [$this->computerDice] dice.\n";
    }

    private function playerSelectsDice(): void
    {
        echo "Choose your dice:\n";
        for ($i = 0; $i < count($this->diceConfigs); $i++) {
            if ($this->computerDice !== $this->diceConfigs[$i]) {
                echo "$i - {$this->diceConfigs[$i]}\n";
            }
        }
        echo "X - exit\n? - help\n";
        
        while (true) {
            $choice = $this->getPlayerInput(0, count($this->diceConfigs) - 1);
            if ($this->diceConfigs[$choice] !== $this->computerDice) {
                $this->playerDice = $this->diceConfigs[$choice];
                echo "You choose the [$this->playerDice] dice.\n";
                break;
            }
            echo "You cannot select the same dice as the computer. Please choose another one.\n";
        }
    }

    private function determineWinner(int $computerThrow, int $playerThrow): void
    {
        if ($computerThrow > $playerThrow) {
            echo "Computer wins ($computerThrow > $playerThrow)!\n";
        } elseif ($playerThrow > $computerThrow) {
            echo "You win ($playerThrow > $computerThrow)!\n";
        } else {
            echo "It's a tie ($computerThrow = $playerThrow)!\n";
        }
    }
} 