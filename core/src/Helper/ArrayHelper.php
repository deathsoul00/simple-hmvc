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
}
