<!DOCTYPE html>
<html lang="<?= apine_app_translator()->language()->code_short;?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="<?= apine_app_config()->get('application', 'description') ?>">
	<meta name="author" content="<?= apine_app_config()->get('application', 'author') ?>">
	
	<title><?= apine_app_config()->get('application', 'title').' - '.$this->_title ?></title>

	<!-- Bootstrap core CSS -->
	<link href="<?= apine_url_helper()->resource('vendor/twbs/bootstrap/dist/css/bootstrap.min.css'); ?>" rel="stylesheet" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
	<div class="container">

    	<?php include_once("$this->_view.php");?>

    </div>
    <footer class="container">
    	<hr>
    	<p class="pull-left">&copy; 2016 <?= apine_app_config()->get('application', 'author'); ?></p>
    	<p class="pull-right text-right">Generated in <?= apine_execution_time() ?> milliseconds</p>
    	<p class="text-center">APIne Framework&nbsp;<br class="visible-xs">ver. <?= apine_application()->get_version()->framework() ?></p>
    </footer>
	<!-- /.container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?= apine_url_helper()->resource('vendor/components/jquery/jquery.min.js'); ?>"></script>
	<script src="<?= apine_url_helper()->resource('vendor/twbs/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
</body>
</html>