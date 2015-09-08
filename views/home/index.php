<h1><?= ApineTranslator::translate('home','title') ?></h1>
<p class="lead"><?= ApineTranslator::translate('home','description') ?></p>
<p><?= ApineTranslator::translate('home','small_one') ?><br><?= ApineTranslator::translate('home','small_two') ?></p>

<?php if(ApineSession::is_logged_in()){
	print "<p class=\"text-right\">".ucfirst(ApineSession::get_user()->get_username())." subscribed on : ".date(Config::get('dateformat', 'datehour'),strtotime(ApineSession::get_user()->get_register_date()))."</p>";
}?>