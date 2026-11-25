<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Domain;

if (!defined('ABSPATH')) {
    exit;
}

final class Seed_Data_Manager {
    /**
     * Retorna el conjunto canónico de soluciones cinestésicas (LösungsWege).
     */
    public static function get_seed_data(): array {
        $items = [
            [
                'id'                   => '1',
                'title'                => 'Sicherer Transfer vom Rollstuhl ins Bett',
                'category'             => 'Mobilität',
                'mediaType'            => 'video',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Rollstuhl parallel zum Bett positionieren und Bremsen feststellen',
                    'Fußrasten hochklappen und Füße auf den Boden stellen',
                    'Seitenlehne auf der Bettseite entfernen oder wegklappen',
                    'Mit beiden Händen auf die Sitzfläche stützen und nach vorne rutschen',
                    'Oberkörper leicht nach vorne neigen und Gewicht auf die Füße verlagern',
                    'Mit einer Drehbewegung auf das Bett übersetzen',
                    'Beine nachziehen und sichere Position einnehmen',
                ],
                'tags'                 => ['Rollstuhl', 'Transfer', 'Bett', 'Selbstständigkeit', 'Kinästhetik'],
                'targetGroup'          => 'Rollstuhlnutzer',
                'pflegegrad'           => 'Pflegegrade 2 bis 5',
                'duration'             => '3:45',
                'leichteSprache'       => 'So kommen Sie sicher vom Roll·stuhl ins Bett. Stellen Sie die Bremsen fest. Nehmen Sie die Fuß·stützen hoch. Stützen Sie sich mit beiden Händen ab. Drehen Sie Ihren Körper langsam auf das Bett.',
            ],
            [
                'id'                   => '2',
                'title'                => 'Orientierung in der Küche bei Sehbeeinträchtigung',
                'category'             => 'Alltag & Haushalt',
                'mediaType'            => 'video',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Feste Plätze für alle Küchenutensilien festlegen',
                    'Tastbare Markierungen an Herd und Backofen anbringen',
                    'Gewürze mit Braille-Etiketten oder tastbaren Symbolen kennzeichnen',
                    'Arbeitsfläche systematisch von links nach rechts nutzen',
                    'Messbecher mit tastbaren Markierungen verwenden',
                    'Timer mit akustischem Signal nutzen',
                ],
                'tags'                 => ['Küche', 'Sehbehinderung', 'Orientierung', 'Kochen'],
                'targetGroup'          => 'Sehbeeinträchtigung',
                'pflegegrad'           => 'Pflegegrade 1 bis 3',
                'duration'             => '5:12',
                'leichteSprache'       => 'Tipps für die Küche für Menschen die schlecht sehen. Legen Sie jedes Ding an seinen festen Platz. Kleben Sie fühlbare Punkte auf den Herd. Nutzen Sie eine sprechende Küchen·uhr.',
            ],
            [
                'id'                   => '3',
                'title'                => 'Kommunikation mit Gebärdensprache-App',
                'category'             => 'Kommunikation',
                'mediaType'            => 'video',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'App herunterladen und installieren',
                    'Persönliches Profil einrichten',
                    'Grundgebärden im Übungsmodus erlernen',
                    'Kamera-Funktion für Echtzeit-Übersetzung aktivieren',
                    'Häufig genutzte Phrasen als Favoriten speichern',
                ],
                'tags'                 => ['Gebärdensprache', 'App', 'Kommunikation', 'Hörbehinderung'],
                'targetGroup'          => 'Hörbeeinträchtigung',
                'pflegegrad'           => 'Pflegegrade 1 bis 4',
                'duration'             => '4:30',
                'leichteSprache'       => 'Eine App für Gebärden·sprache hilft beim Sprechen. Die App zeigt Gebärden als Video. Sie können wichtige Wörter speichern.',
            ],
            [
                'id'                   => '4',
                'title'                => 'Anpassung eines Rollators für unebenes Gelände',
                'category'             => 'Hilfsmittel & Technik',
                'mediaType'            => 'bild',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Bereifung auf Geländereifen (größerer Durchmesser) umstellen',
                    'Bremsen auf Festigkeit prüfen und einstellen',
                    'Griffhöhe an persönliche Körpergröße anpassen',
                    'Zusätzlichen Korb oder Tasche sicher befestigen',
                    'Reflektoren für bessere Sichtbarkeit anbringen',
                ],
                'tags'                 => ['Rollator', 'Gelände', 'Anpassung', 'Outdoor'],
                'targetGroup'          => 'Gehbeeinträchtigung',
                'pflegegrad'           => 'Pflegegrade 1 bis 3',
                'duration'             => '2:50',
                'leichteSprache'       => 'So stellen Sie Ihren Rollator für draußen ein. Große Räder fahren besser über Steine. Die Griffe müssen auf Höhe Ihrer Hand·gelenke sein.',
            ],
            [
                'id'                   => '5',
                'title'                => 'Tastbare Orientierungshilfen im Wohnbereich',
                'category'             => 'Wahrnehmung',
                'mediaType'            => 'text',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Türrahmen mit unterschiedlichen Texturen markieren',
                    'Teppichläufer als Leitlinien auf dem Boden verlegen',
                    'Lichtschalter mit tastbaren Aufklebern kennzeichnen',
                    'Kontraststreifen an Treppenkanten anbringen',
                    'Handlauf durchgehend installieren',
                ],
                'tags'                 => ['Orientierung', 'Wohnung', 'Tastbar', 'Leitlinien'],
                'targetGroup'          => 'Sehbeeinträchtigung',
                'pflegegrad'           => 'Pflegegrade 1 bis 5',
                'duration'             => '',
                'leichteSprache'       => 'Orientierung in der Wohnung. Kleben Sie fühlbare Streifen an die Türen. Bringen Sie helle Streifen an den Treppen an. So finden Sie sicher Ihren Weg.',
            ],
            [
                'id'                   => '6',
                'title'                => 'Einhändig Schuhe binden mit Hilfsmittel',
                'category'             => 'Alltag & Haushalt',
                'mediaType'            => 'video',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Elastische Schnürsenkel oder Magnetverschlüsse besorgen',
                    'Schuh auf rutschfester Unterlage platzieren',
                    'Schnürsenkel durch spezielle Klemme führen',
                    'Mit einer Hand die Schlaufe formen',
                    'Klemme zudrücken, um die Schleife zu fixieren',
                ],
                'tags'                 => ['Einhändig', 'Schuhe', 'Hilfsmittel', 'Alltag', 'Hemiplegie'],
                'targetGroup'          => 'Einschränkung obere Extremität',
                'pflegegrad'           => 'Pflegegrade 2 bis 4',
                'duration'             => '2:15',
                'leichteSprache'       => 'Schuhe binden mit nur einer Hand. Nehmen Sie Schnürsenkel aus Gummi oder mit Magnet. Dann brauchen Sie keine Schleife binden.',
            ],
            [
                'id'                   => '7',
                'title'                => 'Sprachsteuerung für Smart-Home einrichten',
                'category'             => 'Hilfsmittel & Technik',
                'mediaType'            => 'video',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Sprachassistenten auswählen und einrichten',
                    'Smart-Home-Geräte (Licht, Heizung, Tür) verbinden',
                    'Individuelle Sprachbefehle und Routinen konfigurieren',
                    'Notfall-Sprachbefehl einrichten',
                    'Regelmäßig Befehle üben und anpassen',
                ],
                'tags'                 => ['Smart-Home', 'Sprachsteuerung', 'Technik', 'Barrierefreiheit'],
                'targetGroup'          => 'Eingeschränkte Mobilität',
                'pflegegrad'           => 'Pflegegrade 1 bis 5',
                'duration'             => '6:00',
                'leichteSprache'       => 'Das Haus mit der Stimme steuern. Sagen Sie zum Beispiel: Licht an. Oder: Tür öffnen. Das hilft wenn man sich schwer bewegen kann.',
            ],
            [
                'id'                   => '8',
                'title'                => 'Taktile Markierungen für Medikamentensortierung',
                'category'             => 'Wahrnehmung',
                'mediaType'            => 'bild',
                'videoUrl'             => '',
                'captionsUrl'          => '',
                'steps'                => [
                    'Medikamentenbox mit verschiedenen Fächern besorgen',
                    'Jedes Fach mit unterschiedlicher Textur bekleben',
                    'Braille- oder Großdruck-Beschriftung anbringen',
                    'Tageszeiten durch verschiedene Formen kodieren',
                    'Sprechende Erinnerung (Timer oder App) einrichten',
                ],
                'tags'                 => ['Medikamente', 'Taktil', 'Sortierung', 'Sehbehinderung'],
                'targetGroup'          => 'Sehbeeinträchtigung',
                'pflegegrad'           => 'Pflegegrade 1 bis 5',
                'duration'             => '3:10',
                'leichteSprache'       => 'Medikamente sicher ordnen. Kleben Sie Punkte auf die Fächer: Ein Punkt für morgens, zwei Punkte für mittags. So verwechseln Sie keine Tablette.',
            ],
        ];

        if (function_exists('apply_filters')) {
            $filtered = apply_filters('wm_social_care_loesungswege_items', $items);
            if (is_array($filtered)) {
                return $filtered;
            }
        }

        return $items;
    }
}
