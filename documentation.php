Framework documentation

1. MVC - structure of framework
    1.1. Core
    
    Core of project is in /core/ map. There is classes with functions for stabile core run.
    
    1.2. Modules
    
    Developers can add and create his modules in map /globals/modules/. 
    Modules have controller. Those controller have actions, it's can be called as page.
    
    1.3. Templates
    
    Every action can render some template of module, theme template, if module don't have this template, or global templates if theme don't have template too.
    For example in action can use code
    <?php $this->render('foo'); ?>
    
    1.4. Themes
    
    Developers can use different themes for different actions. It's all can be set in config or in some module action.
    In action can set theme using code
    <?php $this->theme = 'default'; ?>
    
        1.4.1. Layouts
        
        On configuration file or in action can set layout that action need to use. Or will be using default layout that is set on configuration file.
        There is two types of layouts.
            - General with header and body
            - With content of body
        Both layouts can change or unset in action with code    
        To unset
        <?php $this->renderLayout=false; //Content layout ?>
        <?php $this->renderHTMLLayout=false; //Header and body layout ?>
        
        To set different layouts
        <?php $this->renderLayout='otherLayout.php'; ?>
        <?php $this->renderHTMLLayout='otherHTMLLayout.php'; ?>
        
        At first action try to use theme layout, if don't find theme layout, use global layout.
        
        1.4.2. JavaScript files
        
        In configuration file can set JavaScript files. At first is checking for JS file in theme, than in global /js/ folder.
        For some action can set additional JavaScript file or JavaScript code.
        
        Add JavaScript file in some action        
        <?php $this->addJSFile('bar.js'); ?>
        For file check at first module of action /js/ folder, if not found, than theme, and than global /js/ folder.
        If script is in some subfolder, can set file like
        <?php $this->addJSFile('foo/bar.js'); ?>
        
        To add simple JavaScript, need to use code
        <?php $this->addJSScripts('var foo="bar";');  ?>
        
        1.4.3. CSS files
        
        Simple like with JavaScript, CSS file can set in configuration file, and in module action.
        To set file in module action need use code
        <?php $this->addCSSFile('bar.css'); ?>
        Or if CSS in some other folder, for example in JS folder with some plugin
        <?php $this->addCSSFile('../js/foo/bar.css'); ?>
        
    1.5. Configuration file
    
    There is global configuration file in /globals/config.php
    And theme configuration file in /themes/{theme_name}/config.php
    
    At first system load global configuration file, than load theme configuration file and override or add theme configuration on global configuration
    
    1.6. Global functions files
    
    There is file for simple PHP function at /globals/global_functions.php
    In this file developers can add some functions that need to be used in different places
    For example to easer set some URL is using function 
    <?php setURL('foo/bar', array()); ?>
    
2. Module structure
    2.1. Structure
    2.2. New action/page
    2.3. Templates
    2.4. Library
    2.5. Call action as block
3. How to create new module
4. Database
    4.1. Connection to database
    4.2. Get data from database - SQL query
        4.1.1. Get all rows
        4.1.2. Get single row
        4.1.3. Get one field
    4.3. Insert and Update data
    4.4. Delete data
    4.5. Variables
5. Create URL to module action
6. Get project configuration
7. How set other theme
8. Get parameters from page requested
9. Global variables/constants
10. Work with debug
