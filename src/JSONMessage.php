<?php

if ( realpath( $_SERVER['SCRIPT_FILENAME'] ) == __FILE__ ) {
    die();
}

// shims of `json_last_error_msg`

if (!function_exists('json_last_error')) {
    function json_last_error_msg() {
        return 'Unknown JSON error';
    }
} elseif (!function_exists('json_last_error_msg')) {
    function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => NULL,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }
}

/**
 * A convenience to get typed properties from an associative array, a default or fail.
 */
class JSONMessage implements ArrayAccess {

    // the associative array wrapped

    public $map;

    // an ArrayAccess implementation that fail fast when `offset` is not a string

    public function offsetSet($offset, $value) {
        if (is_string($offset)) {
            $this->map[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return (is_string($offset) && isset($this->map[$offset]));
    }
    public function offsetUnset($offset) {
        if (is_string($offset)) {
            unset($this->map[$offset]);
        }
    }
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->map[$offset] : NULL;
    }

    // The JSONMessage methods

    /**
     * Return TRUE if $array is a list (ie: an ordered array with numeric indexes)
     *
     * @param array $array to test
     * @return boolean
     */
    static function is_list ($array) {
        if (!is_array($array)) {
            return FALSE;
        } else if (count($array) === 0) {
        	return TRUE;
        }
        return (0 === count(array_diff(range(0, count($array)-1), array_keys($array))));
    }
    /**
     * Return TRUE if $array is a map (ie: an unordered array with string indexes)
     *
     * @param array $array to test
     * @return boolean
     */
    static function is_map ($array) {
        if (!is_array($array)) {
            return FALSE;
        } else if (count($array) === 0) {
        	return TRUE;
        }
        return (!self::is_list($array));
    }
    /**
     *
     */
    private static function _uniform_list ($list) {
        $encoded = array();
        foreach($list as $value) {
            if (is_scalar($value)) {
                $uniform = json_encode($value);
            } elseif (is_array($value)) {
                if (count($value) === 0) {
                    $uniform = 'null';
                } elseif (self::is_list($value)) {
                    $uniform = self::_uniform_list($value);
                } else {
                    $uniform = self::_uniform_map($value);
                }
            } elseif (is_object($value)) {
                $uniform = self::_uniform_map((array) $value);
            } else {
                $uniform = 'null';
            }
            array_push($encoded, $uniform);
        }
        return '['.implode(',', $encoded).']';
    }
    /**
     *
     */
    private static function _uniform_map ($map) {
        $keys = array_keys($map);
        $encoded = array();
        sort($keys);
        foreach($keys as $key) {
            $value = $map[$key];
            if (is_scalar($value)) {
                $uniform = json_encode($value);
            } else if (is_array($value)) {
                $count = count($value);
                if ($count === 0) {
                    $uniform = 'null';
                } else if (0 === count(array_diff(
                    range(0, $count-1), array_keys($value)
                    ))) {
                    $uniform = self::_uniform_list($value);
                } else {
                    $uniform = self::_uniform_map($value);
                }
            } elseif (is_object($value)) {
                $uniform = self::_uniform_map((array) $value);
            } else {
                $uniform = 'null';
            }
            array_push($encoded, json_encode($key).':'.$uniform);
        }
        return '{'.implode(',', $encoded).'}';
    }
    /**
     * Parse a JSON string, eventually with a maximum depth and big integers
     * as strings, return NULL if an error occured (and yes, a NULL message
     * is another error).
     */
    static function parse ($encoded, $maxDepth=512) {
        if (defined('JSON_BIGINT_AS_STRING')) {
            $json = @json_decode($encoded, TRUE, $maxDepth, JSON_BIGINT_AS_STRING);
        } else {
            $json = @json_decode($encoded, TRUE);
        }
        if ($json === NULL) {
            return NULL;
        }
        return new JSONMessage($json, $encoded);
    }
    //
    private $_encoded;
    /**
     * Assert that $array is a map, construct a new `JSONMessage` wrapping it or.
     *
     * @param array $array a map
     * @param string $encoded the original encoded JSON string, defaults to NULL
     * @throws any exception with a type error
     */
    function __construct($array, $encoded=NULL) {
        if (!self::is_map($array)) {
            throw $this->exception(
                'Type Error - not an Object: '.json_encode($array)
                );
        }
        $this->map = $array;
        $this->_encoded = $encoded;
    }
    function exception ($message, $previous=NULL) {
    	return new Exception($message, $previous);
    }
    function uniform () {
        return self::_uniform_map($this->map);
    }
    function encoded ($encoded=NULL) {
        if (is_string($encoded)) {
            $this->_encoded = $encoded;
        }
        return $this->_encoded;
    }
    function encode () {
        return json_encode($this->map);
    }
    final function __toString () {
        $encoded = $this->encoded();
        if ($encoded !== NULL) {
            return $encoded;
        }
        return $this->encode();
    }
    /**
     * Return a new JSONMessage for the intersection of a map with this message.
     *
     * @param array $map
     * @return JSONMessage
     */
    function intersect ($map) {
        return new JSONMessage(@array_intersect_assoc($this->map, $map));
    }
    /**
     * Return TRUE if the $key exists, FALSE otherwise.
     *
     * @param string $key
     * @return boolean
     */
    function has ($key) {
        return array_key_exists($key, $this->map);
    }
    /**
     * Get the keys of the wrapped associative array as a list
     */
    function keys () {
        return array_keys($this->map);
    }
    /**
     * Get the values of the wrapped associative array as a list
     */
    function values () {
        return array_values($this->map);
    }
    /**
     * Get the value of $key in $this->map if it is set, or a
     * $default not NULL, or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return any $this->map[$key] or $default
     * @throws any exception with a name error
     */
    function getDefault($key, $default=NULL) {
        if ($this->has($key)) {
            return $this->map[$key];
        }
        if ($default===NULL) {
            throw $this->exception('Name Error - '.$key.' missing');
        }
        return $default;
    }
    /**
     * Set the value of $key in $this->map to $default if it was not set yet,
     * return the (maybe updated) value of $this->map[$key] if it is a string
     * or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return any $this->map[$key] or $default
     */
    function setDefault($key, $default) {
        if ($this->has($key)) {
            return $this->map[$key];
        }
        $this->map[$key] = $default;
        return $default;
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * assert that it is a string or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return string $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getString($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (!is_string($value)) {
            throw $this->exception('Type Error - '.$key.' must be a String');
        }
        return $value;
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * assert that it is an integer or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return int $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getInt($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (!is_int($value)) {
            throw $this->exception('Type Error - '.$key.' must be an Integer');
        }
        return $value;
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * assert that it is a float or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return float $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getFloat($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (!is_float($value)) {
            throw $this->exception('Type Error - '.$key.' must be a Float');
        }
        return $value;
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * assert that it is an boolean or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return bool $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getBool($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (!is_bool($value)) {
            throw $this->exception('Type Error - '.$key.' must be a Boolean');
        }
        return $value;
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * assert that it is an array or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return array $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getArray($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (!is_array($value)) {
            throw $this->exception('Type Error - '.$key.' must be an Array');
        }
        return $value;
    }
    /**
     * Get the value of $key or a $default not NULL, assert that it is an list or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return array $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getList($key, $default=NULL) {
        $value = $this->getArray($key, $default);
        if (!self::is_list($value)) {
            throw $this->exception('Type Error - '.$key.' must be a List');
        }
        return $value;
    }
    /**
     * Get the value of $key or a $default not NULL, assert that it is a map or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return array $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getMap($key, $default=NULL) {
        $value = $this->getArray($key, $default);
        if (!self::is_map($value)) {
            throw $this->exception('Type Error - '.$key.' must be a Map');
        }
        return $value;
    }
    /**
     * Get a `JSONMessage` boxing the array value of $key or a $default not NULL.
     *
     * @param string $key
     * @param any $default
     *
     * @return JSONMessage boxing $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function getMessage($key, $default=NULL) {
        return new JSONMessage($this->getMap($key, $default));
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * as a string, or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return string $this->map[$key] or $default
     * @throws any exception with a name error
     */
    function asString($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (is_string($value)) {
            return $value;
        }
        return json_encode($value);
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * as an integer, or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return int $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function asInt($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (is_scalar($value)) {
            if (is_numeric($value) || is_bool($value)) {
                return intval($value);
            } else {
                throw $this->exception('Cast Error - '.$key.' must be numeric or boolean');
            }
        }
        throw $this->exception('Cast Error - '.$key.' must be a scalar');
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * as a float, or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return int $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function asFloat($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (is_scalar($value)) {
            if (is_numeric($value)) {
                return floatval($value);
            } else {
                throw $this->exception('Cast Error - '.$key.' must be numeric');
            }
        }
        throw $this->exception('Cast Error - '.$key.' must be a numeric scalar');
    }
    /**
     * Get the value of $key in $this->map or a $default not NULL,
     * as a boolean, or fail.
     *
     * @param string $key
     * @param any $default
     *
     * @return int $this->map[$key] or $default
     * @throws any exception with a name or type error
     */
    function asBool($key, $default=NULL) {
        $value = $this->getDefault($key, $default);
        if (is_scalar($value)) {
            if (is_numeric($value) || is_bool($value)) {
                return intval($value) === 1;
            } else {
                return strtoupper($value) === 'TRUE';
            }
        }
        throw $this->exception('Cast Error - '.$key.' must be a scalar');
    }
}

?>