<div class="text-center" data-bind="with: step_zero_visible">
<br/>
<h1>Install and Configure APIne Framework</h1>
<br/>
<p class="lead">This utility will help you install basic resources and configure your new APIne Application.</p>
<br/>
<button class="btn btn-lg btn-success" data-bind="click: $root.show_next_step">Get Started</button>
<p style="margin: 20px;margin-bottom:10px;padding:0px;">or</p>
<button class="btn btn-link" role="button">Import an existing config</button>
</div>

	<form data-bind="with: step_one_visible, submit: $root.validate_step_one">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Step 1 - Application Description</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="control-label">Name</label>
					<input class="form-control" type="text" placeholder="Application Name" data-bind="value: $root.app_name" required/>
				</div>
				<div class="form-group">
					<label class="control-label">Author</label>
					<input class="form-control" type="text" placeholder="Author Name" data-bind="value: $root.app_auth" required/>
					<span class="help-block">Name of the Author of the application</span>
				</div>
				<div class="form-group">
					<label class="control-label">Description</label>
					<textarea class="form-control" placeholder="Application Description" data-bind="value: $root.app_desc" required></textarea>
					<span class="help-block">Describe your application briefly</span>
				</div>
			</div>
			<div class="panel-footer text-right">
				<button class="btn btn-default" role="submit">Go To Step 2</button>
			</div>
		</div>
	</form>
	
	<form data-bind="with: step_two_visible, submit: $root.validate_step_two">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Step 2 - Database</h3>
			</div>
			<div data-bind="if: $root.invalid_step_two">
				<div class="alert alert-block alert-warning">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4><strong>Warning !</strong></h4>
					<span>It is impossible to connect to database. Please make sure the database server is running and try again.</span>
				</div>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="control-label">Database Type</label>
					<select class="form-control" disabled data-bind="value: $root.db_type">
						<option value="mysql" selected>Relational - MySQL</option>
					</select>
				</div>
				<div class="form-group">
					<label class="control-label">Database Charset</label>
					<input class="form-control" type="text" disabled data-bind="value: $root.db_char"/>
				</div>
				<div class="form-group">
					<label class="control-label">Host</label>
					<input class="form-control" type="text" placeholder="localhost" required data-bind="value: $root.db_host"/>
				</div>
				<div class="form-group">
					<label class="control-label">Database Name</label>
					<input class="form-control" type="text" required data-bind="value: $root.db_name" />
				</div>
				<div class="form-group">
					<label class="control-label">Database Username</label>
					<input class="form-control" type="text" required placeholder="root" data-bind="value: $root.db_user" />
				</div>
				<div class="form-group">
					<label class="control-label">Database Password</label>
					<input class="form-control" type="password" placeholder="*****" data-bind="value: $root.db_pass" />
				</div>
			</div>
			<div data-bind="if: $root.invalid_step_two">
				<div class="alert alert-block alert-warning">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4><strong>Warning !</strong></h4>
					<span>It is impossible to connect to database. Please make sure the database server is running and try again.</span>
				</div>
			</div>
			<div class="panel-footer text-right">
				<button class="btn btn-default" role="submit">Go To Step 3</button>
			</div>
		</div>
	</form>
	
	<form data-bind="with: step_three_visible, submit: $root.show_next_step">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Step 3 - Localization</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="control-label">Default Timezone</label>
					<select class="form-control" data-bind="value: $root.loc_time" required>
						<option selected disabled>Choose a Timezone</option>
					<?php foreach ($this->_params->get_item('timezones') as $name => $items) {?>
					<optgroup label="<?= $name ?>">
						<?php foreach ($items as $timezone => $city) {?>
						<option value="<?= $timezone ?>"><?= $city ?></option>
						<?php }?>
					</optgroup>
					<?php }?>
					</select>
					<span class="help-block">This will be the timezone used when it is impossible to locate the visitor</span>
				</div>
				<div class="form-group">
					<label class="control-label">Default Language and Locale</label>
					<select class="form-control" data-bind="value: $root.loc_lang" required>
						<option selected disabled>Choose a Language</option>
						<?php foreach ($this->_params->get_item('locales') as $name => $item) {?>
						<option value="<?= $name ?>"><?= $item ?></option>
						<?php }?>
					</select>
				</div>
			</div>
			<div class="panel-footer text-right">
				<button class="btn btn-default" role="submit">Go To Step 4</button>
			</div>
		</div>
	</form>
	
	<form data-bind="with: step_four_visible, submit: $root.show_next_step">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Step 4 - Email Client Setup</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="control-label">Configure access to an SMTP Server?</label>
					<select class="form-control" data-bind="value: $root.email" required>
						<option value="1">Yes</option>
						<option value="0" selected>No</option>
					</select>
				</div>
				<div data-bind="if: $root.email() == 1">
					<div class="form-group">
						<label class="control-label">Server Address</label>
						<input class="form-control" type="text" placeholder="Host"/>
					</div>
					<div class="form-group">
						<label class="control-label">Port</label>
						<input class="form-control" type="text" placeholder="Port"/>
					</div>
					<div class="form-group">
						<label class="control-label">Protocol</label>
						<input class="form-control" type="text" placeholder="Protocol"/>
					</div>
					<div class="form-group">
						<label class="control-label">Is SMTP Authentication Required?</label>
						<select class="form-control" data-bind="value: $root.email_auth">
							<option value="1">Yes</option>
							<option value="0" selected>No</option>
						</select>
					</div>
					<div data-bind="if: $root.email_auth_bool">
						<hr>
						<div class="form-group">
							<label class="control-label">SMTP Username</label>
							<input class="form-control" type="text" placeholder="Username"/>
						</div>
						<div class="form-group">
							<label class="control-label">SMTP Password</label>
							<input class="form-control" type="password" placeholder="Password"/>
						</div>
						<hr>
					</div>
					<div class="form-group">
						<label class="control-label">Sender Name</label>
						<input class="form-control" type="text" placeholder="Name"/>
					</div>
					<div class="form-group">
						<label class="control-label">Sender Address</label>
						<input class="form-control" type="email" placeholder="Email Address"/>
					</div>
				</div>
			</div>
			<div class="panel-footer text-right">
				<button class="btn btn-default" role="submit">Go To Step 5</button>
			</div>
		</div>
	</form>
	
	<div data-bind="with: step_five_visible">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Step 5 - Confirmation</h3>
			</div>
			<div class="panel-body">
				<p class="lead">The following settings will be applied to your new application.</p>
				<h4>Application :</h4>
				<dl class="dl-horizontal">
					<dt>Name</dt>
					<dd data-bind="text: $root.app_name"></dd>
					<dt>Author</dt>
					<dd data-bind="text: $root.app_auth"></dd>
					<dt>Description</dt>
					<dd data-bind="text: $root.app_desc"></dd>
				</dl>
				<h4>Database :</h4>
				<dl class="dl-horizontal">
					<dt>Host</dt>
					<dd data-bind="text: $root.db_host"></dd>
					<dt>Database Type</dt>
					<dd data-bind="text: $root.db_type"></dd>
					<dt>Database name</dt>
					<dd data-bind="text: $root.db_name"></dd>
					<dt>Database Charset</dt>
					<dd data-bind="text: $root.db_char"></dd>
					<dt>Username</dt>
					<dd data-bind="text: $root.db_user"></dd>
					<dt>Password</dt>
					<dd>*****</dd>
				</dl>
				<h4>Localization :</h4>
				<dl class="dl-horizontal">
					<dt>Default Timezone</dt>
					<dd data-bind="text: $root.loc_time"></dd>
					<dt>Default Locale</dt>
					<dd data-bind="text: $root.loc_lang"></dd>
				</dl>
				<div data-bind="if: $root.email() == 1">
					<h4>Email Server :</h4>
					<dl class="dl-horizontal">
						<dt>Host</dt>
						<dd data-bind="text: $root.email_host"></dd>
						<dt>Port</dt>
						<dd data-bind="text: $root.email_port"></dd>
						<dt>Protocol</dt>
						<dd data-bind="text: $root.email_prot"></dd>
						<dt>SMTP Authentication</dt>
						<dd data-bind="text: $root.email_auth_text"></dd>
						<div data-bind="if: $root.email_auth_bool">
							<dt>SMTP Username</dt>
							<dd data-bind="text: $root.email_user"></dd>
							<dt>SMTP Password</dt>
							<dd>*****</dd>
						</div>
						<dt>Sender Name</dt>
						<dd data-bind="text: $root.email_name"></dd>
						<dt>Sender Address</dt>
						<dd data-bind="text: $root.email_addr"></dd>
					</dl>
				</div>
				<p class="lead">The following folders and files will be created if they do not exist already :</p>
				<blockquote>The <code>views/</code> folder will be populated with basic views for session managements.</blockquote>
				<blockquote>Make sure www-data user has writting permissions on the project's directory.</blockquote>
				<ul>
					<li>controllers/</li>
					<li>resources/
						<ul>
							<li>languages/</li>
							<li>public/
								<ul>
									<li>assets/</li>
									<li>css/</li>
									<li>scripts</li>
								</ul>
							</li>
						</ul>
					</li>
					<li>views/</li>
					<li>.htaccess</li>
					<li>config.ini</li>
					<li>routes.json</li>
				</ul>
			</div>
			<div class="panel-footer text-right">
				<button class="btn btn-default" role="button" data-bind="click: $root.apply_settings">Apply configuration & Install</button>
			</div>
		</div>
	</div>
