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
    
    public $params = array();
    public $renderLayout=true;
        
    function __construct(){ 
        
    }
        
    /**
     * Run project page
     */
    public function run(){
        bDebug::debug('Start RUN page');
        $params = bURL::getURLParams();
        if(empty($params['module'])){
            bDebug::debugError("No module set");
            return false;
        }        
        if(empty($params['action'])){
            bDebug::debugError("No action set");
            return false;
        } 
                 
        return $this->moduleActionExecute($params['module'], $params['action'], $params);
    }
    
    /**
     * Get module action rendered content
     * @param String $module
     * @param String $action
     * @param Array $params parameters of module/action
     * @return String content of module action
     */
    public static function getModuleAction($module, $action, $params=array()){
        $controller = new bController();
        $controller->renderLayout = false;
        return $controller->moduleActionExecute($module, $action, $params);
    }
    
    /**
     * Execute module action
     * @param String $module
     * @param String $action
     * @param Array $params
     * @return String content of controller or page
     */
    private function moduleActionExecute($module, $action, $params=array()){        
        bDebug::debug('Start execute Module: <b>'.$module.'</b>; Action: <b>'. $action.'</b>');

        $params['module'] = $module;
        $params['action'] = $action;
        
        $actionCall = 'action'.ucfirst($action);
        $controller = $this->getControllerClass($module, $actionCall, $action);
        
        //Set parameters to controller
        $controller->params=$params;
        
        bDebug::debug('Start get content Module: <b>'.$module.'</b>; Action: <b>'. $action.'</b>');
        $content = $controller->$actionCall($this->params);
        bDebug::debug('End get content Module: <b>'.$module.'</b>; Action: <b>'. $action.'</b>');
          
        /*Render layout*/
        if($controller->renderLayout){
            $content = bRender::renderLayout(array('content'=>$content));
        }
        bDebug::debug('End execute Module: <b>'.$module.'</b>; Action: <b>'. $actionCall.'</b>');
        return $content;
    }
    
    /**
     * Get class of controller
     * @param String $moduleName
     * @param String $actionCall
     * @param String $action
     * @return Object Controller class
     */
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
        //Set if call sub additional module/action
        $controller->renderLayout = $this->renderLayout;
        return $controller;
    }
    
    /**
     * Render template of current module
     * @param String $template name of template
     * @param Array $params parameters for template
     * @return string/boolean
     */
    public function render($template, $params=array()){
        $moduleLocation = bSystem::getModuleLocation($this->params['module']);
        if(empty($moduleLocation)){
            bDebug::debug("Can't find module path on location: ".$this->moduleLocation);            
            return false;
        }
        if(!$location = $this->getTemplateLocation($moduleLocation, $template)){            
            bDebug::debug("Can't find template - ".$template.' On location: '.$location);
            return false;
        }
        return bRender::renderInternal($location, $params);
    }
    
    /**
     * Return template location
     * @param String $moduleLocation
     * @param String $template
     * @return boolean|string Path to template
     */
    public function getTemplateLocation($moduleLocation, $template){
        $location = $moduleLocation . 'templates/' . $template . '.php';        
        if(file_exists($location)) {
            return $location;               
        }
        //Check for file in theme
        $location =  bTheme::getThemesPath() . bTheme::getTheme() . '/templates/' . $template.'.php';
        if(file_exists($location)) {
            return $location;               
        }
        //Check for file in globals
        $location =  bCore::getGlobalsPath() . '/templates/' . $template.'.php';
        if(file_exists($location)) {
            return $location;               
        }
        return false;
    }
}