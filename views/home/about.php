<h1>About APIne Framework</h1>
<p>APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and database abstraction without including useless tools.</p>
<p>The most notable features include a complete session manager (login, logout, registration and password restoration) with basic users and permissions and a database abstraction layer that prevents you to write every queries.</p>

<h2>Requirements</h2>
<ul>
	<li>PHP 5.4.0 or greater</li>
	<li>MySQL 5</li>
	<li>Apache 2.4</li>
	<li>mod_rewrite</li>
	<li>filter_module</li>
</ul>

<p>The project must be set in a virtual host that allows rewrites for the routes to work.</p>

<h2>Installation</h2>
<ol>
	<li>Clone this project in your working directory<br> 
	<code>$ git clone https://github.com/Youmy001/apine_framework.git</code></li>
	<li>Setup a virtual host for the project directory that allow rewrite rules and optionaly has mod_deflate and filter_module enabled in apache for version 2.4 or greater.</li>
	<li>Import `apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.</li>
	<li>Create a copy of `sample_config.ini` named `config.ini`.</li>
	<li>Edit the `Database` section in `config.ini` to include connection to your database. Check the <a href="https://github.com/Youmy001/apine_framework/wiki">wiki</a> for more informations on configuration.</li>
	<li>Install composer depandancies<br>
	<code>$ php composer.phar install</code></li> 
	<li>Open your browser and go to your virtual host address. APIne Framework is now ready to work.</li>
</ol>

<h2>Framework Documentation</h2>
<ul>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Database-Connection">Database Connection</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Routes">Advanced Routing</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Controllers">MVC Controllers</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/views">MVC Views</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Entity-Model">Entity Model</a></li>
	<li><a href="https://github/Youmy001/apine_framework/wiki/Entity-Lazy-Loading">Lazy Loading on Entity Models</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Entity-Factories">Entity Factories</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Extended-Users">Extend Users</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Translations-and-locales">Translations &amp; Locales</a></li>
</ul>

<h2>Migration from RC1 (1.0.0-dev.8.6)</h2>
<p>Since RC1, there was a lot of modifications uncompatible with older versions. We recommend to users of older than 1.0.0-dev.11.0 (RC2 release was 1.0.0-dev.11.8) to simply reinstall APIne Framework. The support for the older encryption method was dropped with version 1.0.0-dev.11.0.</p>

<h3>New features :</h3>
<ul>
	<li>Extensible users;</li>
	<li>Improved encryption and hashing methods;</li>
	<li>Exception Handling;</li>
	<li>Improved Session Handling;</li>
	<li>Improved Routing;</li>
	<li>Basic RESTful API (Login &amp; Logout);</li>
	<li>Composer Integration;</li>
</ul>

<h3>Renamed Components :</h3>
<p>Former names for these components are now deprecated.</p>
<ul>
	<li>Autoload became ApineAutoload;</li>
	<li>Liste became ApineCollection;</li>
	<li>Controller became ApineController;</li>
	<li>Config became ApineConfig;</li>
	<li>Cookie became ApineCookie;</li>
	<li>Database became ApineDatabase;</li>
	<li>Encryption became ApineEncryption;</li>
	<li>Request became ApineRequest;</li>
	<li>ApineTranslator became ApineAppTranslator;</li>
	<li>Translation became ApineTranslation;</li>
	<li>TranslationLanguage became ApineTranslationLanguage;</li>
	<li>TranslationLocale became ApineTranslationLocale;</li>
	<li>Translator became ApineTranslator;</li>
	<li>URL_Helper became ApineURLHelper;</li>
	<li>Version became ApineVersion;</li>
	<li>View became ApineView;</li>
	<li>HTMLView became ApineHTMLView;</li>
	<li>FileView became ApineFileView;</li>
	<li>JSONView became ApineJSONView;</li>
</ul>
