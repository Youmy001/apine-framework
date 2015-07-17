
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Sign Up</h3>
			</div>
			<div class="panel-body">
				<form id="content" action="<?php echo URL_Helper::path("register",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label">Username</label>
						<input class="form-control" type="text" name="user" placeholder="Username" required />
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input class="form-control" type="password" name="pwd" placeholder="Password" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Enter a password with at least one of each (Uppercase, Lowercase and Numeral) and having at least 8 characters" required />
					</div>
					<div class="form-group">
						<label class="control-label">Confirm Password</label>
						<input class="form-control" type="password" name="pwd_confirm" placeholder="Password" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Enter a password with at least one of each (Uppercase, Lowercase and Numeral) and having at least 8 characters" required />
					</div>
					<div class="form-group">
						<label class="control-label">Email Address</label>
						<input class="form-control" type="email" name="email" placeholder="example@emxample.com"/>
					</div>
					<button type="submit" class="btn btn-primary pull-right">Login</button>
				</form>
			</div>
		</div>
	</div>