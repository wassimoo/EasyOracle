<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SQLDatabase
 *
 * @author sergo.beruashvili
 */
class SQLDatabase {

    //Private constructor for Singleton
    private function __construct() {
        
    }

    // OCI Database Instance
    private static $instance = false;

    /*
     * Returns instance of OCI Database , creates if necesary
     */

    public static function getInstance() {

        if (SQLDatabase::$instance === false) {
            try {
                SQLDatabase::$instance = @oci_connect(DB_USER, DB_PASS, DB_CONN_STRING,'AL32UTF8');
            } catch (Exception $e) {
                die($e->getMessage());
            }
            if (!SQLDatabase::$instance) {
                $e = oci_error();
                die($e['message']);
            }
        }

        return SQLDatabase::$instance;
    }

    /*
     * 
     * Takes $sql statement and $values containing key => value binding params
     * Returns Array (succes,data)
     *  success - boolean - true/false , if query executed
     *  data    - mixed - array of rows on success-true , error info on succes-false
     */

    public static function qin($sql, $values = Array()) {

        $database = SQLDatabase::getInstance();
        $statement = oci_parse($database, $sql);

        foreach ($values as $key => $val) {
            oci_bind_by_name($statement, $key, $val, 512);
        }

        if (@!oci_execute($statement)) {
            $errors = oci_error($statement);
            return Array('success' => false, 'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message']);
        }

        $result = Array();
        oci_fetch_all($statement, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

        return Array('success' => true, 'data' => $result);
    }

    /*
     * Takes sql statement and $values ,returns boolean , succes/failure of execute ( eg UPDATE , INSERT ... )
     * 
     */

    public static function qout($sql, $values = Array()) {

        $database = SQLDatabase::getInstance();
        $statement = oci_parse($database, $sql);

        foreach ($values as $key => $val) {
            oci_bind_by_name($statement, $key, $val, 512);
        }

        if (@!oci_execute($statement)) {
            $errors = oci_error($statement);
            return Array('success' => false, 'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message']);
        }

        return Array('success' => true, 'data' => '');
    }

    /*
     * Call the procedure , return array of success/true , and params filled up with OUT data ( if available )
     */

    public static function callProcedure($procedure, $values = Array()) {

        $database = SQLDatabase::getInstance();

        $sql = '';

        $keys = array_keys($values);

        if (sizeof($values) > 0) {
            $sql = 'BEGIN ' . $procedure . '(' . implode(',', $keys) . '); END;';
        } else {
            $sql = 'BEGIN ' . $procedure . '; END;';
        }

        $statement = oci_parse($database, $sql);

        foreach ($keys as $key) {
            oci_bind_by_name($statement, $key, $values[$key], 512);
        }


        if (@!oci_execute($statement)) {
            $errors = oci_error($statement);
            return Array('success' => false, 'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message'], 'params' => $values);
        }

        return Array('success' => true, 'data' => '', 'params' => $values);
    }

    /*
     * Call the function and , return arra of success/true , data returned by function and params filled up with OUT data ( if available )
     */

    public static function callFunction($procedure, $values = Array()) {

        $database = SQLDatabase::getInstance();

        $sql = '';

        $keys = array_keys($values);

        $result = NULL;

        if (sizeof($values) > 0) {
            $sql = 'BEGIN :callFunctionRes := ' . $procedure . '(' . implode(',', $keys) . '); END;';
        } else {
            $sql = 'BEGIN :callFunctionRes := ' . $procedure . '; END;';
        }

        $statement = oci_parse($database, $sql);

        oci_bind_by_name($statement, ':callFunctionRes', $result, 512);

        foreach ($keys as $key) {
            oci_bind_by_name($statement, $key, $values[$key], 512);
        }


        if (@!oci_execute($statement)) {
            $errors = oci_error($statement);
            return Array('success' => false, 'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message'], 'params' => $values);
        }

        return Array('success' => true, 'data' => $result, 'params' => $values);
    }

    /*
     * Call the function and , return arra of success/true , data returned by function and params filled up with OUT data ( if available )
     */

    public static function callCursorFunction($procedure, $values = Array()) {

        $database = SQLDatabase::getInstance();

        $sql = '';

        $keys = array_keys($values);

        $p_cursor = oci_new_cursor($database);

        if (sizeof($values) > 0) {
            $sql = 'BEGIN :callFunctionRes := ' . $procedure . '(' . implode(',', $keys) . '); END;';
        } else {
            $sql = 'BEGIN :callFunctionRes := ' . $procedure . '; END;';
        }

        $statement = oci_parse($database, $sql);

        oci_bind_by_name($statement, ':callFunctionRes', $p_cursor, -1, OCI_B_CURSOR);


        foreach ($keys as $key) {
            oci_bind_by_name($statement, $key, $values[$key], 512);
        }


        if (@!oci_execute($statement)) {
            $errors = oci_error($statement);
            return Array('success' => false, 'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message'], 'params' => $values);
        }

        oci_execute($p_cursor);

        $result = Array();
        oci_fetch_all($p_cursor, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

        return Array('success' => true, 'data' => $result, 'params' => $values);
    }

}

?>
