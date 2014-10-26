<?php
/**
 * EBFramework 0.x framework system file
 * Work with URL
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0
 */
class ebUrl{

    /**
    * Get current page domain
    * @return Get domain name
    */
    public static function getDomain(){            
        return (!empty($_SERVER['HTTPS'])?'https://':'http://').$_SERVER['HTTP_HOST'];
    }

    /**
     * Get current subdomain
     * @return Get subdomain name
     */
    public static function getSubdomain(){
        $domain = $_SERVER['HTTP_HOST'];
        $domains = explode('.', $domain);
        if(empty($domains[0])){
            return '';
        }
        if(!in_array($domains[0], array('www'))){
            return $domains[0];
        }
        return '';
    }

    /**
     * @return Get Base URL
     */
    public static function getBaseUrl(){
        return (!empty($_SERVER['HTTPS'])?'https://':'http://').$_SERVER['HTTP_HOST'];            
    }

    /**
     * @return Get Current URL
     */        
    public static function getCurrentURL(){            
        return substr($_SERVER['REQUEST_URI'],1);
    }

    /**
     * Function to redirect from source code
     * @param String $action module/action
     * @param Array $params (optional) Parametrs of URL. Default - array()
     * @param Int $statusCode (optional) Status code. Default - 302
     */
    public static function redirect($action, $params=array(),$statusCode=302){
        $url = ebUrl::createURL($action, $params);
        header('Location: '.$url, true, $statusCode);
        die();
    }

    /**
     * Get front page action from configuration file
     * @global Array $configuration from configuration file
     * @return String action of default page
     */
    public static function getFrontPageAction(){
        global $configuration;
        $action='default/page';
        if(!empty($configuration->defaultPages['front']['module']) && !empty($configuration->defaultPages['front']['action'])){
            $action=$configuration->defaultPages['front']['module'].'/'.$configuration->defaultPages['front']['action'];
        }
        return $action;
    }

    /**
     * Function to redirect to front page
     * @param type $statusCode Status code. Default - 302
     */
    public static function redirectFront($statusCode=302){
        $action = self::getFrontPageAction();
        self::redirect($action, array(), $statusCode);
    }

    /**
     * Get 404 page action from configuration file
     * @global Array $configuration from configuration file
     * @return String action of page 404
     */
    public static function get404PageAction(){
        global $configuration;
        $action='default/404';
        if(!empty($configuration->defaultPages['404']['module']) && !empty($configuration->defaultPages['404']['action'])){
            $action=$configuration->defaultPages['404']['module'].'/'.$configuration->defaultPages['404']['action'];
        }
        return $action;
    }

    /**
     * Function to redirect to 404 page
     * @param type $statusCode Status code. Default - 302
     */
    public static function redirect404($statusCode=302){
        $action = self::get404PageAction();
        self::redirect($action, array(), $statusCode);
    }

    /**
     * Get current URL parametrs
     * @param Array $rules URL rules of project
     * @return Array
     */
    public static function getURLParams(){
        $config = ebConfiguration::getConfiguration();
        $rules = !empty($config['urlManager'])?$config['urlManager']:array();
        $currentURL = ebUrl::getCurrentURL();

        /*No URL Alias, get module and action from URL Configurations rules*/
        $explodedParams = explode('?', $currentURL);
        if(!empty($explodedParams[0])){
            $currentURL = $explodedParams[0];
        }
        $explodedParams = explode('/', $currentURL);
        $params = array(
            'module' => '',
            'action' => '',
        );                       
        foreach ($rules as $urlRule => $moduleEvent) {
            $params = self::validateURLRules($urlRule, $explodedParams);   
            if(!empty($params)){
                foreach ($params as $key => $value) {
                    if(!isset($_GET[$key])){
                        $_GET[$key]=$value;
                    }
                }
            }
            if(!empty($params) || $urlRule==$currentURL){
                $moduleParams = explode('/', $moduleEvent);
                //set or check module
                if ($moduleParams[0]!='<module>'){
                    $params['module']=$moduleParams[0];
                }
                //set or check action
                if ($moduleParams[1]!='<action>'){
                    $params['action']=$moduleParams[1];
                }                    
                if(!empty($_REQUEST)){
                    $params+=$_REQUEST;                    
                }
                return $params;
            }
        }
        return $params;
    }

    /**
     * Validate URL for URL rules
     * @param Array $urlRule
     * @param Array $explodedParams
     * @return boolean
     */
    private static function validateURLRules($urlRule, $explodedParams){                        
        $urlRulesValidate=explode('/', $urlRule);
        if(count($urlRulesValidate)!=count($explodedParams) && !empty($urlRulesValidate) && !empty($explodedParams)){
            return false;
        }
        $returnParams=array();
        foreach ($urlRulesValidate as $k => $key) {                      
            if (strpos($key,'<')==0 && strrpos($key,'>')){
                $key = str_replace(array('<','>'), '', $key);
                $params=explode(':', $key);                    
                if(empty($params['1'])){
                    return false;
                }

                $matches = array();
                switch ($params['1']) {
                    case '\d+':                            
                        if(!is_numeric($explodedParams[$k])){
                            return false;
                        }
                        $matches[0] = $explodedParams[$k];
                        break;
                    case '\w+': /*All symbols ar accepted*/
                        $matches[0] = $explodedParams[$k];
                        break;
                    default:
                        if(!$m=preg_match('/'.$params['1'].'/', $explodedParams[$k], $matches)){
                            return false;
                        }
                        break;
                }

                if($matches[0]!=$explodedParams[$k]){
                    return false;
                }
                $returnParams[$params['0']]=$matches[0];                    
            }elseif($key!=$explodedParams[$k]){
                return false;
            }
        }
        return $returnParams;
    }

