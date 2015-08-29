<h1>APIne Framework</h1>
<p class="lead">APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication and DAL abstraction without including useless tools.</p>
<p>Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>

<?php if(ApineSession::is_logged_in()){
	print "<p class=\"text-right\">".ucfirst(ApineSession::get_user()->get_username())." subscribed on : ".date(Config::get('dateformat', 'datehour'),ApineSession::get_user()->get_register_date())."</p>";
}?>