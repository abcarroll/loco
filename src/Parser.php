<?php

namespace Ferno\Loco;

/**
 * Abstract base-class for Parsers
 */
abstract class Parser
{
    /**
     * a helpful internal function
     *
     * @param mixed $array
     */
    public function serializeArray($array)
    {
        $string = 'array(';
        foreach (array_keys($array) as $keyId => $key) {
            $string .= var_export($key, true) . ' => ';
            if (is_string($array[$key])) {
                $string .= var_export($array[$key], true);
            } else {
                $string .= $array[$key]->__toString();
            }

            if ($keyId + 1 !== count($array)) {
                $string .= ', ';
            }
        }
        $string .= ')';

        return $string;
    }
}