    /**
     * Create URL
     * @param $action String The module and action to redirt
     * @param $params Array Parametrs for the URL
     * @return String URL
     */
    public static function createURL($action, $params=array()){
        $config = ebConfiguration::getConfiguration();

        $additionalParams=explode('/', $action);
        if(empty($additionalParams[0]) || empty($additionalParams[1])) return 'Incorrect action';
        $params['module']=$additionalParams[0];
        $params['action']=$additionalParams[1];

        $url='';

        if(empty($url)){
            $freeParams=count($params)+1;
            foreach ($config['urlManager'] as $urlRule => $moduleEvent) {                                
                $paramsNew=$params;
                if(self::checkModule($moduleEvent, $paramsNew)){
                    $freeParamsNew=0;                    
                    $urlNew = self::buildURL($urlRule, $paramsNew, $freeParamsNew);                                        
                    if(!empty($urlNew) && $freeParams>=$freeParamsNew){
                        $url=$urlNew;
                        $freeParams=$freeParamsNew;
                    }
                }
            }
        }
        return self::getBaseUrl().'/'.$url;
    }

    /**
     * Checl Module rule with params
     */
    private static function checkModule($moduleEvent, &$params){
        $moduleParams = explode('/', $moduleEvent);            
        if (!in_array($moduleParams[0], array($params['module'],'<module>')) || !in_array($moduleParams[1], array($params['action'],'<action>'))){
            return false;
        }             
        if($moduleParams[0]!='<module>'){
            unset($params['module']);
        }
        if($moduleParams[1]!='<action>'){
            unset($params['action']);            
        }
        return true;
    }

    /**
     * Build URL using rules and params
     */
    private static function buildURL($urlRule, $params, &$freeParamsNew){
        $url=$urlRule;
        $addingParams=array();
        $urlRulesValidate=explode('/', $urlRule);                 
        foreach ($urlRulesValidate as $k => $key) {                      
            if (strpos($key,'<')==0 && strrpos($key,'>')){
                $keyStrip = str_replace(array('<','>'), '', $key);                    
                $keyParams=explode(':', $keyStrip);                    
                if(empty($params[$keyParams[0]])){
                    return '';                    
                }
                $param=$params[$keyParams[0]];
                if(!$m=preg_match('/'.$keyParams['1'].'/', $param, $matches)){                        
                    return '';
                }                    
                $url = str_replace($key, $param, $url);
                unset($params[$keyParams[0]]);                    
            }
        }
        foreach ($params as $key => $value) {
            $addingParams[$key]=urlencode($key).'='.urlencode($value);
        }
        $freeParamsNew=count($addingParams);            
        return $url.(!empty($addingParams)?'?'.join('&', $addingParams):'');
    }

    /**
     * Array to URL transform function
     * @var Array
     */
    protected static $allow = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
            , 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
            , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_');

    protected static $convertfrom = array(
            'Ā', 'Ē', 'Ī', 'Ū', 'Ķ', 'Ļ', 'Ņ', 'Č', 'Š', 'Ģ', 'Ž'
            , 'ā', 'ē', 'ī', 'ū', 'ķ', 'ļ', 'ņ', 'č', 'š', 'ģ', 'ž'
            , 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ф', 'Ы', 'В', 'А'
            , 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч', 'С', 'М', 'И', 'Т', 'Б', 'Ю'
            , 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ф', 'ы', 'в', 'а'
            , 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч', 'с', 'м', 'и', 'т', 'б', 'ю'
            , '.', ',', ':', ';', ' '
            ,'.', ',', ':', ';', ' ', '--', '__', '-_-', "_-_");

    protected static $convertto = array('A', 'E', 'I', 'U', 'K', 'L', 'N', 'C', 'S', 'G', 'Z'
            , 'a', 'e', 'i', 'u', 'k', 'l', 'n', 'c', 's', 'g', 'z'
            , 'I', 'C', 'U', 'K', 'E', 'N', 'G', 'SH', 'SH', 'Z', 'H', 'F', 'I', 'V', 'A'
            , 'P', 'R', 'O', 'L', 'D', 'Z', 'E', 'JA', 'CH', 'S', 'M', 'I', 'T', 'B', 'JU'
            , 'i', 'c', 'u', 'k', 'e', 'n', 'g', 'sh', 'sh', 'z', 'h', 'f', 'i', 'v', 'a'
            , 'p', 'r', 'o', 'l', 'd', 'z', 'e', 'ja', 'ch', 's', 'm', 'i', 't', 'b', 'ju'
            , '_', '_', '_', '_', '_'
            , '_', '_', '_', '_', '_', '_', '_', '_', '_');

    protected static $notallowed = array(' ', '/', '\\', '?', '&', '@', '!', '$', '#', '%', '^', '*', '(', ')', '=', '[', ']', '"', "'", '<', '>', ',', '.', '`', '~');

    /**
     * Transform text to normal URL view
     * @param String $text
     * @return String
     */
    public static function  urlTextTransform($text) {

        $text = str_replace(self::$convertfrom, self::$convertto, $text);      
        $text = str_replace(self::$notallowed, "_", $text);

        $convertfrom = array('--', '__', '-_-', "_-_");
        $convertto = array('_');
        $text = str_replace($convertfrom, $convertto, $text);

        $returnText = "";
        for ($i = 0; $i < strlen($text); $i++) {
            if (in_array($text[$i], self::$allow)){
                $returnText.=$text[$i];
            }
        }
        $returnText = trim($returnText);
        return $returnText == "" ? "_" : $returnText;
    }

}