<?php
namespace Core\Helper;

/**
 * contains the helpers for array data type
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class ArrayHelper
{
    /**
     * get array value on dot-notation string
     * 
     * @param  array  $array   array of data
     * @param  string $key     dot-notation key of the array
     * @param  mixed  $default will be returned if no data is found in the array
     * 
     * @return mixed
     */
    public static function dot(array $array, $key, $default = null)
    {
        // explode the dot-notation key
        $stacks = explode('.', $key);

        // if only the first key is asked then return it already
        if (count($stacks) == 1) {
            return @$array[$key];
        }

        // if has a valid count 
        if (count($stacks)) {
            // set the current value as the array passed
            $value = $array;
            // loop through all the dots
            foreach ($stacks as $key => $stack) {
                if ($value) {
                    $value = self::dot($value, $stack);
                } else {
                    // stop the loop if value is empty already
                    break;
                }
            }
            return $value ?: $default;
        }

        return $default ?: null;
    }

    /**
     * Sort a 2 dimensional array based on a given key
     *
     * credits: https://blog.jachim.be/2009/09/php-msort-multidimensional-array-sort/comment-page-1/
     * 
     * @param  array        $array      array to be sort
     * @param  string|array $array_keys keys of an array to be sort
     * @param  int          $sort_flag  sort behavior of the function `asort` or `arsort`
     * @param  int          $sort_order sort order behavior
     * 
     * @return array
     */
    public static function msort(array $array, $array_keys, $sort_flag = SORT_REGULAR, $sort_order = SORT_ASC)
    {
        // return the current array if keys are empty or the array is not valid
        if (!count($array) || empty($array_keys)) {
            return $array;
        }

        $mapping = [];
        foreach ($array as $key => $value) {
            $sort_key = '';
            if (!is_array($array_keys)) {
                $sort_key .= $value[$array_keys];
            } else {
                foreach ($array_keys as $array_key) {
                    $sort_key .= $value[$array_key];
                }
                $sort_flag = SORT_STRING;
            }
            $mapping[$key] = $sort_key;
        }

        switch ($sort_order) {
            case SORT_DESC:
                arsort($mapping, $sort_flag);
                break;
            default:
                asort($mapping, $sort_flag);
                break;
        }

        $sorted = [];
        foreach ($mapping as $key => $value) {
            $sorted[$key] = $array[$key];
        }

        return $sorted;
    }
}
