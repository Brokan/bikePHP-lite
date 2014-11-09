<?php
/**
 * bikePHP 0.x framework system file
 * Foo controller file with default actions
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class fooController extends bController{
        
    /**
     * Front page action
     * @param Array $params Params of page
     * @return String content of page
     */
    public function actionFront($params){        
        $data = $params;
        return $this->render('front', $data);
    }
    
    /**
     * Header block
     * @param Array $params Params of block
     * @return String content of block
     */
    public function actionHeader($params){
        return $this->render('header');        
    }
    
    /**
     * Footer block action
     * @param Array $params Params of block
     * @return String content of block
     */
    public function actionFooter($params){
        return $this->render('footer');        
    }
    
    /**
     * 404 page action
     * @param Array $params Params of page
     * @return String content of page
     */
    public function action404($params){
        return $this->render('404', array());
    }
    
    public function actionBar($params){
        return $this->render('bar', array());
    }
}