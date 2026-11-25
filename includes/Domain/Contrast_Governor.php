<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Domain;

if (!defined('ABSPATH')) {
    exit;
}

final class Contrast_Governor {
    /**
     * Calcula la luminancia relativa de un color sRGB en formato hexadecimal.
     */
    public static function calculate_relative_luminance(string $hex_color): float {
        $clean_hex = ltrim($hex_color, '#');
        if (strlen($clean_hex) === 3) {
            $clean_hex = $clean_hex[0] . $clean_hex[0] . $clean_hex[1] . $clean_hex[1] . $clean_hex[2] . $clean_hex[2];
        }

        if (strlen($clean_hex) !== 6) {
            throw new \InvalidArgumentException('Ungültiger Hex-Farbwert: ' . esc_html($hex_color));
        }

        $r = hexdec(substr($clean_hex, 0, 2)) / 255.0;
        $g = hexdec(substr($clean_hex, 2, 2)) / 255.0;
        $b = hexdec(substr($clean_hex, 4, 2)) / 255.0;

        $r_lin = ($r <= 0.04045) ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g_lin = ($g <= 0.04045) ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b_lin = ($b <= 0.04045) ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return (0.2126 * $r_lin) + (0.7152 * $g_lin) + (0.0722 * $b_lin);
    }

    /**
     * Calcula el ratio de contraste entre dos colores (L1 + 0.05) / (L2 + 0.05).
     */
    public static function calculate_contrast_ratio(string $color1, string $color2): float {
        $l1 = self::calculate_relative_luminance($color1);
        $l2 = self::calculate_relative_luminance($color2);

        $lighter = max($l1, $l2);
        $darker  = min($l1, $l2);

        return round(($lighter + 0.05) / ($darker + 0.05), 2);
    }

    /**
     * Valida el cumplimiento de WCAG 2.2 AA (4.5:1) y AAA (7:1).
     */
    public static function evaluate_wcag_levels(string $text_color, string $bg_color): array {
        $ratio = self::calculate_contrast_ratio($text_color, $bg_color);

        return [
            'ratio'        => $ratio,
            'wcag_aa_normal' => $ratio >= 4.5,
            'wcag_aa_large'  => $ratio >= 3.0,
            'wcag_aaa_normal'=> $ratio >= 7.0,
            'wcag_aaa_large' => $ratio >= 4.5,
        ];
    }
}
