<h1>How to use the APIne RESTful API</h1>
<p>As part of the APIne core, the RESTful API is fully integrated with the framework core so you can develop for the API the same way you'd develop for the web app using the very same interfaces with very low learning curve. APIne Framework is distributed with already functionning user authentication for the API.</p>
<p>The API is currently developped to be a RESTful API of level 2 according to the Richardson Maturity Model. It means the API should use efficiently the protocol attributes in order to deal with scalability and failures. Per example, do not use the default response code <a href="http://httpstatus.es/200"><code>200</code><span class="glyphicon glyphicon-new-window"></span></a> (OK) if something goes wrong.</p>
<p>Please refer to the <a href="https://github.com/Youmy001/apine_framework/wiki">APIne Wiki</a> for instructions on <a href="https://github.com/Youmy001/apine_framework/wiki">how to get started with APIne Framework</a> and on <a>how to create resources for the API</a>.</p>
<hr>
<p>Available resources for user authentication on the API:</p>
<ul>
	<li>/auth
		<ul>
			<li>POST : User registration. Requires POST arguments : <code>username</code>, <code>email</code>, <code>password</code> and <code>password_confirm</code>. For security, passwords should be encoded in base64. </li>
			<li>DELETE : User logout. Works only when a user is logged in.</li>
		</ul>
	</li>
	<li>/auth/<code>username</code>/<code>base64_password</code>
		<ul>
			<li>GET : User login</li>
		</ul>
	</li>
</ul>
<hr/>
<p>There's currently no other resources available.</p>
<hr/>
<h3>How to perpetuate a login</h3>
<p>Some operation may require to be logged in. In APIne Framework, this is done by providing authentication credentials within the request headers.</p>
<ul>
	<li>First, you need to login in order to fetch login credentials using the following request :
	<br/>
	<pre>GET /auth/&lt;username&gt;/&lt;base64_password&gt;</pre>
	This should return on success a JSON array containing the username, the api token and the expiration delay of the token. The array should look that way :<br/>
	<pre>{
    "username": "&lt;username&gt;",
    "token": "&lt;token&gt;",
    "origin": "&lt;origin&gt;",
    "expiration": "600"
}</pre></li>
	<li>Then, you need to add these credentials to any subsequent request until the delay since the previous request is higher than the expiration delay. If the time since the previous request is higher than the expiration delay, you'll need to fetch new credentials.<br/><br/>
	To add the credentials, you need to add a "Authorization" header in the following fashion :<br/>
	<pre>Authorization: &lt;username&gt;:&lt;token&gt;</pre>
	</li>
</ul>