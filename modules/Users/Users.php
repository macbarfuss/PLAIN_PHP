<?php 

/**
 * 
 */
class Users extends Module {
	
    
    public static function init(){
        parent::init();
        
        if(!class_exists("R")){
            throw new Exception("The Users-Module need a configured database!", 1);
        }
    }
    
    public static function checkSession(){
        $session = R::findOne(self::$config["sessionTable"], "val = ?", array(session_id()));
        if($session != null){
            return $session->user;
        }
        self::redirectFromString(self::$config["redirectAfter_failure"]);
    }
    
    private static function createSession($user){
        $session = R::dispense(self::$config["sessionTable"]);
        $session->user = $user;
        $session->val = session_id();
        R::store($session);
    }
    
	public static function create(){
	    //check input
		if($_POST["username"] == "" || $_POST["password1"] == "" || $_POST["password2"] == ""){
        	self::keep("error", "Please fill in all fields");
            self::keep("username", $_POST["username"]);
            self::redirectFromString(self::$config["register"]);
        }else if( $_POST["password1"] != $_POST["password2"] ){
        	self::keep("error", "Your passwords did not match");
            self::keep("username", $_POST["username"]);
            self::redirectFromString(self::$config["register"]);
        }
		
		//check if username is free
		$user = R::findOne(self::$config["userTable"], "username = ?", array($_POST["username"]));
        if($user != null){
            self::keep("error", "This username is taken, please choose another one.");
            self::redirectFromString(self::$config["register"]);
        }
        
        //everything checked, lets create
        $user = R::dispense(self::$config["userTable"]);
        $user->username = $_POST["username"];
        $user->setPassword($_POST["password1"]);
        
        R::store($user);
        
        
        self::createSession($user);
        self::redirectFromString(self::$config["redirectAfter_success"]);
	}
    
    public static function auth(){
        
        if($_POST["username"] == "" || $_POST["password"] == ""){
        	self::keep("error", "Please fill in all fields");
            self::redirectFromString(self::$config["redirectAfter_failure"]);
        }
        
        $user = R::findOne(self::$config["userTable"], "username = ?", array($_POST["username"]));
        if($user == null){
            if(self::$config["registerAfterLoginFail"]){
                self::keep("username", $_POST["username"]);
				self::redirectFromString(self::$config["register"]);
            }
            
        }else if($user->checkPassword($_POST["password"])){
            //login redirect
            self::createSession($user);
            self::redirectFromString(self::$config["redirectAfter_success"]);
        }
        
        //error
        self::keep("error", "Login failed");
        self::redirectFromString(self::$config["redirectAfter_failure"]);
    }
    
    public static function logout(){
        $session = R::findOne(self::$config["sessionTable"], "val = ?", array(session_id()));
        if($session != null){
            R::trash($session);
        }
        
        session_destroy();
        self::redirectFromString(self::$config["redirectAfter_logout"]);
    }
    
    public static function login(){
        self::render();
    }
    
    public static function register(){
        self::render();
    }
    
    public static function loginForm(){
        self::renderPartial(array(
            "action" => Users::linkTo("auth")
        ));
    }
    
    public static function registerForm(){
        self::renderPartial(array(
            "error" => self::get("error"),
            "action" => self::linkTo("create"),
            "username" => self::get("username")
        ));
    }
}



 ?>