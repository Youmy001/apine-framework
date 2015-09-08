<!DOCTYPE html>
<html lang="<?= ApineTranslator::language()->code_short;?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="<?= Config::get('application', 'description') ?>">
	<meta name="author" content="<?= Config::get('application', 'author') ?>">
	<link rel="icon" href="../../favicon.ico">

	<title><?= Config::get('application', 'title').' - '.$this->_title ?></title>

	<!-- Bootstrap core CSS -->
	<link href="<?= URL_Helper::path('resources/public/css/bootstrap.min.css',false); ?>" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<style>
		body {
			padding-top: 70px;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#navbar" aria-expanded="false"
					aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?= URL_Helper::path('home',true) ?>"><?= Config::get('application', 'title') ?></a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="<?= URL_Helper::path('home',true) ?>"><?= ApineTranslator::translate('menu','home') ?></a></li>
					<li><a href="<?= URL_Helper::path('about',true) ?>"><?= ApineTranslator::translate('menu','about') ?></a></li>
					<li><a href="<?= URL_Helper::path('contact',true) ?>"><?= ApineTranslator::translate('menu','contact') ?></a></li>
				</ul>
				<?php if(!ApineSession::is_logged_in()){?>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?= URL_Helper::path('login',true);?>"><?= ApineTranslator::translate('menu','login') ?></a></li>
					<li><a href="<?= URL_Helper::path('register',true);?>"><?= ApineTranslator::translate('menu','register') ?></a></li>
				</ul>
				<?php }else{?>
				<ul class="nav navbar-nav navbar-right">
					<p class="navbar-text"><?= ApineTranslator::translate('menu','signed_in') ?> <?= ApineSession::get_user()->get_username() ?> (<i><?= ((int)ApineSession::get_session_type()===SESSION_ADMIN)?'Admin':'User' ?></i>)</p>
					<li><a href="<?= URL_Helper::path('logout',true) ?>"><?= ApineTranslator::translate('menu','logout') ?></a></li>
				</ul>
				<?php } ?>
			</div>
			<!--/.nav-collapse -->
		</div>
	</nav>

	<div class="container">

    	<?php include_once("views/$this->_view.php");?>

    </div>
    <footer class="container">
    	<hr>
    	<p class="pull-left">&copy; 2015 <?= Config::get('application', 'author'); ?></p>
    	<p class="pull-right"><?= ApineTranslator::translate('menu','generation').execution_time().ApineTranslator::translate('menu','milliseconds') ?></p>
    	<p class="text-center">APIne Framework&nbsp;<br class="visible-xs">ver. <?= Version::framework() ?></p>
    </footer>
	<!-- /.container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script
		src="<?= URL_Helper::path('resources/public/scripts/jquery.min.js',false); ?>"></script>
	<script src="<?= URL_Helper::path('resources/public/scripts/bootstrap.min.js',false); ?>"></script>
</body>
</html>