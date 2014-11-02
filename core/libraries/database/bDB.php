<?php
/**
 * bikePHP 0.x framework system file
 * Connect to databases, execute querys, return database data
 * 
 * Version history:
 * 1.0.0 (2014-08-22) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-08-22)
 */
class bDB {

    /**
     * List of connections to databases
     * @var Array 
     */
    private static $connections = array();
    
    /**
     * List of connected to databases connections
     * @var Array 
     */
    private static $connected = array();
    
    /**
     * Current connection to database
     * @var type 
     */
    private $dbConnetionName;
    private $dbConnetion;    
    
    /**
     * Construction of database connection
     * @global type $configuration
     * @param String $db Connection name
     * @return boolean Success of connection
     */
    public function __construct($db) {
        $connection = bDB::getConnection($db);
        if(!$connection){
            return false;
        }        
        $this->dbConnetionName=$db;
        switch ($connection['type']) {
            case 'mysql':
                if(empty($connection['port'])){
                    $connection['port']='3306';
                }
                if(empty($connection['charset'])){
                    $connection['charset']='UTF8';
                }
                $this->dbConnetion = new bMysql($connection['host'], $connection['username'], $connection['password'], $connection['database'], $connection['port'], $connection['charset']);                
                break;
        }
        return true;
    }

    /**
     * Set connections of databases
     * @param Array $connections
     */
    public static function setConnections($connections){
        array_replace_recursive(self::$connections, $connections);
    }
    
    /**
     * Get connection of database
     * @param Array $db
     * @return Array connection parametrs or false if not found
     */
    private static function getConnection($db){
        if(empty(self::$connections[$db])){
            bDebug::debugError("Can't find databses '".$db."' connetion");
            return false;
        }
        return self::$connections[$db];
    }
    
    /**
     * Get connected connection to database
     * @param String $db connection name
     * @return Connection to databse, or false if not exist
     */
    private static function getConnected($db){
        if(empty(self::$connections[$db])){
            return false;
        }
        return self::$connections[$db];
    }
    
    /**
     * Set connection to database
     * @param String $db Connection name
     * @param Connection $connection
     */
    private static function setConnected($db, $connection){
        self::$connected[$db]=$connection;
    }
    
    /**
     * Connetion to the database
     * @param String $db Connection name (optional - by default use "Default")
     */
    public static function conn($db='default'){     
        $connection = self::getConnected($db);
        if (!empty($connection) && !empty($connection->dbConnetion)){
            //Clear connection for new use
            $connection->query='';
            $connection->result=array();
            $connection->params=array();            
            return $connection;
        }
        
        $newConnection = new bDB($db);
        self::setConnected($db, $newConnection);
        return $newConnection;
    }
    
    public function checkTableExist($table){
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->checkTableExist($table);
    }
    
    /**
     * Get information about connection
     */
    public function getInfo(){
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->getInfo();
    }
    
    /**
     * Execute the query
     * @return boolean Success of Query execution 
     */
    public function execute(){        
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->execute();
    }
    
    /**
     * Set SQL to the command
     */
    public function createCommand($query){
        if(empty($this->dbConnetion)){
            return $this;
        }
        $this->dbConnetion->createCommand($query);
        return $this;
    }
        
    /**
     * Add parameter to query
     * @param string $param Will replace this
     * @param string $value Will replace to this
     * @return Object command
     */
    public function bindParam($param, $value){
        if(empty($this->dbConnetion)){
            return $this;
        }
        $this->dbConnetion->bindParam($param, $value);
        return $this;
    }
    
    /**
     * Execute query
     * @return Object Result of query execution
     */
    public function query(){
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->query();
    }
    
    /**
     * Execute query
     * @return Array Result of query execution
     */
    public function queryAll(){        
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->queryAll();
    }
    
    /**
     * Execute query
     * @return Array Result of query execution first row
     */
    public function queryRow(){
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->queryRow();
    }

    /**
     * Execute query
     * @return String Result of query execution first row first column
     */
    public function queryScalar(){
        if(empty($this->dbConnetion)){
            return false;
        }
        return $this->dbConnetion->queryScalar();
    }
    
   /**
    * Insert or Update row in table
    * @param String $table Table name
    * @param Array $fields Row values
    * @param Int $id Optional. If use ID, fill UPDATE row
    * @return Int Id of saved row to table
    */
    public function save($table, $fields, $id=0){        
        if(empty($this->dbConnetion)){
            return false;        
        }
        return $this->dbConnetion->save($table, $fields, $id);
    }
    
    /**
     * Delete row from table
     * @param String $table Table name
     * @param Int $id Table row Id value
     * @return Int Id of deleted row 
     */
    public function delete($table, $id=0){
        if(empty($this->dbConnetion)){
            return false;        
        }
        return $this->dbConnetion->delete($table, $id);
    }
    
    /**
     * Create database
     * @param type $database
     * @return type 
     */
    public function createDatabase($database){
        if(empty($this->dbConnetion)){
            return false;        
        }
        return $this->dbConnetion->createDatabase($database);
    }
    
    /**
     * Build and return query string
     * @return String Query string 
     */
    public function getQuery(){
        if(empty($this->dbConnetion)){
            return false;        
        }
        return $this->dbConnetion->getQuery();
    }
    
    /**
     * Check database connection
     * @param String $type
     * @param String $host
     * @param String $username
     * @param String $password
     * @param String $port 
     * @return Bool Success (true) or not (false)
     */
    public static function checkConnection($type, $host, $username, $password, $port){
        switch ($type) {
            case 'mysql':
                if(empty($port)){
                    $port='3306';    
                }
                try{
                    $connection = new bMysql($host, $username, $password, '', $port, ''); 
                    return (!empty($connection->connection));
                }catch (Exception $exc) {
                    $message = tWord("Can't connect to host", "system")." - ".$exc;
                    bDebug::debugError($message);
                    return false;
                } 
                break;
        }
        return false;
    }
        
    /**
     * Return bikePHP supported databases
     * @return Array 
     */
    public static function supportDatabasesTypes(){
        return array(
            'mysql'=>'MySQL',
        );
    }
}
