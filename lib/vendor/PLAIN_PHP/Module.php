<?php 

/**
 * 
 */
class Module extends Controller{
    
    
    public static function render($args = array()){
        $trace = debug_backtrace();
        $trace = $trace[1];
        
        $view = "modules/" . $trace["class"] ."/views/" . $trace["function"] . ".php";
        
        //not invoked in execute, do it now
        if(!self::$alwaysInvoked){
            call_user_func($trace["class"]."::"."always");
            self::$alwaysInvoked = true;
        }
        
        if(is_file( $view )){
            self::$renderArgs["args"] = $args;
            self::$renderArgs["view"] = $view;
            self::$shouldRender = true;
        }else{
            throw new Exception("VIEW NOT FOUND - " . $view);
        }
    }
    
    public static function renderPartial($args = array(), $ajax = false){
        $trace = debug_backtrace();
        $trace = $trace[1];
        $view = "modules/" . $trace["class"] ."/views/" . $trace["function"] . ".php";
        if(is_file( $view )){
            extract($args);
            include($view);
            if($ajax)exit();
        }else{
            throw new Exception("VIEW NOT FOUND - " . $view);
        }
    }
    
    
}


 ?>