<?php
/**
 * The MIT License (MIT)
 *
 *  Copyright (c) <2013> <Christian Dreier (dreier.christian@gmail.com) - drailing.net>
 *    
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 * 
 */ 

namespace PLAIN_PHP;
 
class Controller {
    
    protected static $scripts = array();
    protected static $stylesheets = array();
    protected static $renderArgs = array();
	
	protected static $renderContent = array();
	
	protected static $isModule = false;
    
    public static $alwaysInvoked = array();
	
    private static $template = false;
    
    /**
     * add a javascript file to your current render call
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#addScript
     * @param string $filename  
     * @param string $path      default path is lib/js/ , overwrite to load from diffrent location
     * 
     */
    public static function addScript($filename, $path = "public/js/"){
		$fullpath = self::PATH() . $path . $filename;
		if (! in_array($fullpath, self::$scripts)) {
			self::$scripts[] = $fullpath;
    	}
	}
    
    /**
     * add a script from external url
     * 
     * @param String $url
     */
    public static function addExternalScript($url){
		if (! in_array($url, self::$scripts)) {
        	self::$scripts[] = $url;
    	}
	}
	
    /**
     * add key - value based content to you render call, see setTitle for conrete usage
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#renderContent
     * @param string $key   
     * @param string $value
     */
	public static function addRenderContent($key, $value){
        self::$renderContent[$key] = $value;
    }
    
    /**
     * get the render content for given $key, after set with addRenderContent($key, $value)
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#renderContent
     * @param string $key
     */
    public static function getRenderContent($key){
        return ( isset(self::$renderContent[$key]) ) ? self::$renderContent[$key] : "";
    }
    
    /**
     * set the title in your <title> - tag in the index file
     * this is a shortcut for addRenderContent('title', $title)
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#setTitleTitle
     * @param string $title 
     */
    public static function setTitle($title){
        self::$renderContent["title"] = $title;
    }
    
    /**
     * get the title, previously set with setTitle
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#setTitleTitle
     */
    public static function getTitle(){
        return self::getRenderContent("title");
    }
    
    /**
     * add a css file to your current render call
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#addStylesheet
     * @param string $filename  
     * @param string $path      default path is lib/css/ , overwrite to load from diffrent location
     * 
     */
    public static function addStylesheet($filename, $path = "public/css/"){
		$fullpath = self::PATH() . $path . $filename;
		if (! in_array($fullpath, self::$stylesheets)) {
			self::$stylesheets[] = $fullpath;
		}
    }
    
    public static function injectScripts(){
        foreach (self::$scripts as $script) {
            ?>
            <script src="<?php echo $script ?>" type="text/javascript" charset="utf-8"></script>
            <?php 
        }
    }
    public static function injectStylesheets(){
        foreach (self::$stylesheets as $stylesheet) {
            ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $stylesheet ?>" />
            <?php 
        }
    }
    
    /**
     * checks if your calling controller is aktive in the current render call
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#isActive
     * @param string $view          optional, if set, checks also if specific view is called
     * @param array $routeParams    optional, if set, checks if specific view with custom route is called
     * 
     * @return boolean true if your conditions are all fulfilled
     */
    public static function isActive($view = false, $routeParams = false){
    	
        $callee = $checkAgainst = get_called_class();
        //build default route
        $params = false;
        if($routeParams){
            if(!is_array($routeParams)){
                $params = array_slice(func_get_args(), 1);
            }else{
                $params = array($routeParams);
            }
        }

        if($view){
            $checkAgainst .= "/".$view;
            $callee .= "::".$view;
        }
        
        //first check if path info is set and matches 
        if(isset($_SERVER["PATH_INFO"])){
            //check default route
            $defaultCheck = $checkAgainst;
            if($params){
                foreach($params as $p){
                    $defaultCheck .= "/".$p;
                }
            }

        	if(strstr($_SERVER["PATH_INFO"], $defaultCheck))
            	return true;

            //check against custom routing
			if (Routing::isActive($_SERVER["PATH_INFO"], $callee, $params))
                return true;
        }
		
        //check render view only if no route params are set
        if(isset(self::$renderArgs["view"]) && !$routeParams){
        	$renderArgView = self::$renderArgs["view"];
			if(self::$isModule){
				$renderArgView = str_replace("modules/", "", $renderArgView);
				$renderArgView = str_replace("/views/", "/", $renderArgView);
			}
            return strstr($renderArgView, $checkAgainst);
        }
		
        //perhaps i forgot something, but should return false now
        return false;
    }
    
    
    /**
     * to implement from subclasses, if possible called before the execute() function, but at the latest before every render() call
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#always
     * 
     */
    protected static function always(){
    }
	
