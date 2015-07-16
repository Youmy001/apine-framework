<!DOCTYPE html>
<html lang="<?php print session()->get_session_language_id();?>">
	<head prefix="og: http://ogp.me/ns# article: http://ogp.me/ns/article# profile: http://ogp.me/ns/profile#">
		<title><?php print $this->_title?></title>
		<meta charset="utf-8" />
		<meta name="robots" content="nofollow">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="vocaloid, video, hatsune miku">
		<meta name="description" content="<?php print APP_DESCRIPTION?>">
		<?php
			if((preg_match('/(\/player)/',URL_CURRENT)==1||preg_match('/(\/video)/',URL_CURRENT)==1)&&preg_match('/(\/control)/',URL_CURRENT)==0){
				if(isset($video)){
					include(SCRIPT_PATH.'/views/ograph_video.php');
				}else{
					include(SCRIPT_PATH.'/views/ograph.php');
				}
			}else if((preg_match('/(\/blog)/',URL_CURRENT)==1)&&isset($article)){
				include(SCRIPT_PATH.'/views/ograph_article.php');
			}else if(preg_match('/(control)/',URL_CURRENT)==0){
				include(SCRIPT_PATH.'/views/ograph.php');
            		}
            	?>
		<link rel="icon" type="image/png" href="<?php echo session()->path('resources/public/image/logo/bokaro_flat_logo_20140624_16x16.png',false);?>"/>
		<link rel="shortcut icon" href="<?php echo session()->path('resources/public/image/logo/bokaro_flat_logo_20140624_16x16.ico',false);?>">
		<link rel="stylesheet" async="true" href="<?php echo session()->path('resources/public/css/main.css',false);?>"/>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" type="text/css"/>
		<script src="<?php echo session()->path('resources/public/js/cookie.js',false);?>" async></script>
		<script src="<?php echo session()->path('resources/public/js/languages.js',false);?>" async></script>
		<!--<script src="<?php echo session()->path('resources/public/js/jquery.js',false);?>"></script>-->
		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<!--<script src="<?php echo session()->path('resources/public/js/bootstrap.js',false);?>"></script>-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
		<script>
		    $().ready(function(){$(".effect").click(function(){$(".container").fadeOut(600);});$( ".container" ).fadeIn(1000);});
		</script>
		<noscript>
		    <style>.container{display:block;}</style>
		</noscript>