<h1>About APIne Framework</h1>
<p>APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and DAL abstraction without including useless tools.</p>
<p>The most notable features include a conplete session manager (login, logout, registration and password restoration) with basic users and permissions and a database abstraction layer that prevents you to write every queries.</p>

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
	<li>Setup a virtual host for the project directory that allow rewrite rules and has filter module enabled in apache for version 2.4 or greater.</li>
	<li>Import `resources/apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.</li>
	<li>Edit the `Database` section in `config.conf` to include connection to your database. Check the <a href="https://github.com/Youmy001/apine_framework/wiki">wiki</a> for more informations on configuration.</li> 
	<li>Open your browser and go to your virtual host address. APIne Framework is now ready to work.</li>
</ol>

<h2>Framework Documentation</h2>
<ul>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Controllers">How to Write Controllers</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/views">How to Write Views</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Routes">Advanced Routing with Routes</a></li>
	<li><a href="https://github.com/Youmy001/apine_framework/wiki/Entity-Model">How to deal with Entity Model</a></li>
	<li><a href="https://github/Youmy001/apine_framework/wiki/Entity-Lazy-Loading">Manage Lazy Loading on User Defined Entity Models</a></li>
</ul>