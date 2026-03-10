<?php

namespace App\Core;

class Controller
{
    protected function clean(?string $value): string
    {
        return trim((string)$value);
    }

    protected function toNullableDecimal(?string $value): ?float
    {
        $value = trim((string)$value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.\-]/', '', $value);

        if ($value === '') {
            return null;
        }

        return (float)$value;
    }
}