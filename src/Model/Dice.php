<?php

namespace App\Model;

class Dice
{
    private array $values;
    private string $representation;

    public function __construct(array $values) 
    {
        if (count($values) !== 6) {
            throw new \InvalidArgumentException("Dice should have 6 faces");
        }
        
        $this->values = $values;
        $this->representation = implode(',', $values);
    }

    public function getValue(int $index): int 
    {
        return $this->values[$index];
    }

    public function getValues(): array 
    {
        return $this->values;
    }

    public function __toString(): string 
    {
        return $this->representation;
    }
} 