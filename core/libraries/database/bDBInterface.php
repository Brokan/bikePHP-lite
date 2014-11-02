<?php

interface bDBInterface {

    function __construct($host, $username, $password, $database, $port='3306', $charset='UTF8');
    
    public function execute();
    
    public function checkTableExist($table);
    
    public function getInfo();
    
    public function createCommand($query);
    
    public function getQuery();

    public function query();
   
    public function queryAll();
    
    public function queryRow();
    
    public function queryScalar();
            
    public function bindParam($param, $value);
    
    public function save($table, $fields, $id = 0);
    
    public function delete($table, $id =0);
    
    public function createDatabase($database);
    
    public function close();
    
}
?>