<!DOCTYPE html>
<html lang="<?= apine_app_translator()->language()->code_short;?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="<?= apine_app_config()->get('application', 'description') ?>">
	<meta name="author" content="<?= apine_app_config()->get('application', 'author') ?>">
	<link rel="icon" href="<?php apine_url_helper()->resource('resources/public/assets/favicon.ico');?>">
	
	<title><?= apine_app_config()->get('application', 'title').' - '.$this->_title ?></title>

	<!-- Bootstrap core CSS -->
	<link href="<?= apine_url_helper()->resource('vendor/twbs/bootstrap/dist/css/bootstrap.min.css'); ?>" rel="stylesheet" />

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
				<a class="navbar-brand" href="<?= apine_url_helper()->path('home', APINE_PROTOCOL_HTTP) ?>"><?= apine_app_config()->get('application', 'title') ?></a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="<?= apine_url_helper()->path('home', APINE_PROTOCOL_HTTP) ?>"><?= apine_app_translator()->translate('menu','home') ?></a></li>
					<li><a href="<?= apine_url_helper()->path('about', APINE_PROTOCOL_HTTP) ?>"><?= apine_app_translator()->translate('menu','about') ?></a></li>
					<li><a href="https://github.com/Youmy001/apine_framework/issues"><?= apine_app_translator()->translate('menu','contact') ?></a></li>
				</ul>
				<?php if(!apine_session()->is_logged_in()){?>
				<ul class="nav navbar-nav navbar-right">
					<?php if (apine_application()->get_secure_session()) {?>
					<li><a href="<?= apine_url_helper()->path('login', APINE_PROTOCOL_HTTPS);?>"><span class="glyphicon glyphicon-log-in"></span> <?= apine_app_translator()->translate('menu','login') ?></a></li>
					<li><a href="<?= apine_url_helper()->path('register', APINE_PROTOCOL_HTTPS);?>"><span class="glyphicon glyphicon-check"></span> <?= apine_app_translator()->translate('menu','register') ?></a></li>
					<?php } else {?>
					<li><a href="<?= apine_url_helper()->path('login');?>"><span class="glyphicon glyphicon-log-in"></span> <?= apine_app_translator()->translate('menu','login') ?></a></li>
					<li><a href="<?= apine_url_helper()->path('register');?>"><span class="glyphicon glyphicon-check"></span> <?= apine_app_translator()->translate('menu','register') ?></a></li>
					<?php }?>
				</ul>
				<?php }else{?>
				<ul class="nav navbar-nav navbar-right">
					<li class="navbar-text"><?= apine_app_translator()->translate('menu','signed_in') ?> <?= apine_session()->get_user()->get_username() ?> (<i><?= ((int)apine_session()->get_user()->has_group(2))?'Admin':'User' ?></i>)</li>
					<li><a href="<?= apine_url_helper()->path('logout') ?>"><span class="glyphicon glyphicon-log-out"></span> <?= apine_app_translator()->translate('menu','logout') ?></a></li>
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
    	<p class="pull-left">&copy; 2016 <?= apine_app_config()->get('application', 'author'); ?></p>
    	<p class="pull-right text-right"><?= apine_app_translator()->translate('menu','generation') . apine_execution_time() . apine_app_translator()->translate('menu','milliseconds') ?></p>
    	<p class="text-center">APIne Framework&nbsp;<br class="visible-xs">ver. <?= apine_application()->get_version() ?></p>
    </footer>
	<!-- /.container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script
		src="<?= apine_url_helper()->resource('vendor/components/jquery/jquery.min.js'); ?>"></script>
	<script src="<?= apine_url_helper()->resource('vendor/twbs/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
</body>
</html>