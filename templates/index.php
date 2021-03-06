<!DOCTYPE html>
<html lang="de">
    
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <?php use PLAIN_PHP\Controller; ?>
        <title><?php echo Controller::getTitle(); ?></title>

		<link type="text/css" rel="stylesheet" href="<?php echo Controller::PATH() ?>/public/css/bootstrap.min.css" />
		<?php Controller::injectStylesheets() ?>
		
        <?php Controller::_JSPATH(); ?>
        <script src="<?php echo Controller::PATH() ?>/public/js/jquery-2.0.3.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo Controller::PATH() ?>/public/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo Controller::PATH() ?>/public/js/ajaxCall.js" type="text/javascript" charset="utf-8"></script>
        <?php Controller::injectScripts() ?>
        
	</head>
	
	<body >
		
		<div class="container">
		    
	        <?php
	        $template->_yield();
	         ?>
	         
		</div>

	</body>

</html>
