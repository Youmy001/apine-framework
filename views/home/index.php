<h1><?= ApineAppTranslator::translate('home','title') ?></h1>
<p class="lead"><?= ApineAppTranslator::translate('home','description') ?></p>
<p><?= ApineAppTranslator::translate('home','small_one') ?><br><?= ApineAppTranslator::translate('home','small_two') ?></p>

<?php if(ApineSession::is_logged_in()){
	$locale = ApineAppTranslator::translation()->get_locale();
	$date = $locale->format_date(ApineSession::get_user()->get_register_date(), $locale->datehour());
	print "<p class=\"text-right\">".ucfirst(ApineSession::get_user()->get_username())." subscribed on : ". $date ."</p>";
}?>