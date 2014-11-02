<?php
/**
 * bikePHP 0.x framework system file
 * General file!
 * Control modules and actions use.
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class bController{
    
    public $module;
    public $action;
    public $params=array();
    public $moduleLocation='';
    public $theme='default';
    public $renderLayout='';
    public $renderHTMLLayout='html.php';
    
    /*Variables for Layout*/
    public $cssFiles = array();
    public $jsFiles = array();
    public $jsScripts = '';
    
    public $cssFilesOrder = array();
    public $jsFilesOrder = array();
    public $jsScriptsOrder = array();
    
    public $pageTitle = '';
    public $pageDescription = '';
    public $pageKeywords = '';
    
    function __construct(){ 
        $this->theme = bTheme::getTheme();
    }
        
    /**
     * Run project
     */
    public function run(){
        bDebug::debug('Start RUN theme "'.$this->theme.'"');
        $this->params=bURL::getURLParams();
        if(empty($this->params['module'])){
            bDebug::debugError("No module set");
            return false;
        }        
        if(empty($this->params['action'])){
            bDebug::debugError("No action set");
            return false;
        } 
        $GLOBALS['urlParams']=$this->params;
        
        return $this->moduleActionExecute($this->params['module'], $this->params['action'], $this->params);
    }
        
    /**
     * Execute module action
     */
    public function moduleActionExecute($module, $action, $params=array()){
        bDebug::debug('Start execute Module: <b>'.$module.'</b>; Action: <b>'. $action.'</b>');
        $this->module=$module;
        $this->action=$action;
        
        $actionCall = 'action'.ucfirst($action);
        $controller = $this->getControllerClass($module, $actionCall, $action);

        //If have params for page, clear previos params of controller to use new
        if(!empty($params)){
            $this->params=array();
        }
        //Set default params
        $params['module']=$this->module;
        $params['action']=$this->action;
        
        $this->params+=$params;
        $controller->params=$this->params;
        
        bDebug::debug('Start get content Module: <b>'.$this->module.'</b>; Action: <b>'. $this->action.'</b>');
        $content = $controller->$actionCall($this->params);
        bDebug::debug('End get content Module: <b>'.$this->module.'</b>; Action: <b>'. $this->action.'</b>');
          
        /*Render layout*/
        if($controller->renderLayout){
            $data['content']=$content;
            $content = $this->renderLayout($controller->renderLayout, $data, true);
            $this->setControllerParametrsFromThis($controller);            
            $data = $controller->getLayoutParams();
            
            $data['content']=$content;
            $content = $this->renderLayout($controller->renderHTMLLayout, $data);
        }else{
            $this->setControllerParametrsToThis($controller);
        }
        bDebug::debug('End execute Module: <b>'.$this->module.'</b>; Action: <b>'. $actionCall.'</b>');
        return $content;
    }
    
    private function getControllerClass($moduleName, $actionCall, $action){
        $moduleClass = $moduleName.'Controller';
                
        /*Check for class exist*/
        if(!bCore::checkClassLocation($moduleClass)){   
            bUrl::redirect404();
        }
        $controller = new $moduleClass($action);
        
        /*Check for method exist in the class*/      
        if(!method_exists($controller, $actionCall)){
           bUrl::redirect404();
        }
        
        $config = bConfiguration::getConfiguration();
        $configParams = $config['params'];
        
        $controller->theme=$this->theme;
        if(empty($controller->renderLayout)){
            $controller->renderLayout=!empty($configParams['layout'])?$configParams['layout']:false;
        }
        if(empty($controller->renderHTMLLayout)){
            $controller->renderHTMLLayout=!empty($configParams['layoutHTML'])?$configParams['layoutHTML']:false;
        }        
        $controller->moduleLocation = bSystem::getModuleLocation($moduleName);
        return $controller;
    }
        
    /**
     * Check for template existing for this module
     * @param string $template
     * @return bool
     */
    public function checkTemplateExisting($template){
        if($this->getTemplateLocation($template)){
           return true; 
        }
        return false;
    }
        
    /**
     * Return layout location
     */
    public function getLayoutLocation($layout){
        $location =  bTheme::getThemesPath() . $this->theme. '/layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        $location =  bCore::getGlobalsPath() . 'layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        return false;
    }
    
    /**
     * Return template location
     */
    public function getTemplateLocation($template){
        $location = $this->moduleLocation . 'templates/'.$template.'.php';        
        if(file_exists($location)) {
            return $location;               
        }        
        return false;
    }
    
    /**
     * Render template of current module
     */
    public function render($template, $params=array()){
        if(empty($this->moduleLocation)){
            bDebug::debug("Can't find module path on location: ".$this->moduleLocation);            
            return false;
        }
        if(!$location = $this->getTemplateLocation($template)){            
            bDebug::debug("Can't find template - ".$template.' On location: '.$location);
            return false;
        }        
        return $this->renderInternal($location,$params);
    }
        
    /**
     * Render setted layout
     */
    public function renderLayout($layout, $params=array(), $return = false){
        if(!$location = $this->getLayoutLocation($layout)){
            bDebug::debug("Can't find layout - ".$layout);
            return false;
        }
        return $this->renderInternal($location, $params, $return);  
    }
    
    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     * @param string $_viewFile_ view file
     * @param array $_data_ data to be extracted and made available to the view file
     * @param boolean $_return_ whether the rendering result should be returned as a string
     * @return string the rendering result. Null if the rendering result is not required.
     */
    public function renderInternal($_viewFile_,$_data_=null,$_return_=true){
        // we use special variable names here to avoid conflict when extracting data
        $data = null;
        if(is_array($_data_)){
            extract($_data_,EXTR_PREFIX_SAME,'data');
        }else{
            $data=$_data_;
        }
        if($_return_){
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        }else{
            require($_viewFile_);
        }
    }
    
    /**
     * Set default params for the layout
     */
    private function setLayoutParams(){ 
        
        $this->setCSSFiles();
        $this->setJSFiles();
        $this->setJavaScripts();   
        
        $config = bConfiguration::getConfiguration();
        $configParams = $config['params'];
        
        if(empty($this->pageTitle) && !empty($configParams['title'])){
            $this->pageTitle=$configParams['title'];
        }
        if(empty($this->pageDescription) && !empty($configParams['description'])){
            $this->pageDescription=$configParams['description'];
        }
        if(empty($this->pageKeywords) && !empty($configParams['keywords'])){
            $this->pageKeywords=$configParams['keywords'];        
        }
    }
    
    private function setCSSFiles(){
        $config = bConfiguration::getConfiguration();
        $configParams = $config['params'];
        
        if(!empty($configParams['css'])){
            foreach ($configParams['css'] as $fileName => $version) {
                $this->addCSSFile($fileName, $version, '0');
            }
        }
        ksort($this->cssFilesOrder);        
        foreach ($this->cssFilesOrder as $files) {
            if(empty($files)){
                continue;
            }
            foreach ($files as $file) {
                $this->cssFiles[]='
<link type="text/css" rel="stylesheet" href="'.$file.'" media="all" />';
            }
        }
        if(empty($this->cssFiles)){
            $this->cssFiles=''; return;
        }
        $this->cssFiles = join(' ', $this->cssFiles);
    }

    private function setJSFiles(){
        $config = bConfiguration::getConfiguration();
        $configParams = $config['params'];
        
        foreach ($configParams['js'] as $fileName => $version) {
            $this->addJSFile($fileName, $version, '1');
        }             
        ksort($this->jsFilesOrder);
        foreach ($this->jsFilesOrder as $weight => $files) {
            if(empty($files)) continue;
            foreach ($files as $key => $file) {
                $this->jsFiles[]='
<script type="text/javascript" src="'.$file.'" ></script>';
            }
        }
        if(empty($this->jsFiles)){
            $this->jsFiles=''; return;
        }
        $this->jsFiles = join(' ', $this->jsFiles);
    }

    private function setJavaScripts(){
        if(!empty($this->jsScriptsOrder)){
            ksort($this->jsScriptsOrder);   
            foreach ($this->jsScriptsOrder as $scripts) {
                if(empty($scripts)){
                    continue;
                }
                foreach ($scripts as $script) {
                    $this->jsScripts[]=$script;
                }
            }
        }
        if(empty($this->jsScripts)){
            $this->jsScripts=''; return;
        }        
        $this->jsScripts ='<script type="text/javascript">'.join(' ', $this->jsScripts).'</script>';
    }
    
    /**
     * Set to module parametrs (Like CSS files and JS files) this class paramters
     */
    private function setControllerParametrsFromThis($controller){
        $items = array('cssFilesOrder', 'jsFilesOrder', 'jsScriptsOrder');
        foreach ($items as $key) {
            $itemValues = $controller->$key;
            foreach ($this->$key as $weight => $array) {
                if(empty($array)){
                    continue;
                }
                foreach ($array as $value) {
                    if(empty($itemValues[$weight][$value])){
                        $itemValues[$weight][$value]=$value;
                    }
                }                
            }
            $controller->$key=$itemValues;
        }
    }

    /**
     * Set from module parametrs (Like CSS files and JS files) to this class paramters
     */
    private function setControllerParametrsToThis($controller){
        $items = array('cssFilesOrder', 'jsFilesOrder', 'jsScriptsOrder');
        foreach ($items as $key) {
            $itemValues = $this->$key;
            foreach ($controller->$key as $weight => $array) {
                if(empty($array)){
                    continue;
                }
                foreach ($array as $value) {
                    $haveSame=false;                    
                    if(empty($itemValues[$weight][$value])){
                        $itemValues[$weight][$value]=$value;
                    }
                }   
            }     
            $this->$key=$itemValues;
        }
    }
    /**
     * Set default params for the layout
     */
    public function getLayoutParams(){
        $this->setLayoutParams();        
        $data = array(
            'cssFiles' => $this->cssFiles,
            'jsFiles' => $this->jsFiles,
            'jsScripts' => $this->jsScripts,
            'pageTitle' => $this->pageTitle,
            'pageDescription' => $this->pageDescription,
            'pageKeywords' => $this->pageKeywords,
        );
        return $data;
    }
    
    
    /**
     * Add CSS file
     * $param String $fileName CSS file name
     * $param String $version CSS file version
     * $param Int $weight Order Nr of CSS file in CSS file list
     * $param Bool $external If file is external (http://othersite.com/css.css), the use true
     * $param String $path CSS file path, if empty start search from module path, than project, than global     
     */
    public function addCSSFile($fileName, $version='', $weight='99', $external=false, $path=''){
        if(!$external){
            if(!empty($path) && !file_exists($path.$fileName)){
                bDebug::debug("Can't find CSS - ".$fileName.' On location: '.$path);
                return false;
            }        
            $path = $this->getFileLocation($fileName, 'css');
            if(empty($path)){
                return false;
            }
            $path = $this->getDomainCSS().str_replace(bCore::getBasePath(), '', $path);            
        }else{
            $path='';
        }
        if(!empty($version)){
            $fileName.='?version='.$version;
        }
        
        /*Check for same*/
        if(!empty($this->cssFilesOrder[$weight])){
            foreach ($this->cssFilesOrder[$weight] as $key => $rec) {
                if($rec==$path.$fileName){
                    return false;
                }
            }
        }
        $this->cssFilesOrder[$weight][]=$path.$fileName;
        return true;
    }
    
    /**
     * Add JS file
     * $param String $fileName JS file name
     * $param String $version JS file version
     * $param Int $weight Order Nr of JS file in JS file list
     * $param Bool $external If file is external (http://othersite.com/js.js), the use true
     * $param String $path JS file path, if empty start search from module path, than project, than global     
     */
    public function addJSFile($fileName, $version='', $weight='99', $external=false, $path=''){
        if(!$external){
            if(!empty($path) && !file_exists($path.$fileName)){
                bDebug::debug("Can't find JS - ".$fileName.' On location: '.$path);
                return false;
            }        
            $path = $this->getFileLocation($fileName, 'js');
            if(empty($path)){
                return false;
            }
            $path = $this->getDomainJS().str_replace(bCore::getBasePath(), '', $path);
        }else{
            $path='';
        }
        if(!empty($version)){
            $fileName.='?version='.$version;        
        }
        
        /*Check for same*/        
        if(!empty($this->jsFilesOrder[$weight])){
            foreach ($this->jsFilesOrder[$weight] as $key => $rec) {                
                if($rec==$path.$fileName){
                    return false;
                }
            }
        }
        $this->jsFilesOrder[$weight][]=$path.$fileName;
        return true;
    }
    
    /**
     * Add Java Script
     * $param String $script Java Script     
     * $param Int $weight Order Nr of JS file in JS file list     
     */
    public function addJSScripts($script, $weight='99'){     
        /*Check for same*/
        if(!empty($this->jsScriptsOrder[$weight])){
            foreach ($this->jsScriptsOrder[$weight] as $key => $rec) {
                if($rec==$script){
                    return false;
                }
            }
        }
        $this->jsScriptsOrder[$weight][]=$script;
        return true;
    }
    
    /**
     * Check and get path location of file
     */
    public function getFileLocation($fileName, $type){
        $location =  $this->moduleLocation . $type.'/';
        
        if(file_exists($location.$fileName)) {
            return $location;
        }
        $location =  bTheme::getThemesPath() . $this->theme. '/'.$type.'/' ;
        if(file_exists($location. $fileName)) {
            return $location;               
        }
        $location =  bCore::getGlobalsPath() . $type.'/' ;
        if(file_exists($location.$fileName)) {
            return $location;               
        }
        $location =  bCore::getCorePath() . $type.'/' ;
        if(file_exists($location.$fileName)) {
            return $location;               
        }
        bDebug::debug("Can't find ".$type." file - ".$fileName);
        return false;
    }
    
    public function getDomainCSS(){
        return $this->getDomainForType('css');
    }
    
    public function getDomainJS(){
        return $this->getDomainForType('js');
    }
    
    private function getDomainForType($type){
        if(!empty($this->config->params['domains'][$type])){
            return $this->config->params['domains'][$type];
        }else{
             return $this->config->params['domains'][$type] = bUrl::getBaseUrl().'/';
        }
        return '/';
    }
}