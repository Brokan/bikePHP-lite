<?php
/**
 * Connect to MySQL databases, execute querys, return database data
 * For PHP, EBFramework 1.x framework
 * 
 * Version history:
 * 1.0.2 (2014-09-22) - Function to check table for existence
 * 1.0.1 (2013-09-17) - Save SQL fail reason and SQL query to logs 
 * 1.0.0 (2012-08-11) - Created library
 *
 * @copyright Eduard Brokan, 2013
 * @version 1.0.2 (20143-08-22)
 */
class ebMysql implements ebDBInterface {

    var $connection = false;
    var $query = '';    
    var $params = array();
    var $result = array();
    
    /**
     * Connecting to the DB
     */
    function __construct($host, $username, $password, $database, $port='3306', $charset='UTF8') {
        $this->connection = false;
        //For PHP 5.4-
        //$connection = mysql_connect($host.':'.$port, $username, $password, true);
        //For PHP 5.5+
        $connection = mysqli_connect($host, $username, $password, null, (int)$port);
        if (mysqli_connect_errno()) {
            ebDebug::debugError('Failed to connect to MySQL: ' . mysqli_connect_error());
        }
        if (!$connection){
            return false;        
        }
        $this->connection = $connection;
        
        //$this->query('SET NAMES '.$charset, '');
        // If set database, then try set current db (For PHP 5.4-)
        if ($database) {
            $stat = $this->setDB($database);            
            if (!$stat){
                return false;
            }
        }   
    }
    
    /**
     *  Selecting DB 
     */
    private function setDB($database) {
        if (!$stat = mysqli_select_db($this->connection, $database)){
            ebDebug::debugError('Could not select DB: ' . $database);
        }else{
            ebDebug::debug('Selected DB: ' . $database);
        }
        return ($stat ? true : false);
    }
        
    public function execute(){
        if(!$sql=$this->getQuery()) return false;    
        $timeStart = microtime(true);       
        //For PHP 5.4-
        //$this->result = mysql_query($sql, $this->connection);        
        //For PHP 5.5+
        $this->result = mysqli_query($this->connection, $sql);
        $timeEnd = microtime(true);
        if (!$this->result) {
            if(mysql_errno() || mysql_error()){
                $message = tWord('Query fail', 'system').': ('.mysql_errno().')' . mysql_error();
                ebDebug::debugError($sql, $message);
                if(class_exists('logs')){
                    logs::errorLogs($message.'; SQL: '.$sql);
                }
            }
            return false;
        }
        ebDebug::debugDatabase($sql.' ; Run time:'.round($timeEnd-$timeStart,3));   
        return true;     
    }
    
    public function checkTableExist($table) {
        $this->params = array(
            'table' => $table,
        );
        $this->query = "SHOW TABLES LIKE :table ";
        return $this->queryScalar()?true:false;
    }
    
    public function getInfo(){        
        return array(
            tWord('Type') => 'MySQL',
            tWord('Version') => mysql_get_client_info(),
            tWord('Host info') => mysql_get_host_info(),
            tWord('Server info') => mysql_get_server_info(),
        );        
    }
    
    public function getQuery(){
        if (!$this->query || !$this->connection) return false;
        $sql=$this->query;
        if(!empty($this->params)){
            foreach ($this->params as $param => $value) {   
                //$value = var_export($value,true);
                //$value = "'".mysql_real_escape_string($value)."'";
                $value = "'".str_replace("'","\'",$value)."'";
                if(strpos($sql, ':'.$param.' ')){
                    $sql=str_replace(':'.$param.' ', $value.' ', $sql);
                }else{
                    $sql=str_replace(':'.$param, $value.' ', $sql);
                }                
            }
        }
        return $sql;
    }
    
    public function createCommand($query){
        $this->query=$query;
        $this->result=array();
        $this->params=array();
    }


    public function query(){
        if (!$this->execute()) return false;
        return $this->result;
    }
   
    public function queryAll(){
        if (!$this->execute()) return false;
        $return = array();
        while($object = mysqli_fetch_object($this->result)) {
            $row=array();
            foreach ($object as $key => $value) {
                $row[$key]=$this->valueRevert($value);
            }
            if(!empty($row)) $return[]=$row;
        }
        return $return;
    }
    
    public function queryRow(){
        if (!$this->execute()) return false;
        $return = array();
        while($object = mysqli_fetch_object($this->result)) {
            $row=array();
            foreach ($object as $key => $value) {
                $row[$key]=$this->valueRevert($value);
            }
            return $row;
        }
        return $return;
    }
    
    public function queryScalar(){
        if (!$this->execute()) return false;
        $return = array();
        while($object = mysqli_fetch_object($this->result)) {
            $row=array();
            foreach ($object as $key => $value) {
                return $this->valueRevert($value);
            }
            return $row;
        }
        return $return;
    }
    
    public function bindParam($param, $value){ 
        $this->params[$param]=$value;
    }
       
    /** 
     * Close connection to db 
     */
    function close() {
        if (!$this->connection)  return false;
        @mysql_close($this->connection);
    }
    
    public function save($table, $fields, $id = 0) {        
        if (empty($table) || !is_array($fields) || !count($fields)){
            return false;
        }
        if (!$id){
            return $this->insert($table, $fields);
        }
        else{
            return $this->update($table, $fields, $id);
        }
    }

    private function insert($table, $fields) {        
        $queryColumns = array();
        $queryParams = array();
        foreach ($fields as $key => $val) {
            if (in_array($key, array("id")))
                continue;
            $queryColumns[$key] = " `" . $key . "`";
            $queryParams[$key] = ':' . $key.' ';
        }

        $this->createCommand("INSERT INTO $table (" . join(' ,', $queryColumns) . ") VALUES (" . join(' ,', $queryParams) . ")");        
        foreach ($queryParams as $key => $param) {
            $this->bindParam($key, $fields[$key]);
        }   
        $this->execute();
        
        
        $this->createCommand("SELECT LAST_INSERT_ID()");
        return $this->queryScalar();    
    }

    private function update($table, $fields, $id) {
        $querySet = array();
        $queryParams = array();
        foreach ($fields as $key => $val) {
            if (in_array($key, array("id")))
                continue;
            $queryParams[$key] = ':' . $key.' ';
            $querySet[$key] = " `" . $key . "`=" . $queryParams[$key].' ';
        }
        $this->createCommand("UPDATE $table SET " . join(',', $querySet) . " WHERE id = :id ");

        foreach ($queryParams as $key => $param) {
            $this->bindParam($key, $fields[$key]);
        }
        $this->bindParam("id", $id);
        $this->execute();        
        return $id;
    }

    public function delete($table, $id=0) {           
        $this->createCommand("DELETE FROM $table WHERE id = :id ");
        $this->bindParam("id", $id);
        $this->execute();        
        return $id;
    }
    
    /**
     * Create database
     * @param type $database
     * @return type 
     */
    public function createDatabase($database){           
        $this->createCommand("CREATE DATABASE `".$database."` ");
        return $this->execute(); 
    }
    
    private function valueRevert($value){
        return str_replace("\'","'",$value);
    }
    
}

?>
