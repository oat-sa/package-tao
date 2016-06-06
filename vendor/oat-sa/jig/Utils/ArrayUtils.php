<?php

/**
 * This file is part of the Jig package.
 *
 * Copyright (c) 04-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Utils;

/**
 * ArrayUtils
 */
class ArrayUtils
{

    /**
     * Merges any number of arrays / parameters recursively, replacing
     * entries with string keys with values from latter arrays.
     * If the entry or the next value to be assigned is an array, then it
     * automagically treats both arguments as an array.
     * Numeric entries are appended, not replaced, but only if they are
     * unique
     *
     * @example result = array_merge_recursive_distinct(a1, a2, ... aN)
     * Credit: mark.roduner@gmail.com on http://php.net/manual/en/function.array-merge-recursive.php
     * */
    public static function arrayMergeRecursiveDistinct()
    {
        $arrays = func_get_args();
        $base   = array_shift($arrays);
        if (!is_array($base)) {
            $base = empty($base) ? array() : array($base);
        }

        foreach ($arrays as $append) {
            if (!is_array($append)) {
                $append = array($append);
            }

            foreach ($append as $key => $value) {
                if (!array_key_exists($key, $base) and !is_numeric($key)) {
                    $base[$key] = $append[$key];
                    continue;
                }
                if (is_array($value) or is_array($base[$key])) {
                    $base[$key] = self::arrayMergeRecursiveDistinct($base[$key], $append[$key]);
                } else if (is_numeric($key)) {
                    if (!in_array($value, $base)) {
                        $base[] = $value;
                    }
                } else {
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }


    /**
     * Pick works very much like pop() or shift() but gets data from any key of the
     * array. It also works with associative keys
     *
     * @param array $array
     * @param string $wantedKey
     * @return array
     */
    public static function pick(array &$array, $wantedKey)
    {
        $returnVal = array();
        foreach ($array as $key => $value) {
            if ($key === $wantedKey) {
                $returnVal = $value;
            }
        }
        unset($array[$wantedKey]);
        return $returnVal;
    }

    /**
     * Quote all values of an array according to their type (mainly for usage in CSV)
     *
     * @param array $array
     * @return array
     */
    public static function csvQuote(array $array)
    {
        foreach ($array as &$value) {
            $value = is_array($value) ? self::csvQuote($value) : StringUtils::csvQuote($value);
        }
        return $array;
    }

}
