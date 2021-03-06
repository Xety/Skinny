<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Skinny\Utility;

class Inflector
{
    /**
     * Method cache array.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Cache inflected values, and return if already available.
     *
     * @param string $type Inflection type.
     * @param string $key Original value.
     * @param string|bool $value Inflected value.
     *
     * @return string|bool Inflected value on cache hit or false on cache miss.
     */
    protected static function cache($type, $key, $value = false)
    {
        $key = '_' . $key;
        $type = '_' . $type;
        if ($value !== false) {
            static::$cache[$type][$key] = $value;

            return $value;
        }
        if (!isset(static::$cache[$type][$key])) {
            return false;
        }

        return static::$cache[$type][$key];
    }

    /**
     * Returns the input lower_case_delimited_string as a CamelCasedString.
     *
     * @param string $string String to camelize.
     * @param string $delimiter The delimiter in the input string.
     *
     * @return string CamelizedStringLikeThis.
     */
    public static function camelize($string, $delimiter = '_')
    {
        $cacheKey = __FUNCTION__ . $delimiter;
        $result = static::cache($cacheKey, $string);
        if ($result === false) {
            $result = str_replace(' ', '', static::humanize($string, $delimiter));
            static::cache(__FUNCTION__, $string, $result);
        }

        return $result;
    }

    /**
     * Returns the input lower_case_delimited_string as 'A Human Readable String'.
     * (Underscores are replaced by spaces and capitalized following words.)
     *
     * @param string $string String to be humanized.
     * @param string $delimiter The character to replace with a space.
     *
     * @return string Human-readable string.
     */
    public static function humanize($string, $delimiter = '_')
    {
        $cacheKey = __FUNCTION__ . $delimiter;
        $result = static::cache($cacheKey, $string);
        if ($result === false) {
            $result = explode(' ', str_replace($delimiter, ' ', $string));
            foreach ($result as &$word) {
                $word = mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
            }
            $result = implode(' ', $result);
            static::cache($cacheKey, $string, $result);
        }

        return $result;
    }
}
