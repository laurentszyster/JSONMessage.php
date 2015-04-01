JSONMessage.php
===
[![Build Status](https://travis-ci.org/laurentszyster/JSONMessage.php.svg)](https://travis-ci.org/laurentszyster/JSONMessage.php)

A convenience class to validate a PHP associative array as a typed JSON message with mandatory properties and default values.

Requirements
---
- [Box an associative array with conveniences for JSON message validation](#validate-json-messages)
- [Implement `ArrayAccess`, `Traversable` and `Countable`](#quack-like-an-array)
- [Validate URL encoded forms with the same conveniences](#url-encoded-forms)
- Support PHP 5.2, 5.3 and 5.4

Also,

- Define a uniform JSON representation of PHP arrays

Synopsis
---
Use `JSONMessage` whenever a PHP function must validate the presence of keys, the type of values and supply defaults for an associative array.

### Validate JSON Messages

JSON is everywhere: in the (JavaScript) web client; in HTTP requests and responses; in SQL databases; in software configuration files, etc.

Eventually all those JSON messages make their way in PHP.

And all PHP arrays eventually find their way out as JSON messages.

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

Without the convenience of `JSONMessage`, this function would look something like this error-prone, incomplete and butt-ugly code:

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

### URL Encoded Forms

Validating JSON decoded messages in one use case of JSONMessage. The other is the validation of URL encoded forms as parsed into PHP's superglobal `$_GET` and `$_POST` arrays. So, whenever possible a boolean, float, integer or string value will be casted by the `getBool`, `getFloat`, `getInt` and `getString` methods of `JSONMessage`. 

For instance, this use of the queryOptions defined above will work in a script handling a parsed URL encoded form instead of a JSON message:

~~~php
list($table, $params, $columns, $offset, $limit) = queryOptions($_POST);
~~~

Practicality beats purity.

### Uniform JSON

JSONMessage is also usefull when testing associative arrays, because it provides a uniform JSON representation.

~~~php
$uniform = '{"a":1,"b":3,"d":4}';
$message = new JSONMessage(array("b"=>3,"a"=>1,"d"=>4));
$message->uniform() === $uniform;
~~~

Having a uniform representation also means that we can identify a message by distributed key or validate the digital signature of a message.