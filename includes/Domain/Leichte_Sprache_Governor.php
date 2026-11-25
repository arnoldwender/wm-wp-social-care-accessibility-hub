<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Domain;

if (!defined('ABSPATH')) {
    exit;
}

final class Leichte_Sprache_Governor {
    /**
     * Diccionario de palabras compuestas comunes en el sector de cuidados de habla alemana (DACH).
     */
    private const COMPOUND_CARE_WORDS = [
        'Pflegekasse' => 'Pflege·kasse',
        'Pflegeversicherung' => 'Pflege·versicherung',
        'Alltagsbegleitung' => 'Alltags·begleitung',
        'Entlastungsbetrag' => 'Entlastungs·betrag',
        'Verhinderungspflege' => 'Verhinderungs·pflege',
        'Kurzzeitpflege' => 'Kurzzeit·pflege',
        'Pflegegrad' => 'Pflege·grad',
        'Pflegeberatung' => 'Pflege·beratung',
        'Seniorenheim' => 'Senioren·heim',
        'Krankenkasse' => 'Kranken·kasse',
        'Haushaltshilfe' => 'Haushalts·hilfe',
        'Pflegedienst' => 'Pflege·dienst',
        'Betreuungsdienst' => 'Betreuungs·dienst',
        'Schwerbehindertenausweis' => 'Schwer·behinderten·ausweis',
        'Hilfsmittel' => 'Hilfs·mittel',
    ];

    /**
     * Aplica el formato de Mediopunkt (·) a términos compuestos según la norma DIN SPEC 33429.
     */
    public static function apply_mediopunkt(string $text): string {
        foreach (self::COMPOUND_CARE_WORDS as $plain => $segmented) {
            // Reemplazo exacto respetando mayúsculas/minúsculas
            $pattern = '/\b' . preg_quote($plain, '/') . '\b/u';
            $text = preg_replace($pattern, $segmented, $text) ?? $text;
        }
        return $text;
    }

    /**
     * Valida si la atribución de Inclusion Europe está completa en textos de Leichte Sprache.
     */
    public static function validate_inclusion_europe_license(bool $has_logo, string $copyright_text): bool {
        if (!$has_logo) {
            return true; // No usa el logo oficial
        }

        // Requisito obligatorio: Mención expresa a Inclusion Europe y enlace web
        $has_mention = str_contains($copyright_text, 'Inclusion Europe');
        $has_link = str_contains($copyright_text, 'inclusion-europe.eu/easy-to-read');

        return $has_mention && $has_link;
    }

    /**
     * Valida un texto según las reglas de oraciones cortas de Leichte Sprache (máx 15 palabras/frase).
     */
    public static function audit_sentence_length(string $text): array {
        // Dividir por puntos, signos de exclamación o interrogación
        $sentences = preg_split('/[.!?]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $long_sentences = [];

        foreach ($sentences as $index => $sentence) {
            $words = preg_split('/\s+/u', trim($sentence), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $count = count($words);
            if ($count > 15) {
                $long_sentences[] = [
                    'index'      => $index + 1,
                    'word_count' => $count,
                    'sentence'   => trim($sentence)
                ];
            }
        }

        return [
            'total_sentences' => count($sentences),
            'violations'      => count($long_sentences),
            'is_valid'        => count($long_sentences) === 0,
            'details'         => $long_sentences
        ];
    }
}
