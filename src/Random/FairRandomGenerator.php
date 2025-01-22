<?php

namespace App\Random;

use Exception;

class FairRandomGenerator
{
    private const KEY_LENGTH = 32; 

    public function generateSecretKey(): string 
    {
        return random_bytes(self::KEY_LENGTH);
    }

    public function calculateHMAC(string $message, string $key): string 
    {
        return hash_hmac('sha3-256', $message, $key);
    }

    public function generateUniformRandom(int $min, int $max): int 
    {
        $range = $max - $min + 1;
        $bits = ceil(log($range, 2));
        $bytes = ceil($bits / 8);
        $mask = (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(random_bytes($bytes))) & $mask;
        } while ($rnd >= $range);

        return $min + $rnd;
    }

    public function calculateModularSum(int $a, int $b, int $modulus): int 
    {
        return ($a + $b) % $modulus;
    }
} 