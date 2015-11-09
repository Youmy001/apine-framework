<h1>How to use the APIne RESTful API</h1>
<p>As part of the APIne core, the RESTful API is fully integrated with the framework core so you can develop for the API the same way you'd develop for the web app using the very same interfaces with very low learning curve. APIne Framework is distributed with already functionning user authentication for the API.</p>
<p>The API is currently developped to be a RESTful API of level 2 according to the Richardson Maturity Model. It means the API should use efficiently the protocol attributes in order to deal with scalability and failures. Per example, do not use the default response code <a href="http://httpstatus.es/200"><code>200</code><span class="glyphicon glyphicon-new-window"></span></a> (OK) if something goes wrong.</p>
<p>Please refer to the <a href="https://github.com/Youmy001/apine_framework/wiki">APIne Wiki</a> for instructions on <a href="https://github.com/Youmy001/apine_framework/wiki">how to get started with APIne Framework</a> and on <a>how to create resources for the API</a>.</p>
<hr>
<p>Available resources for user authentication on the API:</p>
<ul>
	<li>/auth
		<ul>
			<li>POST : User registration. Requires a POST arguments : <code>username</code>, <code>email</code>, <code>password</code> and <code>password_again</code>. For security, passwords should be encoded in base64. </li>
		</ul>
	</li>
	<li>/auth/<code>username</code>
		<ul>
			<li>DELETE : User logout. Works only when a user is logged in.</li>
		</ul>
	</li>
	<li>/auth/<code>username</code>/<code>base64_password</code>
		<ul>
			<li>GET : User login</li>
		</ul>
	</li>
</ul>
<hr>
<p>There's currently no other resources available.</p>