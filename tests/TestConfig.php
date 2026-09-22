<?php

namespace Comento\SensAlimtalk\Test;

/**
 * Minimal config store backing the test-only config() helper.
 *
 * The package relies on Laravel's config() helper, which is provided by
 * illuminate/foundation - a dependency this package deliberately does not pull in.
 */
class TestConfig
{
    private static $items = [];

    public static function set(array $items)
    {
        self::$items = $items;
    }

    public static function reset()
    {
        self::$items = [];
    }

    public static function all(): array
    {
        return self::$items;
    }

    public static function get($key, $default = null)
    {
        return array_key_exists($key, self::$items) ? self::$items[$key] : $default;
    }
}
