PHP-Oracle-SQLDatabase
======================

SQLDatabase class for PHP/Oracle

read usage and implementation at http://ogres.ge/site/php-class-for-oracle-database/



Features :
<ul>
    <li>Single connection to the database ( using Singleton pattern ) .</li>
	<li>Dynamic parameter binding ( except BLOB/CLOB ) .</li>
	<li>Calls Directly PL/SQL Functions/Procedures , supports OUT parameters also .</li>
</ul>

Usage :
You must set up connection with your user/password in getInstance method , or define() them
<pre>SQLDatabase::$instance = @oci_connect(DB_USER, DB_PASS, DB_CONN_STRING,'AL32UTF8');</pre>
Select without parameters:
<pre>$sql = 'SELECT * FROM table';
$result = SQLDatabase::qin($sql); 
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	foreach($result['data'] as $row) {
		var_dump($row);
	}
}</pre>
Select with parameters
<pre>    
$sql = 'SELECT * FROM table WHERE id = :id AND cat_id = :cat_id';
$params = Array(
	':id' => $id,
	':cat_id' => $cat_id
);
$result = SQLDatabase::qin($sql, $params); 
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	foreach($result['data'] as $row) {
		var_dump($row);
	}
}</pre>
Update:
<pre>$sql = 'UPDATE table SET cat_id = :cat_id WHERE id = :id';
$params = Array(
	':id' => $id,
	':cat_id' => $cat_id
);
$result = SQLDatabase::qout($sql, $params);
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // Update/Insert succeeded

}</pre>
Cursor Function Calling:Cursor Function Calling:
<pre>$funcName = 'function_name_that_returns_cursor';
$params = Array(
	':param1' => $param1,
	':param2' => $param2,
	':param3' => $param3,
	':param4' => $param4,
);
$result = SQLDatabase::callCursorFunction($funcName, $params);               

// We must check if operation succeeded
if(!$result['success']) { // operation failed
	$error = $result['data'];
} else { // operation succeeded
	$cursor_result = $result['data'];
}</pre>
Procedure Calling:
<pre>// add_new_user = procedure , that inserts new user , it has out parameters
$procName = 'add_new_user';
$params = Array(
	':p_username' => $username,
	':p_password' => md5($password),
	':p_inserted_id' => NULL , // OUT parameter for new user id
	':p_error' => NULL , // OUT parameter for error number
	':p_error_description' => NULL  // OUT parameter for error description
);
$result = SQLDatabase::callProcedure($procName, $params);
if($result['success']) {

	if($params[':p_error'] != NULL) {
		$error = $params[':p_error_description'];
	} else {
		$last_inserted_id = $params[':p_inserted_id'];
	}            

} else {
	$error = $result['data'];
}</pre>
