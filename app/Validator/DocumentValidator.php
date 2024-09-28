<?php

namespace App\Validator;

class DocumentValidator
{
    public static function validateCpf(string $value): bool
    {
        $cpf = preg_replace('/[^0-9]/is', '', $value);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public static function validateCnpj(string $value): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $length = strlen($cnpj) - 2;
        $numbers = substr($cnpj, 0, $length);
        $digits = substr($cnpj, $length);
        $firstDigit = self::checkDigit($length, $numbers);

        if ($firstDigit != $digits[0]) {
            return false;
        }

        $length += 1;
        $numbers = substr($cnpj, 0, $length);
        $secondDigit = self::checkDigit($length, $numbers);

        if ($secondDigit != $digits[1]) {
            return false;
        }

        return true;
    }

    /**
     * @param int $length
     * @param $numbers
     * @return int
     */
    public static function checkDigit(int $length, $numbers): int
    {
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        return ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
    }

}