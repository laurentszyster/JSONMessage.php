JSONMessage.php
===
[![Build Status](https://travis-ci.org/laurentszyster/JSONMessage.php.svg)](https://travis-ci.org/laurentszyster/JSONMessage.php)

A convenience class to validate a PHP associative array as a typed JSON message with mandatory properties and default values.

Requirements
---
- [Box an associative array with conveniences for JSON message validation](#validate-json-messages)
- [Validate `$_GET` and `$_POST` with the same conveniences](#validate-get-and-post)
- [Implement `ArrayAccess`, `Traversable` and `Countable`](#quack-like-an-array)
- [Support I/O lists with a `__toString` magic method](#support_io_lists)
- [Define a uniform JSON representation of PHP arrays](#uniform-json)
- Support PHP 5.2, 5.3 and 5.4

### Introduction

JSON is everywhere: in the (JavaScript) web client; in HTTP requests and responses; in SQL databases; in software configuration files, etc. 

Eventually all those JSON messages make their way in PHP. 

~~~php
$message = JSONMessage::parse('{"foo":"bar"}');
~~~

And all PHP arrays eventually find their way out *as* JSON messages. 

~~~php
$message = new JSONMessage($_POST);
~~~

...

### Validate JSON Messages

Use `JSONMessage` whenever a PHP function must validate the presence of keys, the type of values and supply defaults for an associative array.

For instance in a function that validates options for an SQL query builder:

~~~php
function queryOptions($array) {
    // box the original option's array
    $m = new JSONMessage($array);
    // mandatory typed values
    $table = $m->getString('table');
    $params = $m->getMap('params');
    // optional typed defaults
    $columns = $m->getList('columns', array());
    $offset = $m->getInt('offset', 0);
    $limit = $m->getInt('limit', 30);
    // return valid query options
    return array($table, $params, $columns, $offset, $limit);
}
~~~

Without the convenience of `JSONMessage`, this function would look something like:

~~~php
function queryOptions($array) {
    // mandatory values
    if (isset($array['table']) && is_string($array['table'])) {
        $table = $array['table'];
    } else {
        throw new Exception("Name or Type error for 'table'");
    }
    if (isset($array['params']) && is_array($array['params'])) {
        $params = $array['params'];
    } else {
        throw new Exception("Name or Type error for 'params'");
    }
    // optional defaults
    if (isset($array['columns']) {
        if (is_array($array['columns'])) {
            $params = $array['columns'];
        } else {
            throw new Exception("Type error for 'columns'");
        }
    } else {
        $columns = array();
    }
    if (isset($array['offset']) {
        if (is_integer($array['offset'])) {
            $offset = $array['offset'];
        } else {
            throw new Exception("Type error for 'offset'");
        }
    } else {
        $offset = 0;
    }
    if (isset($array['limit']) {
        if (is_integer($array['limit'])) {
            $limit = $array['limit'];
        } else {
            throw new Exception("Type error for 'limit'");
        }
    } else {
        $limit = 0;
    }

    return array($table, $params, $columns, $offset, $limit);
}
~~~

Definitively not the kind of this error-prone, incomplete and butt-ugly code you want to write nor read.

### Validate `$_POST` And `$_GET`

A `JSONMessage` can also be used to validate the HTTP query and URL encoded form parsed into PHP's superglobal `$_GET` and `$_POST` arrays.

For instance, the function `queryOptions` can be applied on a parsed URL encoded form instead of a JSON message:

~~~php
list($table, $params, $columns, $offset, $limit) = queryOptions($_POST);
~~~

Whenever possible a boolean, float, integer or string value will be casted by the `getBool`, `getFloat`, `getInt` and `getString` methods of `JSONMessage`.

### Quack Like An Array

A `JSONMessage` behaves somewhat like a PHP associative array. 

It is accessible as an array :

~~~php
function queryOptions(JSONMessage $message) {
    // mandatory values
    $table = $message['table'];
    $params = $message['params'];
    // optional defaults
    $columns = isset($message['columns']) ? $message['columns'] : NULL;
    $offset = isset($message['offset']) ? intval($message['offset']) : 0;
    $limit = isset($message['limit']) ? intval($message['limit']) : 30;
    return array($table, $params, $columns, $offset, $limit);
}
~~~

It is also traversable and countable :

~~~php
function echoJSONMessageKeys (JSONMessage $properties) {
    if (count($properties) > 0) {
        foreach($properties as $key => $value) {
            echo strval($key)."\n";
        }
    }
}
~~~

And that's where the kool-aid syntactic sugar must stop.

### Support I/O Lists

A `__toString` magic method is provided and `JSONMessage` can be used everywhere an object may be casted as a string. 

For instance, this will log the JSON string of `$message` :

~~~php
error_log($message);
~~~

And this will output a JSON list for the `$messages` :

~~~php
echo '['.implode(',',$messages).']';
~~~

Note that, if the message caches a JSON encoded string then this string will use be used to avoid re-encoding the message.

### Uniform JSON

JSONMessage is also usefull when testing associative arrays, because it provides a uniform JSON representation.

~~~php
$uniform = '{"a":1,"b":3,"d":4}';
$message = new JSONMessage(array("b"=>3,"a"=>1,"d"=>4));
$message->uniform() === $uniform;
~~~

Having a uniform representation also means that we can identify a message by distributed key or validate the digital signature of a message.

### Support PHP 5.3

To support PHP 5.3 shims are provided for `json_last_error`, `json_last_error_message` and  `JsonSerializable`.