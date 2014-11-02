<?php
/**
 * bikePHP 0.x framework system file
 * Class work with page request parametrs
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class bRequest{
    
        /**
	 * Get Request parameter value of key
         * @param String $key
         * @param Object $default (Optional) Default value
         * @return Object Value or default value
	 */
        public static function getParam($key, $default=null){
            if (isset($_POST[$key])) {
                return $_POST[$key];
            } else {
                return self::getGet($key, $default);
            }	
	}
        
        /**
         * Get all requests
         */
        public static function getAll(){
            return $_REQUEST;
        }
        
        /**
         * Get GET value of key
         * @param String $key
         * @param Object $default (Optional) Default value
         * @return Object Get value or default value
         */
        public static function getGet($key, $default=null){
            if (isset($_GET[$key])) {
                return $_GET[$key];
            } else {
                return $default;
            }
        }
        
        /**
         * Set GET value for key
         * @param String $key
         * @param Object $value
         */
        public static function setGet($key, $value){
            $_GET[$key]=$value;
        }
        /**
         * Get IP of user
         * @return String
         */
        public static function getIP(){
            $ip="";
            if (!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet            
                $ip=$_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy            
                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{            
                $ip=$_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }
        
}