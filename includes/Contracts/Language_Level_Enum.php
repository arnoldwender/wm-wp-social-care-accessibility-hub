<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Contracts;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sprachniveaus für kognitive Barrierefreiheit nach DIN SPEC 33429 und DIN 8581-1.
 */
enum Language_Level_Enum: string {
    case STANDARD = 'standard';
    case EINFACHE_SPRACHE = 'einfache_sprache'; // DIN 8581-1 / ISO 24495-1 (Niveau B1)
    case LEICHTE_SPRACHE = 'leichte_sprache';   // DIN SPEC 33429 (Niveau A1-A2)

    public function get_label_de(): string {
        return match ($this) {
            self::STANDARD => 'Standarddeutsch',
            self::EINFACHE_SPRACHE => 'Einfache Sprache (B1)',
            self::LEICHTE_SPRACHE => 'Leichte Sprache (A1/A2)',
        };
    }

    public function requires_target_group_audit(): bool {
        return $this === self::LEICHTE_SPRACHE;
    }
}
