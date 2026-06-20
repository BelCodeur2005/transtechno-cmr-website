<?php

namespace App\Support;

/**
 * Convertit un entier en toutes lettres françaises (majuscules).
 * Couvre 0 – 999 999 999 FCFA.
 */
class NombrEnLettres
{
    private static array $units = [
        '', 'UN', 'DEUX', 'TROIS', 'QUATRE', 'CINQ', 'SIX', 'SEPT', 'HUIT', 'NEUF',
        'DIX', 'ONZE', 'DOUZE', 'TREIZE', 'QUATORZE', 'QUINZE', 'SEIZE',
        'DIX-SEPT', 'DIX-HUIT', 'DIX-NEUF',
    ];

    private static array $tens = [
        '', 'DIX', 'VINGT', 'TRENTE', 'QUARANTE', 'CINQUANTE', 'SOIXANTE',
        'SOIXANTE', 'QUATRE-VINGT', 'QUATRE-VINGT',
    ];

    public static function convert(int $n, string $currency = 'FRANCS CFA'): string
    {
        if ($n === 0) {
            return 'ZÉRO ' . $currency;
        }
        $words = trim(self::words($n));
        // CENT prend un S seulement s'il est final : "DEUX CENTS" mais "DEUX CENT MILLE"
        $words = preg_replace('/\bCENTS\b(?=\s+(?:MILLE|MILLION|MILLIARD))/', 'CENT', $words);
        return $words . ' ' . $currency;
    }

    private static function words(int $n): string
    {
        if ($n === 0) return '';
        if ($n < 20) return self::$units[$n];
        if ($n < 100) return self::belowHundred($n);
        if ($n < 1_000) return self::belowThousand($n);
        if ($n < 1_000_000) {
            $k = intdiv($n, 1_000);
            $r = $n % 1_000;
            $prefix = $k === 1 ? 'MILLE' : self::words($k) . ' MILLE';
            return $r > 0 ? $prefix . ' ' . self::words($r) : $prefix;
        }
        if ($n < 1_000_000_000) {
            $m = intdiv($n, 1_000_000);
            $r = $n % 1_000_000;
            $prefix = self::words($m) . ' MILLION' . ($m > 1 ? 'S' : '');
            return $r > 0 ? $prefix . ' ' . self::words($r) : $prefix;
        }
        $b = intdiv($n, 1_000_000_000);
        $r = $n % 1_000_000_000;
        $prefix = self::words($b) . ' MILLIARD' . ($b > 1 ? 'S' : '');
        return $r > 0 ? $prefix . ' ' . self::words($r) : $prefix;
    }

    private static function belowHundred(int $n): string
    {
        $ten  = intdiv($n, 10);
        $unit = $n % 10;

        return match (true) {
            // 70–79 : soixante + 10..19
            $ten === 7 && $unit === 0 => 'SOIXANTE-DIX',
            $ten === 7 && $unit === 1 => 'SOIXANTE-ET-ONZE',
            $ten === 7               => 'SOIXANTE-' . self::$units[10 + $unit],
            // 80–89 : quatre-vingt(s)
            $ten === 8 && $unit === 0 => 'QUATRE-VINGTS',
            $ten === 8               => 'QUATRE-VINGT-' . self::$units[$unit],
            // 90–99 : quatre-vingt-dix + x
            $ten === 9 && $unit === 0 => 'QUATRE-VINGT-DIX',
            $ten === 9               => 'QUATRE-VINGT-' . self::$units[10 + $unit],
            // 20–69 standard
            $unit === 0              => self::$tens[$ten],
            $unit === 1              => self::$tens[$ten] . '-ET-UN',
            default                  => self::$tens[$ten] . '-' . self::$units[$unit],
        };
    }

    private static function belowThousand(int $n): string
    {
        $hundreds = intdiv($n, 100);
        $rest     = $n % 100;
        $centWord = $hundreds === 1
            ? 'CENT'
            : self::$units[$hundreds] . ' CENT' . ($rest === 0 && $hundreds > 1 ? 'S' : '');
        return $rest > 0 ? $centWord . ' ' . self::words($rest) : $centWord;
    }
}
