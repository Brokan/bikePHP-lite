<?php
/**
 * EBFramework 0.x framework system file
 * Class work with page request parametrs
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class ebRequest{
    
        /**
	* @return Get $_POST or $_GET parametr
	*/
        public static function getParam($name,$defaultValue=null){
		return isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : $defaultValue);
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