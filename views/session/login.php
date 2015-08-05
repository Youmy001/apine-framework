
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Login</h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong>Warning !</strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?php echo URL_Helper::path("login",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label">Username or Email</label>
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
						<button type="submit" class="btn btn-primary pull-right">Login</button>
					</div>
					<a href="<?php echo URL_Helper::path("login/restore");?>">Forgot your password?</a>
				</form>
			</div>
		</div>

	</div>