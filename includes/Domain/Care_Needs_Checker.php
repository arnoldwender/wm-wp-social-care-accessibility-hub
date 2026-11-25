<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Domain;

if (!defined('ABSPATH')) {
    exit;
}

final class Care_Needs_Checker {
    /**
     * Gesetzliche Leistungsbeträge des SGB XI (Deutschland).
     *
     * Stand 2025-07-01, unverändert für 2026 (Pflegeunterstützungs- und -entlastungsgesetz:
     * +4,5 % zum 2025-01-01; gemeinsamer Jahresbetrag seit 2025-07-01; nächste reguläre
     * Anpassung frühestens 2028-01-01). Bis zum 2026-09-14 standen hier die Werte von 2024
     * (125 € Entlastungsbetrag, 332–947 € Pflegegeld, 1.612 € Verhinderungspflege).
     * Quellen (abgerufen 2026-09-14): pflege.de (Entlastungsbetrag, Pflegegeld, gemeinsamer
     * Jahresbetrag), pflegeabc.de (Pflegesachleistungen), dejure.org (§ 45b SGB XI).
     */
    public const STATUTORY_AS_OF = '2025-07-01';

    private const ENTLASTUNGSBETRAG_MONTHLY = 131.00; // § 45b SGB XI (Pflegegrad 1 - 5), seit 2025-01-01

    /**
     * Gemeinsamer Jahresbetrag für Verhinderungs- und Kurzzeitpflege (§ 42a SGB XI, Pflegegrad
     * 2 - 5, seit 2025-07-01): ein Budget für beide Leistungen, daher sind Einzel- und
     * Kombinationsbetrag gleich. Die getrennten Töpfe (1.612 € + 2.418 €) gelten nicht mehr.
     */
    private const VERHINDERUNGSPFLEGE_ANNUAL = 3539.00;
    private const VERHINDERUNGSPFLEGE_MAX_COMBINED = 3539.00;

    private const PFLEGEGELD_TABLE = [ // § 37 SGB XI, seit 2025-01-01
        1 => 0.00,
        2 => 347.00,
        3 => 599.00,
        4 => 800.00,
        5 => 990.00,
    ];

    private const SACHLEISTUNG_TABLE = [ // § 36 SGB XI, seit 2025-01-01
        1 => 0.00,
        2 => 796.00,
        3 => 1497.00,
        4 => 1859.00,
        5 => 2299.00,
    ];

    /**
     * Calcula los subsidios y prestaciones mensuales/anuales según el Pflegegrad.
     */
    public static function calculate_entitlements(int $pflegegrad, bool $include_verhinderung = true): array {
        if ($pflegegrad < 1 || $pflegegrad > 5) {
            throw new \InvalidArgumentException('Ungültiger Pflegegrad. Erlaubt sind Werte von 1 bis 5.');
        }

        $entlastung_monthly = self::ENTLASTUNGSBETRAG_MONTHLY;
        $entlastung_annual = $entlastung_monthly * 12;

        $pflegegeld = self::PFLEGEGELD_TABLE[$pflegegrad];
        $sachleistung = self::SACHLEISTUNG_TABLE[$pflegegrad];

        $verhinderung_annual = ($pflegegrad >= 2 && $include_verhinderung) ? self::VERHINDERUNGSPFLEGE_ANNUAL : 0.00;
        $verhinderung_max = ($pflegegrad >= 2 && $include_verhinderung) ? self::VERHINDERUNGSPFLEGE_MAX_COMBINED : 0.00;

        $total_monthly_available = $entlastung_monthly + $pflegegeld;

        $result = [
            'pflegegrad'               => $pflegegrad,
            'entlastungsbetrag_month'  => $entlastung_monthly,
            'entlastungsbetrag_year'   => $entlastung_annual,
            'pflegegeld_month'         => $pflegegeld,
            'sachleistung_month'       => $sachleistung,
            'verhinderungspflege_year' => $verhinderung_annual,
            'verhinderungspflege_max'  => $verhinderung_max,
            'total_relief_monthly'     => $total_monthly_available,
            'has_anfoevo_subsidy'      => true, // Anerkannt nach AnFöVO NRW
            'statutory_as_of'          => self::STATUTORY_AS_OF,
        ];

        if (function_exists('apply_filters')) {
            $filtered = apply_filters('wm_social_care_bedarfscheck_result', $result, $pflegegrad, $include_verhinderung);
            if (is_array($filtered)) {
                $result = $filtered;
            }
        }

        return $result;
    }

    /**
     * Tabla por Pflegegrad para la calculadora del frontend: una sola fuente para PHP, WP-CLI y
     * el script del shortcode (hasta el 2026-09-14 el shortcode llevaba su propia tabla con los
     * valores de 2024).
     *
     * @return array<int, array{entlastung: float, entlastung_jahr: float, geld: float, verhinderung: float}>
     */
    public static function entitlement_table(): array {
        $table = [];
        for ($pg = 1; $pg <= 5; $pg++) {
            $row = self::calculate_entitlements($pg);
            $table[$pg] = [
                'entlastung'      => $row['entlastungsbetrag_month'],
                'entlastung_jahr' => $row['entlastungsbetrag_year'],
                'geld'            => $row['pflegegeld_month'],
                'verhinderung'    => $row['verhinderungspflege_year'],
            ];
        }
        return $table;
    }
}
