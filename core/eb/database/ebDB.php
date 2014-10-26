<?php
/**
 * Connect to databases, execute querys, return database data
 * For PHP, EBFramework 1.x framework
 * 
 * Version history:
 * 1.1.2 (2014-08-22) - Add function to check table for existenc
 * 1.1.1 (2013-09-17) - Save exceptions to logs
 * 1.1.0 (2013-03-30) - Add database connection check, database creation
 * 1.0.0 (2012-08-11) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.1.2 (2014-08-22)
 */
class ebDB {

    private $dbConnetionName;
    private $dbConnetion;    
    
    function __construct($db) {
        global $configuration;
        if(empty($configuration->databases[$db])){
            ebDebug::debugError("Can't find databses '".$db."' connetion");
            return false;
        }        
        $this->dbConnetionName=$db;        
        $host = $configuration->databases[$db]['host'];
        $username = $configuration->databases[$db]['username'];
        $password = $configuration->databases[$db]['password'];
        $database = $configuration->databases[$db]['database'];
        $port = !empty($configuration->databases[$db]['host'])?$configuration->databases[$db]['host']:'';
        $charset = !empty($configuration->databases[$db]['charset'])?$configuration->databases[$db]['charset']:'UTF8';
        switch ($configuration->databases[$db]['type']) {
            case 'mysql':
                if(empty($port)) $port='3306';
                $this->dbConnetion = new ebMysql($host, $username, $password, $database, $port, $charset);                
                break;
        }                
    }

    /**
     * Connetion to the database
     */
    static function conn($db='default'){     
        global $DBConnetions;        
        if (!empty($DBConnetions[$db]) && !empty($DBConnetions[$db]->dbConnetion)){            
            $DBConnetions[$db]->query='';
            $DBConnetions[$db]->result=array();
            $DBConnetions[$db]->params=array();            
            return $DBConnetions[$db];
        }
                
        $command = $DBConnetions[$db] = new ebDB($db);        
        return $command;
    }
    
    public function checkTableExist($table){
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->checkTableExist($table);
    }
    
    /**
     * Get information about connection
     */
    public function getInfo(){
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->getInfo();
    }
    
    /**
     * Execute the query
     * @return boolean Success of Query execution 
     */
    public function execute(){        
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->execute();
    }
    
    /**
     * Set SQL to the command
     */
    public function createCommand($query){
        if(empty($this->dbConnetion)) return $this;
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
        if(empty($this->dbConnetion)) return $this;
        $this->dbConnetion->bindParam($param, $value);
        return $this;
    }
    
    /**
     * Execute query
     * @return Object Result of query execution
     */
    public function query(){
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->query();
    }
    
    /**
     * Execute query
     * @return Array Result of query execution
     */
    public function queryAll(){        
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->queryAll();
    }
    
    /**
     * Execute query
     * @return Array Result of query execution first row
     */
    public function queryRow(){
        if(empty($this->dbConnetion)) return false;
        return $this->dbConnetion->queryRow();
    }

    /**
     * Execute query
     * @return String Result of query execution first row first column
     */
    public function queryScalar(){
        if(empty($this->dbConnetion)) return false;
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
        if(empty($this->dbConnetion)) return false;        
        return $this->dbConnetion->save($table, $fields, $id);
    }
    
    /**
     * Delete row from table
     * @param String $table Table name
     * @param Int $id Table row Id value
     * @return Int Id of deleted row 
     */
    public function delete($table, $id=0){
        if(empty($this->dbConnetion)) return false;        
        return $this->dbConnetion->delete($table, $id);
    }
    
    /**
     * Create database
     * @param type $database
     * @return type 
     */
    public function createDatabase($database){
        if(empty($this->dbConnetion)) return false;        
        return $this->dbConnetion->createDatabase($database);
    }
    
    /**
     * Build and return query string
     * @return String Query string 
     */
    public function getQuery(){
        if(empty($this->dbConnetion)) return false;        
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
                if(empty($port)) $port='3306';    
                try{
                    $connection = new ebMysql($host, $username, $password, '', $port, ''); 
                    return (!empty($connection->connection));
                }catch (Exception $exc) {
                    $message = tWord("Can't connect to host", "system")." - ".$ext;
                    ebDebug::debugError($message);
                    if(class_exists('logs')){
                        logs::errorLogs($message);
                    }
                    return false;
                } 
                break;
        }
        return false;
    }
        
    /**
     * Return EBFramework supported databases
     * @return Array 
     */
    public static function supportDatabasesTypes(){
        return array(
            'mysql'=>'MySQL',
        );
    }
}
