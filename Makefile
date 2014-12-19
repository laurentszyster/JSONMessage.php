test: pull
	php test/test_is_list.php
	php test/test_is_map.php
	php test/test_construct.php
	php test/test_has.php
	php test/test_keys_values.php
	php test/test_getDefault.php
	php test/test_setDefault.php
	php test/test_getString.php
	php test/test_getInt.php
	php test/test_getFloat.php
	php test/test_getBool.php
	php test/test_getArray.php
	php test/test_getList.php
	php test/test_getMap.php
	php test/test_asString.php
	php test/test_asInt.php
	php test/test_asFloat.php
	php test/test_asBool.php

pull: deps deps/test-more-php

deps:
	mkdir -p deps

deps/test-more-php:
	svn checkout http://test-more-php.googlecode.com/svn/trunk/ deps/test-more-php

