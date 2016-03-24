<h1><?= apine_app_translator()->translate('home','title') ?></h1>
<p class="lead"><?= apine_app_translator()->translate('home','description') ?></p>
<p><?= apine_app_translator()->translate('home','small_one') ?><br><?= apine_app_translator()->translate('home','small_two') ?></p>

<?php if(apine_session()->is_logged_in()){
	$locale = apine_app_translator()->translation()->get_locale();
	$date = $locale->format_date(apine_session()->get_user()->get_register_date(), $locale->datehour());
	print "<p class=\"text-right\">".ucfirst(apine_session()->get_user()->get_username())." subscribed on : ". $date ."</p>";
}?>
