<?php
/**
 * Minimal WP-CLI surface for running Social_Care_CLI without WP-CLI: records every log, success,
 * error and table call so the tests can assert what a command printed.
 *
 * @package ArnoldWender\SocialCareA11y\Tests
 */

declare(strict_types=1);

namespace {
    if (!class_exists('WP_CLI')) {
        final class WP_CLI {
            /** @var array<int, array{0: string, 1: mixed}> */
            public static array $calls = [];

            public static function add_command(...$args): void {
                self::$calls[] = ['add_command', $args[0] ?? ''];
            }
            public static function log(string $message): void {
                self::$calls[] = ['log', $message];
            }
            public static function success(string $message): void {
                self::$calls[] = ['success', $message];
            }
            public static function warning(string $message): void {
                self::$calls[] = ['warning', $message];
            }
            public static function error(string $message): void {
                self::$calls[] = ['error', $message];
            }
        }
    }
}

namespace WP_CLI\Utils {
    if (!\function_exists('WP_CLI\Utils\format_items')) {
        function format_items(string $format, array $items, array $fields): void {
            \WP_CLI::$calls[] = ['table', $items];
        }
    }
}
