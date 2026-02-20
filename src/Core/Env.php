<?php
declare(strict_types=1);

namespace Skinny\Core;

/**
 * Helper class for accessing environment variables with type casting and default values.
 */
class Env
{
    /**
     * Get an environment variable value.
     *
     * @param string $key The environment variable name.
     * @param mixed $default Default value if not found.
     *
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        return self::cast($value);
    }

    /**
     * Get an environment variable as a string.
     *
     * @param string $key The environment variable name.
     * @param string $default Default value if not found.
     *
     * @return string
     */
    public static function getString(string $key, string $default = ''): string
    {
        return (string) self::get($key, $default);
    }

    /**
     * Get an environment variable as an integer.
     *
     * @param string $key The environment variable name.
     * @param int $default Default value if not found.
     *
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    /**
     * Get an environment variable as a boolean.
     *
     * @param string $key The environment variable name.
     * @param bool $default Default value if not found.
     *
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Get an environment variable as a float.
     *
     * @param string $key The environment variable name.
     * @param float $default Default value if not found.
     *
     * @return float
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) self::get($key, $default);
    }

    /**
     * Get an environment variable as an array (comma-separated values).
     *
     * @param string $key The environment variable name.
     * @param array $default Default value if not found.
     * @param string $separator The separator used to split the string.
     *
     * @return array
     */
    public static function getArray(string $key, array $default = [], string $separator = ','): array
    {
        $value = self::get($key);

        if ($value === null || $value === '') {
            return $default;
        }

        if (is_array($value)) {
            return $value;
        }

        return array_map('trim', explode($separator, (string) $value));
    }

    /**
     * Check if an environment variable is set.
     *
     * @param string $key The environment variable name.
     *
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]) || isset($_SERVER[$key]) || getenv($key) !== false;
    }

    /**
     * Get a required environment variable or throw an exception.
     *
     * @param string $key The environment variable name.
     *
     * @return mixed
     *
     * @throws \RuntimeException If the variable is not set.
     */
    public static function getRequired(string $key): mixed
    {
        if (!self::has($key)) {
            throw new \RuntimeException("Required environment variable '{$key}' is not set.");
        }

        return self::get($key);
    }

    /**
     * Cast string values to appropriate PHP types.
     *
     * @param string $value The value to cast.
     *
     * @return mixed
     */
    private static function cast(string $value): mixed
    {
        $lower = strtolower($value);

        // Boolean values
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }

        // Null value
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }

        // Empty value
        if ($lower === 'empty' || $lower === '(empty)') {
            return '';
        }

        // Quoted strings - remove quotes
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            return $matches[1];
        }
        if (preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }

        return $value;
    }
}
