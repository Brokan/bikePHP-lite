<?php
/**
 * bikePHP 0.x framework system file
 * Work with URL
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0
 */
class bURL{

    /**
     * URL manager
     * @var Array
     */
    private static $urlRules = array();
    
    /**
     * Default pages parametrs such as front page, 404
     * @var Array 
     */
    private static $defaultPages = array();
    
    /**
     * Domains of components (CSS files, JS files)
     * @var Array 
     */
    private static $domains = array();
    
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
     * Set rules for links
     * @param Array $urlRules
     */
    public static function setURLRules($urlRules){
        self::$urlRules = array_replace_recursive(self::$urlRules, $urlRules);
    }

    /**
     * Get URL rules
     * @return Array
     */
    private static function getURLRules(){
        return self::$urlRules;
    }
    
    /**
     * Set default pages parameters
     * @param Array $defaultPages
     */
    public static function setDefaultPages($defaultPages){
        self::$defaultPages = array_replace_recursive(self::$defaultPages, $defaultPages);
    }
    
    /**
     * Get page action
     * @param String $page Page name
     * @param Array $params (return; optional) Params of page
     * @return String "module/action" of page or false if not found
     */
    public static function getDefaultPage($page, &$params=array()){
        if(empty(self::$defaultPages[$page]['module']) || empty(self::$defaultPages[$page]['action'])){
            bDebug::debugError('Default page '.$page.' not found');
            return false;
        }
        $params = !empty(self::$defaultPages[$page]['params'])?self::$defaultPages[$page]['params']:array();
        return self::$defaultPages[$page]['module'].'/'.self::$defaultPages[$page]['action']; 
    }

    /**
     * Redirect to page
     * @param String $page Page name
     * @param type $statusCode (optional) Status code. Default - 302
     * @return Boolean false if no page found
     */
    public static function redirectToDefaultPage($page, $statusCode=302){
        $params = array();
        $action = self::getDefaultPage($page, $params);
        if(!$action){
            bDebug::debugError("Can't redirect to page ".$page);
            return false;
        }
        self::redirect($action, $params, $statusCode);
    }
    
    /**
     * Set domains for CSS, JS and others files
     * @param Array $domains
     */
    public static function setDomains($domains){
        self::$domains = array_replace_recursive(self::$domains, $domains);
    }
    
    /**
     * Get domain of file type
     * @param String $type
     * @return String domain
     */
    public static function setFileTypeDomain($type){
        if(!empty(self::$domains[$type])){
            return self::$domains[$type];
        }
        return self::getDomain();
    }
    
    /**
     * Function to redirect from source code
     * @param String $action module/action
     * @param Array $params (optional) Parametrs of URL. Default - array()
     * @param Int $statusCode (optional) Status code. Default - 302
     */
    public static function redirect($action, $params=array(), $statusCode=302){
        $url = bUrl::createURL($action, $params);
        header('Location: '.$url, true, $statusCode);
        die();
    }

    /**
     * Get front page action from configuration file
     * @return String action of default page
     */
    public static function getFrontPageAction(){
        return self::getDefaultPage('front');
    }

    /**
     * Function to redirect to front page
     * @param type $statusCode Status code. Default - 302
     */
    public static function redirectFront($statusCode=302){
        self::redirectToDefaultPage('front', $statusCode);
    }

    /**
     * Get 404 page action from configuration file
     * @return String action of page 404
     */
    public static function get404PageAction(){
        return self::getDefaultPage('404');
    }

    /**
     * Function to redirect to 404 page
     * @param type $statusCode Status code. Default - 302
     */
    public static function redirect404($statusCode=302){
        self::redirectToDefaultPage('404', $statusCode);
    }

    /**
     * Get current URL parametrs
     * @param Array $rules URL rules of project
     * @return Array
     */
    public static function getURLParams(){
        $currentURL = bUrl::getCurrentURL();
        $explodedParams = self::explodeURLToParams($currentURL);
        $params = array(
            'module' => '',
            'action' => '',
        );
        /*Get module and action from URL Configurations rules*/
        foreach (self::getURLRules() as $urlRule => $moduleEvent) {
            $params = self::validateURLRules($urlRule, $explodedParams);   
            self::setParamsToGet($params);
            if(!empty($params) || $urlRule==$currentURL){
                return self::fillParams($params, $moduleEvent);
            }
        }
        return $params;
    }

    /**
     * Explode URL to params
     * @param String $url
     * @return Array
     */
    private static function explodeURLToParams($url){
        $params = explode('?', $url);
        if(!empty($params[0])){
            $url = $params[0];
        }
        return explode('/', $url);
    }

    /**
     * Set parameters to global GET
     * @param Array $params
     */
    private static function setParamsToGet($params){
        if(!empty($params)){
            foreach ($params as $key => $value) {
                if(bRequest::getGet($key)!==null){
                    bRequest::setGet($key, $value);
                }
            }
        }
    }
    
    /**
     * Fill parameters with rule event
     * @param Array $params
     * @param String $moduleEvent
     * @return Array
     */
    private static function fillParams($params, $moduleEvent){
        $moduleParams = explode('/', $moduleEvent);
        //set or check module
        if ($moduleParams[0]!='<module>'){
            $params['module']=$moduleParams[0];
        }
        //set or check action
        if ($moduleParams[1]!='<action>'){
            $params['action']=$moduleParams[1];
        }
        $requests = bRequest::getAll();
        if(!empty($requests)){
            $params+=$requests;                    
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
        $additionalParams=explode('/', $action);
        if(empty($additionalParams[0]) || empty($additionalParams[1])){
            bDebug::debugError('Not correct create URL action - '.$action);
            return 'Incorrect action';
        }
        $params['module']=$additionalParams[0];
        $params['action']=$additionalParams[1];

        $url='';

        $freeParams=count($params)+1;
        foreach (self::getURLRules() as $urlRule => $moduleEvent) {                                
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