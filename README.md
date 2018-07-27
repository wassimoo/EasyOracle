EASY ORACLE
======================

Oracle class for PHP/Oracle

# Features :
<ul>
	<li>Database connection instance with SYSDBA option</li>
	<li>Schema switching</li>
	<li>Dynamic parameter binding ( except BLOB/CLOB ) .</li>
	<li>Calls Directly PL/SQL Functions/Procedures , supports OUT parameters also .</li>
</ul>

# Usage :

Creating instance:
* By default a localhost connection is established on default port.
```php
	$db = new Oracle(); 
```

* To specify host and other parameters:
```php
	$db = new Oracle($host, $port, $charset);
```

Establishing connection:

* Connection method is defined as follow
```php
	connect($username, $password, $isSysdba = false, $schema = null)
```

 * Switching Schema:
 ```php
 	$db->switchSchema("hr")
 ```
 NOTE: these methods may return ```DBCException``` on failure.

Select without parameters:

```php 
$sql = 'SELECT * FROM table';
$result = $db->qin($sql); 
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	foreach($result['data'] as $row) {
		var_dump($row);
	}
}
```

Select with parameters
```php   
$sql = 'SELECT * FROM table WHERE id = :id AND cat_id = :cat_id';
$params = Array(
	':id' => $id,
	':cat_id' => $cat_id
);
$result = $db->qin($sql, $params); 
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	foreach($result['data'] as $row) {
		var_dump($row);
	}
}
```

Update:
```php
$sql = 'UPDATE table SET cat_id = :cat_id WHERE id = :id';
$params = Array(
	':id' => $id,
	':cat_id' => $cat_id
);
$result = $db->qout($sql, $params);
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // Update/Insert succeeded

}
```

Cursor Function Calling:Cursor Function Calling:
```php
$funcName = 'function_name_that_returns_cursor';
$params = Array(
	':param1' => $param1,
	':param2' => $param2,
	':param3' => $param3,
	':param4' => $param4,
);
$result = $db->callCursorFunction($funcName, $params);               

// We must check if operation succeeded
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	$cursor_result = $result['data'];
}
```

Procedure Calling:
```php
// add_new_user = procedure , that inserts new user , it has out parameters
$procName = 'add_new_user';
$params = Array(
	':p_username' => $username,
	':p_password' => md5($password),
	':p_inserted_id' => NULL , // OUT parameter for new user id
	':p_error' => NULL , // OUT parameter for error number
	':p_error_description' => NULL  // OUT parameter for error description
);
$result = $db->callProcedure($procName, $params);
if($result['success']) {

	if($params[':p_error'] != NULL) {
		$error = $params[':p_error_description'];
	} else {
		$last_inserted_id = $params[':p_inserted_id'];
	}            

} else {
	$error = $result['data'];
}
```
