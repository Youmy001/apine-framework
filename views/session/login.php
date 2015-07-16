
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Login</h3>
			</div>
			<div class="panel-body">
				<form id="content" action="<?php echo URL_Helper::path("login",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label">Username</label>
						<input class="form-control" type="text" name="user"
								placeholder="Username" required />
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input class="form-control" type="password" name="pwd"
								placeholder="Password" required />
					</div>
					<div class="checkout">
						<label>
							<input type="checkbox" name="perm" value="on"> Remember me
						</label>
					</div>
					<a href="<?php echo URL_Helper::path("login/restore");?>">Forgot your password?</a>
					<button type="submit" class="btn btn-primary pull-right">Login</button>
				</form>
			</div>
		</div>

	</div>