    /**
     * renders the corresponding view to your controller function
     * e.g. MyController::showMe renders /views/MyController/showMe.php
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#render
     * @param array $args   associative array with data for your view, the keys are the name of the variable in your view
     */
    public static function render($args = array()){
        $trace = debug_backtrace();
        $trace = $trace[1];
        $view = "views/" . $trace["class"] ."/" . $trace["function"] . ".php";
        
        //not invoked in execute, do it now
        if(!in_array($trace["class"], self::$alwaysInvoked)){
            call_user_func($trace["class"]."::"."always");
			self::$alwaysInvoked[] = $trace["class"];
        }
        
        self::_render($view, $args);
    }
	
	/**
	 * extracted to reduce redudancy
	 */
	protected static function _render($view, $args){
		if(is_file( $view )){
            self::$renderArgs["args"] = $args;
            self::$renderArgs["view"] = $view;
			
			//check if template is set
			if(self::$template !== false){
				Template::extend(self::$template);
			}
			
			extract(self::$renderArgs["args"]);
        	include(self::$renderArgs["view"]);
			Template::_finish();
			
        }else{
        	throw new Exceptions\ViewNotFoundException($view);
        }
	}
	
	/**
	 * to call in the always function, tells the framework to extend every view from given template
	 * only affects render(), partials needs to extend explicitly
	 * 
	 * @param string $templateName	the template name to extend from
	 */
	public static function extendFromTemplate($templateName){
		self::$template = $templateName;
	}
	
    /**
     * prints the text with the correct Content-Type header (text / plain) 
     * scipt processing is canceled after renderText
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#controller_renderText
     * @param string $txt
     */
	public static function renderText($txt){
		header("Content-Type: text/plain");
		exit($txt);
	}
	
    /**
     * prints the json_encoded data with the correct Content-Type header (application / json) 
     * scipt processing is canceled after renderJSON
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#controller_renderJson
     * @param array $data
     */
	public static function renderJSON($data){
		header("Content-Type: application/json");
        if(PLAIN_PHP_DEV){
            exit(json_encode($data, JSON_PRETTY_PRINT));
        }
		exit(json_encode($data));
	}

    /**
     * renders a binary file and exits script execution
     *
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#controller_renderBinary
     * @param string $path  the full path to the file you want to render
     * @param string $name  optional param, if not set renderBinary will output the binary, if set, renderBinary adds attachment header and starts a download
     * @param string $mime  optional param, if not set renderBinary tries to find the mime-type with help of finfo
     */
    public static function renderBinary($path, $name = false, $mime = false){

        if(!is_file($path)){
            throw new Exceptions\Exception("No file found at: ".$path);
        }

        //no explizit mime type is set, try to find with finfo
        if(!$mime){
            $mimeInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($mimeInfo, $path);
        }
        header('Content-type: '.$mime);

        if($name !== false){
            $info = new \SplFileInfo($path);

            //no extension in path, try to set with mime type
            if($info->getExtension() == ""){
                list($garbage, $ext) = explode("/", $mime);
                $name .= ".".$ext;
            }

            //name is found and not empty
            if($name && $name != ""){
                header("Content-Disposition: attachment; filename=$name");
            }
        }

        exit(readfile($path));
    }
    
    /**
     * includes the view at the place the function is called
     * the view is found by the known naming convention
     * e.g. MyController::showMe includes /views/MyController/showMe.php
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/views#renderPartial
     * @param array $args   optional, associative array with data for your view, the keys are the name of the variable in your view
     * @param boolean $ajax optional, default = false, set to true if you want to ouput the view via AJAX, if so, script processing is canceled 
     * 
     */
    public static function renderPartial($args = array(), $ajax = false){
        $trace = debug_backtrace();
        $trace = $trace[1];
        $view = "views/" . $trace["class"] ."/" . $trace["function"] . ".php";
        
        //check if always function is called
        if(!in_array($trace["class"], self::$alwaysInvoked)){
            call_user_func($trace["class"]."::"."always");
            self::$alwaysInvoked[] = $trace["class"];
        }
        
        if(is_file( $view )){
            extract($args);
            include($view);
			
			if($ajax)exit();
        }else{
            throw new Exceptions\ViewNotFoundException($view);
        }
    }
    
