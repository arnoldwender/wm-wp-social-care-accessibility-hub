<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\CLI;

use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP-CLI: `wp wm-social-care seed` and `wp wm-social-care calc`.
 */
final class Social_Care_CLI {
    public static function register(): void {
        if (!defined('WP_CLI') || !WP_CLI) {
            return;
        }

        \WP_CLI::add_command('wm-social-care', self::class);
    }

    /**
     * Lists the eight built-in example solutions. Writes nothing to the database.
     *
     * The name is historical: until 2026-09-14 the command said it seeded the database and printed
     * "Erfolgreich 8 Lösungswege-Datensätze geladen." without writing anything.
     *
     * ## EXAMPLES
     *     wp wm-social-care seed
     */
    public function seed($args, $assoc_args): void {
        $rows = [];
        foreach (Seed_Data_Manager::get_seed_data() as $item) {
            $rows[] = [
                'id'        => $item['id'],
                'title'     => $item['title'],
                'mediaType' => $item['mediaType'],
                'videoUrl'  => $item['videoUrl'] !== '' ? $item['videoUrl'] : '-',
            ];
        }

        \WP_CLI\Utils\format_items('table', $rows, ['id', 'title', 'mediaType', 'videoUrl']);
        \WP_CLI::log(sprintf('%d eingebaute Beispiele. Es wurde nichts in die Datenbank geschrieben; Beiträge vom Typ wm_loesungsweg legen Sie im Backend an.', count($rows)));
    }

    /**
     * Prints the statutory SGB XI amounts for one care degree.
     *
     * ## OPTIONS
     * [--pflegegrad=<pg>]
     * : Pflegegrad from 1 to 5.
     * ---
     * default: 2
     * ---
     *
     * ## EXAMPLES
     *     wp wm-social-care calc --pflegegrad=2
     */
    public function calc($args, $assoc_args): void {
        $raw = $assoc_args['pflegegrad'] ?? '2';
        // Whole numbers 1-5 only, as the REST route accepts; until 2026-09-15 "2.9" was cut to care degree 2.
        if (!is_scalar($raw) || !preg_match('/^[1-5]$/', trim((string) $raw))) {
            \WP_CLI::error('Ungültiger Pflegegrad. Erlaubt sind die ganzen Zahlen 1 bis 5.');
            return;
        }

        $result = Care_Needs_Checker::calculate_entitlements((int) $raw);
        $euro   = static fn(float $amount): string => number_format($amount, 2, ',', '.') . ' €';

        \WP_CLI::log(sprintf('Pflegegrad: %d', $result['pflegegrad']));
        \WP_CLI::log(sprintf('Entlastungsbetrag: %s / Monat (%s / Jahr)', $euro($result['entlastungsbetrag_month']), $euro($result['entlastungsbetrag_year'])));
        \WP_CLI::log(sprintf('Pflegegeld (§ 37 SGB XI): %s / Monat', $euro($result['pflegegeld_month'])));
        \WP_CLI::log(sprintf('Pflegesachleistung (§ 36 SGB XI): %s / Monat', $euro($result['sachleistung_month'])));
        // The joint budget starts at care degree 2. The shortcode leaves the line out below that; until 2026-09-15
        // this command printed "Bis zu 0.00 € / Jahr" for care degree 1.
        if ($result['verhinderungspflege_year'] > 0) {
            \WP_CLI::log(sprintf('Verhinderungs- und Kurzzeitpflege, gemeinsamer Jahresbetrag (§ 42a SGB XI): Bis zu %s / Jahr', $euro($result['verhinderungspflege_year'])));
        }
        \WP_CLI::log(sprintf('Stand der Beträge: %s', $result['statutory_as_of']));
        \WP_CLI::success('Berechnung erfolgreich durchgeführt.');
    }
}