    /**
     * only called once, at the top of the root index.php file, to start the framework and route processing
     */
    public static function execute( $pathInfo ){
        $controllerInfo = Routing::parsePathInfo($pathInfo);
        if(count($controllerInfo) == 2 && is_callable($controllerInfo[0])){

        	if(!is_array($controllerInfo[1])){
        		$controllerInfo[1] = array($controllerInfo[1]);
        	}
            
            //just to be sure
            list($class, $unused) = explode("::", $controllerInfo[0]);
            if(!in_array($class, self::$alwaysInvoked)){
                call_user_func($class."::"."always");
                self::$alwaysInvoked[] = $class;
            }
            
			if( isset($_POST["PLAIN_PHP_AJAX"]) && isset($_POST["args"]) ){
                if(is_array($_POST["args"])){
                    $controllerInfo[1][] = $_POST["args"];
                }else{
                    $controllerInfo[1][] = array($_POST["args"]);
                }
            }
            
            call_user_func_array($controllerInfo[0], $controllerInfo[1]);
            
        }else{
        	throw new Exceptions\ActionNotFoundException($pathInfo);
        }
    }
    
    
    /**
     * starts a json store in the session
     * this value is kept as long as you get it with Controller::get 
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#keepGet
     * @param string $key
     * @param string $value
     */
    public static function keep($key, $value){
        if(!isset($_SESSION["keep"])){
            $_SESSION["keep"] = json_encode(array($key => $value));
        }else{
            $data = json_decode($_SESSION["keep"], true);
            $data[$key] = $value;
            $_SESSION["keep"] = json_encode($data);
        }
    }
    
    /**
     * return the value for given key, stored with Controller::keep 
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#keepGet
     * @param string $key
     */
    public static function get($key){
        if(!isset($_SESSION["keep"])){
            return null;
        }
        
        $data = json_decode($_SESSION["keep"], true);
        if(!isset($data[$key])){
            return null;
        }
        
        $return = $data[$key];
        unset($data[$key]);
        $_SESSION["keep"] = json_encode($data);
        return $return;
    }
    
	private static function buildRoute($functionName, $param){
		
        $route = Routing::checkFunction(get_called_class()."::". $functionName);
		if(!$route){
		    //no rout specified
	        $route = "/".get_called_class()."/$functionName";
            //if there are params without specific route, just append 
            if(is_array($param)){
                foreach ($param as $p) {
                    $route .= "/".$p;
                }
            }
		}else{
			try{
				//params only use with custom route
				$route = Routing::fillParams($route, $param);
			}catch(PLAIN_PHP\Exceptions\Exception $e){
				//error in routing, missmatch from expected and total params 
				echo $e;
			}
		}
		
		return $route;
	}
	
    /**
     * generates absolute framework link to controller function
     * 
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#linkTo
     * @param string $functionName  default = "index"
     * @param mixed $param          optional, if there is a custom route, this can be an array or a list of parameters (see documentation)
     * 
     * @return string $link         the generated, absolute link to the requested route
     */
    public static function linkTo($functionName = "index", $param = false){
    	if($param !== false){
			if(!is_array($param)){
	    		$param = array_slice(func_get_args(), 1);
	    	}
    	}
		
    	$route = self::buildRoute($functionName, $param);
        return self::PATH()."index.php".$route;
    }
	
    /**
     * starts a header redirect to the requested controller function and cancels the script processing  
     *  
     * @link http://plain-php.drailing.net/index.php/Manual/controllers#redirTo
     * @param string $functionName  default = "index"
     * @param mixed $param          optional, if there is a custom route, this can be an array or a list of parameters (see documentation)
    
     */
    public static function redirectTo($functionName = "index", $param = false){
    	if($param !== false){
			if(!is_array($param)){
	    		$param = array_slice(func_get_args(), 1);
	    	}
    	}
		
    	$route = self::buildRoute($functionName, $param);
        header( "Location: " . self::PATH() . "index.php" . $route ) ;
        exit();
    }
    
    public static function PATH() {
        return PLAIN_PHP_ROOT;
    }
    
    public static function _JSPATH(){
        ?>
        <script>
            function _getServerUrl(){
                return "<?php echo self::PATH(); ?>";
            }
        </script>
        <?php 
    }
    
}

 ?>